<?php

namespace App\DiagnosticEcosystem\DTO;

readonly class MaintenanceRecommendation
{
    public function __construct(
        public string $recommendationType,
        public string $title,
        public string $description,
        public string $priority,
        public ?int $dueAtMileage = null,
        public ?string $dueAtDate = null,
        public ?string $reasoning = null,
        public string $status = 'pending',
        public string $source = 'ai_predictive',
        public ?int $id = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'recommendation_type' => $this->recommendationType,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'due_at_mileage' => $this->dueAtMileage,
            'due_at_date' => $this->dueAtDate,
            'reasoning' => $this->reasoning,
            'status' => $this->status,
            'source' => $this->source,
        ];
    }
}
