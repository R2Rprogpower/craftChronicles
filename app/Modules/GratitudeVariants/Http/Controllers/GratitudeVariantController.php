<?php

declare(strict_types=1);

namespace App\Modules\GratitudeVariants\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\GratitudeVariants\Http\Requests\StoreGratitudeVariantRequest;
use App\Modules\GratitudeVariants\Models\GratitudeVariant;
use App\Modules\MovieTierList\Services\MovieTierListEditorAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GratitudeVariantController extends Controller
{
    public function index(Request $request, MovieTierListEditorAccess $access): View
    {
        $variants = GratitudeVariant::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'title', 'language', 'body', 'tags']);

        return view('gratitude-variants', [
            'variants' => $variants,
            'canEdit' => $access->canEdit($request),
        ]);
    }

    public function store(StoreGratitudeVariantRequest $request): JsonResponse
    {
        $variant = GratitudeVariant::query()->create([
            ...$request->validated(),
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Вариант добавлен',
            'variant' => $variant->only(['id', 'title', 'language', 'body', 'tags']),
        ], Response::HTTP_CREATED);
    }

    public function update(
        StoreGratitudeVariantRequest $request,
        GratitudeVariant $gratitudeVariant,
    ): JsonResponse {
        $gratitudeVariant->update($request->validated());

        return response()->json([
            'message' => 'Вариант обновлён',
            'variant' => $gratitudeVariant->only(['id', 'title', 'language', 'body', 'tags']),
        ]);
    }

    public function destroy(GratitudeVariant $gratitudeVariant): JsonResponse
    {
        $gratitudeVariant->delete();

        return response()->json(['message' => 'Вариант удалён']);
    }
}
