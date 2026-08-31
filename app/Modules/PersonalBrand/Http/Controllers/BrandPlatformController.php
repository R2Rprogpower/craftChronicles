<?php

declare(strict_types=1);

namespace App\Modules\PersonalBrand\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\PersonalBrand\Presentations\BrandPlatformPresentation;
use App\Modules\PersonalBrand\Processors\PortfolioPageProcessor;
use App\Modules\PersonalBrand\Processors\ProgressPageProcessor;
use Illuminate\Contracts\View\View;

class BrandPlatformController extends Controller
{
    public function portfolio(PortfolioPageProcessor $processor, BrandPlatformPresentation $presentation): View
    {
        return view('brand-platform', ['page' => 'portfolio', 'payload' => $presentation->present($processor->execute())]);
    }

    public function progress(ProgressPageProcessor $processor, BrandPlatformPresentation $presentation): View
    {
        return view('brand-platform', ['page' => 'progress', 'payload' => $presentation->present($processor->execute())]);
    }
}
