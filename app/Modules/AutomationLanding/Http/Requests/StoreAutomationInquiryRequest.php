<?php

declare(strict_types=1);

namespace App\Modules\AutomationLanding\Http\Requests;

use App\Core\Abstracts\Request;
use App\Modules\AutomationLanding\Services\AutomationLandingContent;
use App\Modules\AutomationLanding\Services\OpenClawDeveloperContent;
use App\Modules\AutomationLanding\Services\OpenClawShortContent;
use Illuminate\Validation\Rule;

class StoreAutomationInquiryRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $interestIds = match ($this->route()->getName()) {
            'openclaw-short.request' => app(OpenClawShortContent::class)->interestIds(),
            'openclaw-developer.request' => app(OpenClawDeveloperContent::class)->interestIds(),
            default => app(AutomationLandingContent::class)->interestIds(),
        };

        return [
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'required_without:contact'],
            'contact' => ['nullable', 'string', 'max:255', 'required_without:email'],
            'business_type' => ['nullable', 'string', 'max:160'],
            'company_size' => ['nullable', 'string', 'max:80'],
            'current_stack' => ['nullable', 'string', 'max:1000'],
            'budget' => ['nullable', 'string', 'max:120'],
            'interests' => ['required', 'array', 'min:1', 'max:8'],
            'interests.*' => ['string', Rule::in($interestIds)],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }
}
