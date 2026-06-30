<?php

namespace Tests\Feature;

use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherTest extends TestCase
{
    use RefreshDatabase;

    // ─── POST /api/check ────────────────────────────────────────────────────

    public function test_check_returns_false_when_no_voucher_exists(): void
    {
        $response = $this->postJson('/api/check', [
            'flightNumber' => 'GA102',
            'date'         => '2025-07-12',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['exists' => false]);
    }

    public function test_check_returns_true_when_voucher_exists(): void
    {
        Voucher::factory()->create([
            'flight_number' => 'GA102',
            'flight_date'   => '2025-07-12',
        ]);

        $response = $this->postJson('/api/check', [
            'flightNumber' => 'GA102',
            'date'         => '2025-07-12',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['exists' => true]);
    }

    public function test_check_validates_required_fields(): void
    {
        $response = $this->postJson('/api/check', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['flightNumber', 'date']);
    }

    // ─── POST /api/generate ─────────────────────────────────────────────────

    public function test_generate_creates_voucher_with_valid_seats(): void
    {
        $response = $this->postJson('/api/generate', [
            'name'         => 'Sarah',
            'id'           => '98123',
            'flightNumber' => 'GA102',
            'date'         => '2025-07-12',
            'aircraft'     => 'Airbus 320',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'success',
                         'seats',
                         'details' => ['crewName', 'crewId', 'flightNumber', 'flightDate', 'aircraftType'],
                     ],
                 ])
                 ->assertJsonPath('data.success', true);

        $seats = $response->json('data.seats');
        $this->assertCount(3, $seats);
        $this->assertCount(3, array_unique($seats)); // all unique

        // Validate each seat matches Airbus 320 layout (rows 1-32, cols A-F)
        foreach ($seats as $seat) {
            $this->assertMatchesRegularExpression('/^([1-9]|[12]\d|3[0-2])[A-F]$/', $seat);
        }

        $this->assertDatabaseHas('vouchers', [
            'flight_number' => 'GA102',
            'flight_date'   => '2025-07-12',
        ]);
    }

    public function test_generate_returns_409_when_duplicate_flight_date(): void
    {
        $payload = [
            'name'         => 'Sarah',
            'id'           => '98123',
            'flightNumber' => 'GA102',
            'date'         => '2025-07-12',
            'aircraft'     => 'Airbus 320',
        ];

        $this->postJson('/api/generate', $payload)->assertStatus(201);
        $second = $this->postJson('/api/generate', $payload);

        $second->assertStatus(409)
               ->assertJson(['success' => false]);
    }

    public function test_generate_validates_required_fields(): void
    {
        $response = $this->postJson('/api/generate', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'id', 'flightNumber', 'date', 'aircraft']);
    }

    public function test_generate_rejects_invalid_aircraft_type(): void
    {
        $response = $this->postJson('/api/generate', [
            'name'         => 'Sarah',
            'id'           => '98123',
            'flightNumber' => 'GA102',
            'date'         => '2025-07-12',
            'aircraft'     => 'Boeing 747',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['aircraft']);
    }

    public function test_generate_atr_seats_are_valid(): void
    {
        $response = $this->postJson('/api/generate', [
            'name'         => 'John',
            'id'           => '00001',
            'flightNumber' => 'ID500',
            'date'         => '2025-08-01',
            'aircraft'     => 'ATR',
        ]);

        $response->assertStatus(201);

        $seats = $response->json('data.seats');
        foreach ($seats as $seat) {
            // ATR: rows 1-18, columns A, C, D, F only
            $this->assertMatchesRegularExpression('/^([1-9]|1[0-8])[ACDF]$/', $seat);
        }
    }

    public function test_generate_boeing_seats_are_valid(): void
    {
        $response = $this->postJson('/api/generate', [
            'name'         => 'Jane',
            'id'           => '00002',
            'flightNumber' => 'SJ888',
            'date'         => '2025-09-01',
            'aircraft'     => 'Boeing 737 Max',
        ]);

        $response->assertStatus(201);

        $seats = $response->json('data.seats');
        foreach ($seats as $seat) {
            $this->assertMatchesRegularExpression('/^([1-9]|[12]\d|3[0-2])[A-F]$/', $seat);
        }
    }
}
