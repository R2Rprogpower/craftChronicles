<?php

declare(strict_types=1);

namespace App\Modules\ServiceRequests\Http\Requests;

use App\Core\Abstracts\Request;

class StoreServiceRequestRequest extends Request
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255', 'required_without:contact'],
            'contact' => ['nullable', 'string', 'max:255', 'required_without:email'],
            'company' => ['nullable', 'string', 'max:255'],
            'service_id' => ['nullable', 'integer', 'exists:service_offerings,id'],
            'budget' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }
}
