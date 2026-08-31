<?php

declare(strict_types=1);

namespace App\Modules\Portfolio\Database\Seeders;

use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['title' => 'CraftChronicles', 'slug' => 'craft-chronicles', 'category' => 'software-project', 'status' => 'in-development', 'short_description' => 'A modular Laravel platform evolving into a personal brand, portfolio, content, and product operating system.', 'description' => 'A modular monolith with public product surfaces and a clean path toward internal editorial and progress tooling.', 'technologies' => ['Laravel 12', 'React', 'Vite', 'PHPStan', 'PHPUnit'], 'links' => [['label' => 'GitHub', 'url' => 'https://github.com/R2Rprogpower/craftChronicles']], 'timeline' => '2026 — ongoing', 'achievements' => ['Reusable module conventions', 'Configurable brand data', 'Public progress tracking'], 'featured' => true, 'sort_order' => 10],
            ['title' => 'Automation Control', 'slug' => 'automation-control', 'category' => 'experiment', 'status' => 'active', 'short_description' => 'A local-first Telegram control surface for running personal automations without spending AI tokens.', 'description' => 'A safe command router and operational layer for scheduled digests, reminders, and health checks.', 'technologies' => ['Node.js', 'Telegram Bot API', 'systemd', 'Local LLM'], 'links' => [], 'timeline' => '2026', 'achievements' => ['Allowlisted command execution', 'Structured automation status', 'Local model routing'], 'featured' => true, 'sort_order' => 20],
            ['title' => 'Vygotsky Knowledge Pipeline', 'slug' => 'vygotsky-knowledge-pipeline', 'category' => 'educational-project', 'status' => 'research', 'short_description' => 'A structured workflow for turning dense scientific material into traceable, accessible educational content.', 'description' => 'Research, extraction, claims tracking, synthesis, and editorial review built around source fidelity.', 'technologies' => ['Python', 'Structured prompts', 'Markdown', 'Document pipelines'], 'links' => [], 'timeline' => '2026 — ongoing', 'achievements' => ['Traceable claims registry', 'Multi-stage editorial workflow'], 'featured' => false, 'sort_order' => 30],
        ];

        foreach ($items as $item) {
            PortfolioItem::query()->updateOrCreate(['slug' => $item['slug']], $item + ['active' => true]);
        }
    }
}
