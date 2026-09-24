<?php

declare(strict_types=1);

namespace App\Modules\MovieTierList\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\MovieTierList\Http\Requests\UpdateMovieTierListRequest;
use App\Modules\MovieTierList\Services\MovieTierListContent;
use App\Modules\MovieTierList\Services\MovieTierListEditorAccess;
use App\Modules\MovieTierList\Services\MovieTierListService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class MovieTierListController extends Controller
{
    public function show(
        Request $request,
        MovieTierListService $service,
        MovieTierListContent $content,
        MovieTierListEditorAccess $access,
    ): View {
        $tierList = $service->get();

        return view('movie-tier-list', [
            'content' => $content->get(),
            'movies' => $tierList->payload['movies'] ?? [],
            'revision' => $tierList->revision,
            'canEdit' => $access->canEdit($request),
        ]);
    }

    public function update(UpdateMovieTierListRequest $request, MovieTierListService $service): JsonResponse
    {
        /** @var array{revision: int, movies: list<array<string, mixed>>} $data */
        $data = $request->validated();

        try {
            $tierList = $service->replace($data['movies'], $data['revision']);
        } catch (RuntimeException) {
            return response()->json([
                'message' => 'Список уже изменён в другой вкладке. Обнови страницу.',
            ], Response::HTTP_CONFLICT);
        }

        return response()->json([
            'message' => 'Сохранено',
            'revision' => $tierList->revision,
            'updated_at' => $tierList->updated_at?->toIso8601String(),
        ]);
    }
}
