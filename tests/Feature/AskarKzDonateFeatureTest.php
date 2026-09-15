<?php

namespace Tests\Feature;

use Tests\TestCase;

class AskarKzDonateFeatureTest extends TestCase
{
    public function test_donation_hub_is_available_with_all_payment_links(): void
    {
        $this->get('/askar_kz_1')
            ->assertOk()
            ->assertSee('StreamLabs')
            ->assertSee('https://streamlabs.com/askarick', false)
            ->assertSee('https://donatello.to/askar', false)
            ->assertSee('https://t.me/tribute/app?startapp=d913', false)
            ->assertSee('https://t.me/tribute/app?startapp=d8ZT', false)
            ->assertSee('https://tourniquet.app/donate/Askar', false);
    }
}
