<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ChessGame;
use App\Models\ChessProfile;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PChess\Chess\Board;
use Tests\TestCase;

class ChessFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_guest_can_create_session_profile_and_game(): void
    {
        $this->post('/chess/profile', ['name' => 'Knight Rider'])->assertRedirect();
        $profile = ChessProfile::query()->firstOrFail();

        $this->assertSame($profile->id, session('chess_profile_id'));
        $this->post('/chess/games')->assertRedirect();
        $this->assertDatabaseHas('chess_games', ['white_profile_id' => $profile->id, 'status' => 'waiting']);
    }

    public function test_second_session_can_join_and_players_can_make_legal_moves(): void
    {
        $white = ChessProfile::query()->create(['name' => 'White']);
        $black = ChessProfile::query()->create(['name' => 'Black']);
        $game = ChessGame::query()->create(['white_profile_id' => $white->id, 'fen' => Board::DEFAULT_POSITION, 'moves' => []]);

        $this->withSession(['chess_profile_id' => $black->id])
            ->postJson(route('chess.join', $game), ['color' => 'black'])
            ->assertOk()
            ->assertJsonPath('status', 'playing');

        $this->withSession(['chess_profile_id' => $white->id])
            ->postJson(route('chess.move', $game), ['from' => 'e2', 'to' => 'e4'])
            ->assertOk()
            ->assertJsonPath('moves.0', 'e4');

        $this->withSession(['chess_profile_id' => $white->id])
            ->postJson(route('chess.move', $game), ['from' => 'd2', 'to' => 'd4'])
            ->assertForbidden();
    }

    public function test_observer_can_read_state_but_cannot_move(): void
    {
        $white = ChessProfile::query()->create(['name' => 'White']);
        $black = ChessProfile::query()->create(['name' => 'Black']);
        $observer = ChessProfile::query()->create(['name' => 'Observer']);
        $game = ChessGame::query()->create([
            'white_profile_id' => $white->id, 'black_profile_id' => $black->id,
            'fen' => Board::DEFAULT_POSITION, 'status' => 'playing', 'moves' => [],
        ]);

        $this->withSession(['chess_profile_id' => $observer->id])
            ->getJson(route('chess.state', $game))
            ->assertOk()
            ->assertJsonPath('myColor', null);

        $this->withSession(['chess_profile_id' => $observer->id])
            ->postJson(route('chess.move', $game), ['from' => 'e2', 'to' => 'e4'])
            ->assertForbidden();
    }
}
