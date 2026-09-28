<?php

declare(strict_types=1);

namespace App\Modules\AutomationLanding\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AutomationLanding\Http\Requests\StoreAutomationInquiryRequest;
use App\Modules\AutomationLanding\Services\AutomationLandingContent;
use App\Modules\AutomationLanding\Services\OpenClawDeveloperContent;
use App\Modules\AutomationLanding\Services\OpenClawShortContent;
use App\Modules\AutomationLanding\Support\OpenClawLocale;
use App\Modules\ServiceRequests\DTO\CreateServiceRequestDTO;
use App\Modules\ServiceRequests\Services\ServiceRequestService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AutomationLandingController extends Controller
{
    public function index(Request $request, AutomationLandingContent $content): View
    {
        $locale = OpenClawLocale::resolve($request->query('lang'));

        return view('automation-services', ['content' => $content->get($locale), 'locale' => $locale]);
    }

    public function short(Request $request, OpenClawShortContent $content): View
    {
        $locale = OpenClawLocale::resolve($request->query('lang'));

        return view('openclaw-short', ['content' => $content->get($locale), 'locale' => $locale]);
    }

    public function developer(Request $request, OpenClawDeveloperContent $content): View
    {
        $locale = OpenClawLocale::resolve($request->query('lang'));

        return view('openclaw-developer', ['content' => $content->get($locale), 'locale' => $locale]);
    }

    public function research(Request $request): View
    {
        $locale = OpenClawLocale::resolve($request->query('lang'));
        $view = $locale === 'ru' ? 'openclaw-research' : "openclaw-research-{$locale}";

        return view($view, ['locale' => $locale]);
    }

    public function store(StoreAutomationInquiryRequest $request, ServiceRequestService $service): JsonResponse
    {
        return $this->persist($request->validated(), $service, 'automation-landing');
    }

    public function storeShort(StoreAutomationInquiryRequest $request, ServiceRequestService $service): JsonResponse
    {
        return $this->persist($request->validated(), $service, 'openclaw-short');
    }

    public function storeDeveloper(StoreAutomationInquiryRequest $request, ServiceRequestService $service): JsonResponse
    {
        return $this->persist($request->validated(), $service, 'openclaw-developer');
    }

    /** @param array<string, mixed> $data */
    private function persist(array $data, ServiceRequestService $service, string $source): JsonResponse
    {
        $inquiry = $service->create(new CreateServiceRequestDTO(
            name: $data['name'],
            email: $data['email'] ?? null,
            contact: $data['contact'] ?? null,
            company: $data['company'] ?? null,
            serviceOfferingId: null,
            budget: $data['budget'] ?? null,
            message: $data['message'],
            source: $source,
            metadata: [
                'interests' => array_values($data['interests']),
                'business_type' => $data['business_type'] ?? null,
                'company_size' => $data['company_size'] ?? null,
                'current_stack' => $data['current_stack'] ?? null,
            ],
        ));

        return response()->json([
            'message' => 'OpenClaw inquiry received',
            'data' => ['id' => $inquiry->id, 'status' => $inquiry->status],
        ], Response::HTTP_CREATED);
    }
}
