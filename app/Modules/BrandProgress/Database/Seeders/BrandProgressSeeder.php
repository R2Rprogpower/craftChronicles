<?php

declare(strict_types=1);

namespace App\Modules\BrandProgress\Database\Seeders;

use App\Models\BrandProgressArea;
use Illuminate\Database\Seeder;

class BrandProgressSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['name' => 'Personal Brand', 'slug' => 'personal-brand', 'status' => 'building', 'progress' => 40, 'summary' => 'Positioning and the first public platform foundation.', 'goals' => ['Make the positioning clear', 'Create one coherent public home'], 'tasks' => ['Review public copy with real audience feedback'], 'next_steps' => ['Refine positioning after five conversations'], 'sort_order' => 10, 'milestones' => [['title' => 'Define initial positioning', 'status' => 'completed'], ['title' => 'Publish brand platform MVP', 'status' => 'in-progress']]],
            ['name' => 'Portfolio', 'slug' => 'portfolio', 'status' => 'building', 'progress' => 55, 'summary' => 'Turning existing work into specific, credible case studies.', 'goals' => ['Publish three strong cases'], 'tasks' => ['Add screenshots', 'Write outcome-focused case studies'], 'next_steps' => ['Complete the CraftChronicles case study'], 'sort_order' => 20, 'milestones' => [['title' => 'Create portfolio data model', 'status' => 'completed'], ['title' => 'Publish first three project summaries', 'status' => 'in-progress']]],
            ['name' => 'YouTube', 'slug' => 'youtube', 'status' => 'planning', 'progress' => 15, 'summary' => 'Building a repeatable video format around engineering judgment.', 'goals' => ['Publish the first three videos'], 'tasks' => ['Choose pilot topic', 'Create lightweight production checklist'], 'next_steps' => ['Draft and record the architecture-theatre pilot'], 'sort_order' => 30, 'milestones' => [['title' => 'Define channel themes', 'status' => 'in-progress']]],
            ['name' => 'Twitch', 'slug' => 'twitch', 'status' => 'planning', 'progress' => 10, 'summary' => 'Testing focused live sessions instead of generic coding streams.', 'goals' => ['Run the first thematic stream'], 'tasks' => ['Choose format', 'Prepare the first codebase'], 'next_steps' => ['Schedule the legacy refactoring stream'], 'sort_order' => 40, 'milestones' => [['title' => 'Design stream format', 'status' => 'planned']]],
            ['name' => 'Products', 'slug' => 'products', 'status' => 'validating', 'progress' => 20, 'summary' => 'Validating small products before committing to large builds.', 'goals' => ['Validate one paid problem'], 'tasks' => ['Interview developers about English pain points'], 'next_steps' => ['Run five problem interviews'], 'sort_order' => 50, 'milestones' => [['title' => 'Create product hypothesis backlog', 'status' => 'completed'], ['title' => 'Validate first hypothesis', 'status' => 'planned']]],
            ['name' => 'Sales', 'slug' => 'sales', 'status' => 'foundation', 'progress' => 25, 'summary' => 'A clear offer and a low-friction path to start a conversation.', 'goals' => ['Generate qualified service conversations'], 'tasks' => ['Publish service descriptions', 'Connect request notifications'], 'next_steps' => ['Review first incoming requests weekly'], 'sort_order' => 60, 'milestones' => [['title' => 'Add structured service request flow', 'status' => 'completed']]],
        ];

        foreach ($areas as $data) {
            $milestones = $data['milestones'];
            unset($data['milestones']);
            $area = BrandProgressArea::query()->updateOrCreate(['slug' => $data['slug']], $data + ['active' => true]);
            foreach ($milestones as $index => $milestone) {
                $area->milestones()->updateOrCreate(['title' => $milestone['title']], $milestone + ['sort_order' => ($index + 1) * 10, 'completed_at' => $milestone['status'] === 'completed' ? now() : null]);
            }
        }
    }
}
