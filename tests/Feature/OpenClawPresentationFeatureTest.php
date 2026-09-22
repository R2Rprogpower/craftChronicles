<?php

namespace Tests\Feature;

use Tests\TestCase;

class OpenClawPresentationFeatureTest extends TestCase
{
    public function test_openclaw_presentation_is_publicly_available(): void
    {
        $response = $this->get('/presentation/openclaw')
            ->assertOk()
            ->assertSee('OpenClaw')
            ->assertSee('ШІ, який не лише відповідає — а діє')
            ->assertSee('7-денний експеримент')
            ->assertSee('openclaw-first-years-ua.pptx', false);

        $this->assertSame(10, substr_count($response->getContent(), 'data-slide '));
        $this->assertFileExists(public_path('presentations/openclaw/openclaw-first-years-ua.pptx'));
    }
}
