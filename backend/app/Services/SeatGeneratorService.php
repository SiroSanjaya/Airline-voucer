<?php

namespace App\Services;

use InvalidArgumentException;

class SeatGeneratorService
{
    /**
     * Seat layout definitions per aircraft type.
     * Each entry: ['rows' => int, 'columns' => string[]]
     */
    private const LAYOUTS = [
        'ATR' => [
            'rows'    => 18,
            'columns' => ['A', 'C', 'D', 'F'],
        ],
        'Airbus 320' => [
            'rows'    => 32,
            'columns' => ['A', 'B', 'C', 'D', 'E', 'F'],
        ],
        'Boeing 737 Max' => [
            'rows'    => 32,
            'columns' => ['A', 'B', 'C', 'D', 'E', 'F'],
        ],
    ];

    /**
     * Generate exactly 3 unique random seats for the given aircraft type.
     *
     * @param  string $aircraftType
     * @return string[]  e.g. ['3B', '7C', '14D']
     *
     * @throws InvalidArgumentException if aircraft type is unknown
     */
    public function generate(string $aircraftType): array
    {
        if (!array_key_exists($aircraftType, self::LAYOUTS)) {
            throw new InvalidArgumentException("Unknown aircraft type: {$aircraftType}");
        }

        $layout  = self::LAYOUTS[$aircraftType];
        $rows    = $layout['rows'];
        $columns = $layout['columns'];

        // Build full seat pool
        $pool = [];
        for ($row = 1; $row <= $rows; $row++) {
            foreach ($columns as $col) {
                $pool[] = $row . $col;
            }
        }

        // Pick 3 unique seats without replacement
        $picked = [];
        $usedIndexes = [];

        while (count($picked) < 3) {
            $index = random_int(0, count($pool) - 1);
            if (!in_array($index, $usedIndexes, true)) {
                $usedIndexes[] = $index;
                $picked[]      = $pool[$index];
            }
        }

        return $picked;
    }

    /**
     * Returns the list of valid aircraft types.
     */
    public static function validAircraftTypes(): array
    {
        return array_keys(self::LAYOUTS);
    }
}
