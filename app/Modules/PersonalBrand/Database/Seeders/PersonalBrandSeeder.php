<?php

declare(strict_types=1);

namespace App\Modules\PersonalBrand\Database\Seeders;

use App\Models\BrandProfile;
use Illuminate\Database\Seeder;

class PersonalBrandSeeder extends Seeder
{
    public function run(): void
    {
        BrandProfile::query()->updateOrCreate(['key' => 'primary'], [
            'name' => 'Ruslan',
            'headline' => 'Full-stack engineer, technical mentor, and English coach',
            'bio' => 'I build maintainable products with Laravel and React, modernize existing systems, and help developers communicate and grow with clarity.',
            'location' => 'Ukraine · working remotely',
            'availability' => 'Open to selected product, consulting, and coaching engagements',
            'expertise' => [
                ['title' => 'Product Engineering', 'description' => 'From ambiguous idea to a focused, production-ready web product.'],
                ['title' => 'Laravel Architecture', 'description' => 'Modular backends, API design, modernization, and pragmatic technical direction.'],
                ['title' => 'Technical Communication', 'description' => 'English coaching and mentoring for developers working internationally.'],
            ],
            'social_links' => [
                ['label' => 'GitHub', 'url' => 'https://github.com/R2Rprogpower'],
                ['label' => 'Telegram', 'url' => 'https://t.me/rusrag'],
            ],
            'contact' => [
                'heading' => 'Have a product, system, or skill you want to move forward?',
                'description' => 'Send the context and desired outcome. I will reply with the most useful next step—not a ceremonial sales dance.',
                'preferred_channel' => 'Service request form or Telegram',
            ],
            'faq' => [
                ['question' => 'What projects are the best fit?', 'answer' => 'Laravel/React products, legacy modernization, architecture reviews, technical mentoring, and English coaching for developers.'],
                ['question' => 'Can we start with a small engagement?', 'answer' => 'Yes. A focused audit, discovery session, or technical spike is often the highest-leverage first step.'],
                ['question' => 'Do you work with existing codebases?', 'answer' => 'Yes. Improving a live system without rewriting everything is a core part of the work.'],
                ['question' => 'How quickly will you respond?', 'answer' => 'Usually within two working days. Include context, constraints, and the outcome you need.'],
            ],
            'active' => true,
        ]);
    }
}
