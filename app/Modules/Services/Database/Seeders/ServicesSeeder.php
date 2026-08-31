<?php

declare(strict_types=1);

namespace App\Modules\Services\Database\Seeders;

use App\Models\ServiceOffering;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Laravel Product Development', 'slug' => 'laravel-product-development', 'description' => 'Design and build a focused web product or a high-value feature inside an existing system.', 'benefits' => ['Clear technical scope', 'Maintainable modular delivery', 'Production-minded implementation'], 'engagement_format' => 'Discovery followed by a milestone-based implementation.', 'cta_label' => 'Build a product', 'sort_order' => 10],
            ['name' => 'Legacy Modernization', 'slug' => 'legacy-modernization', 'description' => 'Reduce delivery risk and improve an existing codebase without reflexively rewriting it.', 'benefits' => ['Architecture and risk map', 'Incremental modernization plan', 'Measured implementation'], 'engagement_format' => 'Audit, prioritized roadmap, then focused delivery sprints.', 'cta_label' => 'Modernize a system', 'sort_order' => 20],
            ['name' => 'Architecture Consulting', 'slug' => 'architecture-consulting', 'description' => 'Get an independent technical review before a costly product or architecture decision.', 'benefits' => ['Concrete trade-offs', 'Written recommendations', 'Actionable next steps'], 'engagement_format' => 'Focused review session with an async written decision record.', 'cta_label' => 'Review my architecture', 'sort_order' => 30],
            ['name' => 'Technical Mentoring', 'slug' => 'technical-mentoring', 'description' => 'Develop engineering judgment through real code, system design, and product decisions.', 'benefits' => ['Individual development plan', 'Code and design feedback', 'Practical accountability'], 'engagement_format' => 'Recurring one-to-one sessions with work between calls.', 'cta_label' => 'Start mentoring', 'sort_order' => 40],
            ['name' => 'English Coaching for Developers', 'slug' => 'english-coaching-developers', 'description' => 'Improve the English you actually need for interviews, meetings, writing, and international teamwork.', 'benefits' => ['Developer-specific practice', 'Direct communication feedback', 'Work-relevant vocabulary'], 'engagement_format' => 'One-to-one coaching built around your real professional situations.', 'cta_label' => 'Improve my English', 'sort_order' => 50],
        ];

        foreach ($services as $service) {
            ServiceOffering::query()->updateOrCreate(['slug' => $service['slug']], $service + ['active' => true]);
        }
    }
}
