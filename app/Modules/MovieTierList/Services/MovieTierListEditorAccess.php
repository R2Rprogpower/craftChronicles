<?php

declare(strict_types=1);

namespace App\Modules\MovieTierList\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class MovieTierListEditorAccess
{
    public function canEdit(Request $request): bool
    {
        $clientIp = $request->ip();
        $allowedIps = config('movie-tier-list.editor_ips', []);

        if (! is_string($clientIp)) {
            return false;
        }

        if (is_array($allowedIps)) {
            foreach ($allowedIps as $allowedIp) {
                if (is_string($allowedIp) && $allowedIp !== '' && IpUtils::checkIp($clientIp, $allowedIp)) {
                    return true;
                }
            }
        }

        $clientIpHash = hash('sha256', $clientIp);
        $allowedIpHashes = config('movie-tier-list.editor_ip_hashes', []);

        if (is_array($allowedIpHashes)) {
            foreach ($allowedIpHashes as $allowedIpHash) {
                if (is_string($allowedIpHash) && hash_equals($allowedIpHash, $clientIpHash)) {
                    return true;
                }
            }
        }

        return false;
    }
}
