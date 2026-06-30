<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'crew_name',
        'crew_id',
        'flight_number',
        'flight_date',
        'aircraft_type',
        'seat1',
        'seat2',
        'seat3',
    ];

    /**
     * Check if a voucher already exists for the given flight and date.
     */
    public static function existsForFlight(string $flightNumber, string $date): bool
    {
        return static::where('flight_number', $flightNumber)
            ->where('flight_date', $date)
            ->exists();
    }
}
