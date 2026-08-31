<?php

declare(strict_types=1);

namespace App\Modules\ServiceRequests\DTO;

readonly class CreateServiceRequestDTO
{
    public function __construct(
        public string $name,
        public ?string $email,
        public ?string $contact,
        public ?string $company,
        public ?int $serviceOfferingId,
        public ?string $budget,
        public string $message,
        public string $source = 'portfolio',
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return ['name' => $this->name, 'email' => $this->email, 'contact' => $this->contact, 'company' => $this->company, 'service_offering_id' => $this->serviceOfferingId, 'budget' => $this->budget, 'message' => $this->message, 'source' => $this->source, 'status' => 'new'];
    }
}
