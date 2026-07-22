<?php

namespace App\DiagnosticEcosystem\DTO;

readonly class HistoryEvent
{
    public function __construct(
        public string $eventType,
        public string $title,
        public ?string $description = null,
        public ?int $mileage = null,
        public array $metadata = [],
        public ?int $id = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'event_type' => $this->eventType,
            'title' => $this->title,
            'description' => $this->description,
            'mileage' => $this->mileage,
            'metadata' => $this->metadata,
        ];
    }
}
