<?php

declare(strict_types=1);

namespace App\Modules\MovieTierList\Http\Middleware;

use App\Modules\MovieTierList\Services\MovieTierListEditorAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMovieTierListEditorIp
{
    public function __construct(private readonly MovieTierListEditorAccess $access) {}

    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($this->access->canEdit($request), Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}
