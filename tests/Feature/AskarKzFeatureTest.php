<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class AskarKzFeatureTest extends TestCase
{
    public function test_askar_kz_showcase_is_available(): void
    {
        $this->get('/askar_kz')
            ->assertOk()
            ->assertSee('AASKAR')
            ->assertSee('Craft Chronicles')
            ->assertSee('https://www.twitch.tv/aaskar', false)
            ->assertSee('Асқарбиновая Орда');
    }
}
