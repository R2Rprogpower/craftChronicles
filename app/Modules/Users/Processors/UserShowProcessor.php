<?php

declare(strict_types=1);

namespace App\Modules\Users\Processors;

use App\Core\Abstracts\Processor;
use App\Models\User;
use App\Modules\Users\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserShowProcessor extends Processor
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function execute(int $id): User
    {
        $user = $this->userService->findById($id);

        if (! $user) {
            throw (new ModelNotFoundException)->setModel(User::class, [$id]);
        }

        return $user;
    }
}
