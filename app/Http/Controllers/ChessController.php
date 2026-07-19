<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChessGame;
use App\Models\ChessProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use PChess\Chess\Board;
use PChess\Chess\Chess;

class ChessController extends Controller
{
    public function index(Request $request): View
    {
        return view('chess.index', [
            'profile' => $this->profile($request),
            'games' => ChessGame::query()->with(['white', 'black'])->latest()->limit(30)->get(),
        ]);
    }

    public function profileUpdate(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'min:2', 'max:30']]);
        $profile = $this->profile($request) ?? ChessProfile::query()->create($data);
        $profile->update($data);
        $request->session()->put('chess_profile_id', $profile->id);

        return back();
    }

    public function create(Request $request): RedirectResponse
    {
        $profile = $this->requireProfile($request);
        $game = ChessGame::query()->create([
            'white_profile_id' => $profile->id,
            'fen' => Board::DEFAULT_POSITION,
            'moves' => [],
        ]);

        return redirect()->route('chess.game', $game);
    }

    public function show(Request $request, ChessGame $game): View
    {
        return view('chess.game', ['game' => $game->load(['white', 'black']), 'profile' => $this->profile($request)]);
    }

    public function state(Request $request, ChessGame $game): JsonResponse
    {
        $game->load(['white', 'black']);
        $profile = $this->profile($request);

        return response()->json($this->gameData($game, $profile));
    }

    public function join(Request $request, ChessGame $game): JsonResponse
    {
        $profile = $this->requireProfile($request);
        $data = $request->validate(['color' => ['required', Rule::in(['white', 'black'])]]);

        DB::transaction(function () use ($game, $profile, $data): void {
            /** @var ChessGame $locked */
            $locked = ChessGame::query()->whereKey($game->id)->lockForUpdate()->firstOrFail();
            $column = $data['color'].'_profile_id';
            abort_if($locked->{$column} !== null && $locked->{$column} !== $profile->id, 409, 'That seat is already taken.');
            abort_if(in_array($profile->id, [$locked->white_profile_id, $locked->black_profile_id], true) && $locked->{$column} !== $profile->id, 409, 'You already occupy the other seat.');
            $locked->{$column} = $profile->id;
            $locked->status = $locked->white_profile_id && $locked->black_profile_id ? 'playing' : 'waiting';
            $locked->save();
        });

        return $this->state($request, $game->fresh());
    }

    public function move(Request $request, ChessGame $game): JsonResponse
    {
        $profile = $this->requireProfile($request);
        $data = $request->validate([
            'from' => ['required', 'regex:/^[a-h][1-8]$/'],
            'to' => ['required', 'regex:/^[a-h][1-8]$/'],
            'promotion' => ['nullable', Rule::in(['q', 'r', 'b', 'n'])],
        ]);

        DB::transaction(function () use ($game, $profile, $data): void {
            /** @var ChessGame $locked */
            $locked = ChessGame::query()->whereKey($game->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'playing', 409, 'The game is not active.');
            $chess = new Chess($locked->fen);
            $colorColumn = $chess->turn === 'w' ? 'white_profile_id' : 'black_profile_id';
            abort_unless($locked->{$colorColumn} === $profile->id, 403, 'It is not your turn.');
            $move = $chess->move($data);
            abort_if($move === null, 422, 'Illegal move.');

            $moves = $locked->moves;
            $moves[] = $move->san;
            $locked->fen = $chess->fen();
            $locked->moves = $moves;
            if ($chess->gameOver()) {
                $locked->status = 'finished';
                $locked->result = $chess->inCheckmate() ? ($chess->turn === 'w' ? 'black' : 'white') : 'draw';
            }
            $locked->save();
        });

        return $this->state($request, $game->fresh());
    }

    private function profile(Request $request): ?ChessProfile
    {
        $id = $request->session()->get('chess_profile_id');

        return $id ? ChessProfile::query()->find($id) : null;
    }

    private function requireProfile(Request $request): ChessProfile
    {
        return $this->profile($request) ?? abort(403, 'Create a chess profile first.');
    }

    /** @return array<string, mixed> */
    private function gameData(ChessGame $game, ?ChessProfile $profile): array
    {
        $game->loadMissing(['white', 'black']);

        return [
            'id' => $game->id, 'fen' => $game->fen, 'status' => $game->status, 'result' => $game->result,
            'moves' => $game->moves, 'white' => $game->white?->name, 'black' => $game->black?->name,
            'myColor' => match ($profile?->id) {
                $game->white_profile_id => 'white', $game->black_profile_id => 'black', default => null
            },
        ];
    }
}
