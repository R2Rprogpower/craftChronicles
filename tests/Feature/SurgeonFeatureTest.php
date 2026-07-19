<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class SurgeonFeatureTest extends TestCase
{
    public function test_surgeon_game_is_available(): void
    {
        $this->get('/surgeon')
            ->assertOk()
            ->assertSee('SURGEON')
            ->assertSee('УРОВЕНЬ 1')
            ->assertSee('УРОВЕНЬ 2');
    }
}
