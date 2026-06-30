<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'seats'   => [$this->seat1, $this->seat2, $this->seat3],
            'details' => [
                'crewName'     => $this->crew_name,
                'crewId'       => $this->crew_id,
                'flightNumber' => $this->flight_number,
                'flightDate'   => $this->flight_date,
                'aircraftType' => $this->aircraft_type,
            ],
        ];
    }
}
