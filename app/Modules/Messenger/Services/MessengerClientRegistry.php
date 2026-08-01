<?php

declare(strict_types=1);

namespace App\Modules\Messenger\Services;

use App\Modules\Messenger\Contracts\MessengerClientInterface;
use InvalidArgumentException;

class MessengerClientRegistry
{
    /**
     * @param  array<string, MessengerClientInterface>  $clients
     */
    public function __construct(
        private readonly array $clients
    ) {}

    public function forDriver(string $driver): MessengerClientInterface
    {
        if (! isset($this->clients[$driver])) {
            throw new InvalidArgumentException("Messenger driver '{$driver}' is not configured.");
        }

        return $this->clients[$driver];
    }
}
