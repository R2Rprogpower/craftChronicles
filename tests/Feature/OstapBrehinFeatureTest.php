<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class OstapBrehinFeatureTest extends TestCase
{
    public function test_ostap_brehin_showcase_is_available(): void
    {
        $this->get('/OstapBrehin')
            ->assertOk()
            ->assertSee('Ostap Brehin')
            ->assertSee('OpenClaw')
            ->assertSee('Interactive demo');
    }
}
