<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class OpenClawResearchFeatureTest extends TestCase
{
    public function test_openclaw_research_case_explains_cited_library_search(): void
    {
        $this->get('/openclaw-research?lang=ru')
            ->assertOk()
            ->assertSee('Вся библиотека')
            ->assertSee('Миллионы страниц')
            ->assertSee('Ответы с доказательствами')
            ->assertSee('Научная добросовестность')
            ->assertSee(route('openclaw-short', ['lang' => 'ru']).'#request', false);
    }

    public function test_openclaw_pages_share_navigation_to_every_offer_and_presentation(): void
    {
        $destinations = [
            route('automation-landing', ['lang' => 'en']),
            route('openclaw-short', ['lang' => 'en']),
            route('openclaw-developer', ['lang' => 'en']),
            route('openclaw-research', ['lang' => 'en']),
            route('presentations.openclaw', ['lang' => 'en']),
        ];

        foreach (['/ai-automation', '/openclaw', '/openclaw-developer', '/openclaw-research', '/presentation/openclaw'] as $uri) {
            $response = $this->get($uri)->assertOk();

            foreach ($destinations as $destination) {
                $response->assertSee('href="'.$destination.'"', false);
            }
        }
    }

    public function test_english_is_default_and_every_page_supports_all_languages(): void
    {
        $expectations = [
            '/ai-automation' => ['Setting up OpenClaw', 'Настраиваю OpenClaw', 'Налаштовую OpenClaw'],
            '/openclaw' => ['You send a task', 'Вы пишете задачу', 'Ви пишете завдання'],
            '/openclaw-developer' => ['Typical site changes', 'Типовые изменения сайта', 'Типові зміни сайту'],
            '/openclaw-research' => ['The entire library', 'Вся библиотека', 'Вся бібліотека'],
            '/presentation/openclaw' => ['AI that not only responds', 'ИИ, который не только отвечает', 'ШІ, який не лише відповідає'],
        ];

        foreach ($expectations as $uri => [$english, $russian, $ukrainian]) {
            $this->get($uri)->assertOk()->assertSee($english)->assertSee('lang="en"', false);
            $this->get($uri.'?lang=ru')->assertOk()->assertSee($russian)->assertSee('lang="ru"', false);
            $this->get($uri.'?lang=uk')->assertOk()->assertSee($ukrainian)->assertSee('lang="uk"', false);
        }
    }
}
