<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MainSectionResource extends JsonResource
{
    public function toArray($request)
    {
        $sectionsSetup = [
            1 => ['key' => 'cards_section', 'limit' => 4],
            2 => ['key' => 'centers_section', 'limit' => 4],
            3 => ['key' => 'services_section', 'limit' => 5],
            4 => ['key' => 'stats_section', 'limit' => 4],
        ];

        $config = $sectionsSetup[$this->id] ?? null;
        if (!$config) return [];

        $data = [];

        for ($i = 1; $i <= $config['limit']; $i++) {
            $column = "column_{$i}";

            if (!isset($this->$column)) continue;

            $value = $this->$column;

            if (is_string($value) && json_decode($value) !== null) {
                $value = json_decode($value, true);
            }

            $data[$column] = $value;
        }

        return [
            $config['key'] => [
                'data' => $data
            ]
        ];
    }
}
