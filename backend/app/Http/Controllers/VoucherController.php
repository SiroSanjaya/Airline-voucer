<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateVoucherRequest;
use App\Http\Resources\VoucherResource;
use App\Models\Voucher;
use App\Services\SeatGeneratorService;
use Illuminate\Http\JsonResponse;

class VoucherController extends Controller
{
    public function __construct(
        private readonly SeatGeneratorService $seatGenerator
    ) {}


    /**
     * POST /api/generate
     * Generate 3 random seats, persist to DB, and return the result.
     */
    public function generate(GenerateVoucherRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Guard: prevent duplicate assignment (race condition safety)
        if (Voucher::existsForFlight($data['flightNumber'], $data['date'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vouchers have already been generated for this flight and date.',
            ], 409);
        }

        $seats = $this->seatGenerator->generate($data['aircraft']);

        $voucher = Voucher::create([
            'crew_name'     => $data['name'],
            'crew_id'       => $data['id'],
            'flight_number' => $data['flightNumber'],
            'flight_date'   => $data['date'],
            'aircraft_type' => $data['aircraft'],
            'seat1'         => $seats[0],
            'seat2'         => $seats[1],
            'seat3'         => $seats[2],
        ]);

        return (new VoucherResource($voucher))
            ->response()
            ->setStatusCode(201);
    }
}
