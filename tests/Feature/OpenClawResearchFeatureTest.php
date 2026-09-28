<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class OpenClawResearchFeatureTest extends TestCase
{
    public function test_openclaw_research_case_explains_cited_library_search(): void
    {
        $this->get('/openclaw-research')
            ->assertOk()
            ->assertSee('Вся библиотека')
            ->assertSee('Миллионы страниц')
            ->assertSee('Ответы с доказательствами')
            ->assertSee('Научная добросовестность')
            ->assertSee(route('openclaw-short').'#request', false);
    }

    public function test_openclaw_pages_share_navigation_to_every_offer_and_presentation(): void
    {
        $destinations = [
            route('automation-landing'),
            route('openclaw-short'),
            route('openclaw-developer'),
            route('openclaw-research'),
            route('presentations.openclaw'),
        ];

        foreach (['/ai-automation', '/openclaw', '/openclaw-developer', '/openclaw-research', '/presentation/openclaw'] as $uri) {
            $response = $this->get($uri)->assertOk();

            foreach ($destinations as $destination) {
                $response->assertSee('href="'.$destination.'"', false);
            }
        }
    }
}
