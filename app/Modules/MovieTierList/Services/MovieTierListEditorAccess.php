<?php

declare(strict_types=1);

namespace App\Modules\MovieTierList\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class MovieTierListEditorAccess
{
    public function canEdit(Request $request): bool
    {
        $allowedIps = config('movie-tier-list.editor_ips', []);
        $allowedIpHashes = config('movie-tier-list.editor_ip_hashes', []);

        foreach ($this->candidateIps($request) as $clientIp) {
            if (is_array($allowedIps)) {
                foreach ($allowedIps as $allowedIp) {
                    if (is_string($allowedIp) && $allowedIp !== '' && IpUtils::checkIp($clientIp, $allowedIp)) {
                        return true;
                    }
                }
            }

            $clientIpHash = hash('sha256', $clientIp);

            if (is_array($allowedIpHashes)) {
                foreach ($allowedIpHashes as $allowedIpHash) {
                    if (is_string($allowedIpHash) && hash_equals($allowedIpHash, $clientIpHash)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /** @return list<string> */
    public function candidateIps(Request $request): array
    {
        $candidates = array_merge([$request->ip()], $request->ips());
        $normalized = [];

        foreach ($candidates as $candidate) {
            if (! is_string($candidate) || $candidate === '') {
                continue;
            }

            if (preg_match('/^::ffff:(\d{1,3}(?:\.\d{1,3}){3})$/i', $candidate, $matches) === 1) {
                $candidate = $matches[1];
            }

            if (filter_var($candidate, FILTER_VALIDATE_IP) !== false) {
                $normalized[] = $candidate;
            }
        }

        return array_values(array_unique($normalized));
    }
}
