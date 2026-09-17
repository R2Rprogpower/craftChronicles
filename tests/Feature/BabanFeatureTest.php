<?php

namespace Tests\Feature;

use Tests\TestCase;

class BabanFeatureTest extends TestCase
{
    public function test_baban_tier_list_page_is_available(): void
    {
        $this->get('/baban')
            ->assertOk()
            ->assertSee('BABANGIDA — ULTIMATE TIER LIST', false)
            ->assertSee('babangida-tierlist-v2', false)
            ->assertSee('Импорт JSON', false);
    }
}
