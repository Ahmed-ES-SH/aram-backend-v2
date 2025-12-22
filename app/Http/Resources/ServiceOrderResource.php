<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),

            'invoice' => $this->whenLoaded('invoice', fn() => [
                'number' => $this->invoice->invoice_number,
                'total' => (float) $this->invoice->total_invoice,
                'currency' => $this->invoice->currency,
                'status' => $this->invoice->status,
                'payment_method' => $this->invoice->payment_method,
            ]),

            'service' => $this->whenLoaded('service', fn() => [
                'id' => $this->service->id,
                'slug' => $this->service->slug,
                'price' => (float) $this->service->price,
                'type' => $this->service->type,
                'is_active' => $this->service->is_active,
                'whatsapp_number' => $this->service->whatsapp_number,
                'image' => $this->service->galleryImages[0]->path,

                // 'tracking_files' => $this->service->trackings->flatMap(fn($track) => $track->files),
            ]),

            'user' => $this->user_type === 'user'
                ? $this->whenLoaded('user')
                : $this->whenLoaded('organization'),

            'trackings' => $this->service->trackings->map(fn($track) => [
                'id' => $track->id,
                'status' => $track->status,
                'phase' => $track->current_phase,
                'start_time' => $track->start_time,
                'end_time' => $track->end_time,
                'metadata' => $this->isJson($track->metadata) ? json_decode($track->metadata) : $track->metadata,
                'files' => $track->files,
            ]),

            'form_data' => $this->extractFormData(),
        ];
    }

    private function extractFormData(): array
    {
        return collect($this->metadata['items']['metadata'] ?? [])
            ->map(fn($item) => [
                'key' => $item['key'],
                'label' => $item['label'],
                'value' => $item['value'],
                'type' => $item['type'],
            ])
            ->values()
            ->all();
    }


    private function isJson($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
