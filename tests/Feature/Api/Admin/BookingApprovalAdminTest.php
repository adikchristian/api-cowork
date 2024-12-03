<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestHelpers;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed --class=RoleSeeder');
    $this->artisan('db:seed --class=AdminSeeder');
});

$name = 'Booking Approval Admin Area';
$url = '/api/v1/booking-approval';
$formatSuccess = TestHelpers::formatResponseSuccess();
$formatError = TestHelpers::formatResponseError();

describe('create ' . $name . ' api test', function () use (
    $url,
    $formatError,
    $formatSuccess,
    $name
) {
    it('should check validation required', function () use (
        $url,
        $formatError
    ) {
        $tokenAdmin = TestHelpers::getJwtTokenAdmin();
        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenAdmin]
        )
            ->postJson($url);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'booking_id' => ['Booking ID is required'],
                    'status' => ['Status is required'],
                ]
            ]);
    });

    it('should check validation exists booking id', function () use (
        $url,
        $formatError
    ) {
        $tokenAdmin = TestHelpers::getJwtTokenAdmin();
        $data = [
            'booking_id' => 1000,
            'status' => 'success',
        ];
        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenAdmin]
        )
            ->postJson($url, $data);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'booking_id' => ['Booking ID Not Found']
                ]
            ]);
    });

    it('should check validation :in status', function () use (
        $url,
        $formatError
    ) {
        $tokenAdmin = TestHelpers::getJwtTokenAdmin();
        $bookingDetail = TestHelpers::createBookingDetail();
        $data = [
            'booking_id' => $bookingDetail->booking_id,
            'status' => 'test',
        ];
        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenAdmin]
        )
            ->postJson($url, $data);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'status' => ['Status must be success, pending, or cancled']
                ]
            ]);
    });

    it('should create a ' . $name, function () use (
        $url,
        $formatSuccess
    ) {
        $tokenAdmin = TestHelpers::getJwtTokenAdmin();

        $bookingDetail = TestHelpers::createBookingDetail();

        $data = [
            'booking_id' => $bookingDetail->booking_id,
            'status' => 'success',
        ];

        $response = $this->withHeaders(
            [
                'Authorization' => 'Bearer ' . $tokenAdmin
            ]
        )
            ->postJson($url, $data);

        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Approve Booking Success',
                'data' => null,
            ]);
    });

    it('should create a ' . $name . ' with invalid role', function () use (
        $url,
        $formatError
    ) {
        $tokenMember = TestHelpers::getJwtTokenMember();

        $bookingDetail = TestHelpers::createBookingDetail();

        $data = [
            'booking_id' => $bookingDetail->booking_id,
            'status' => 'success',
        ];

        $response = $this->withHeaders(
            [
                'Authorization' => 'Bearer ' . $tokenMember
            ]
        )
            ->postJson($url, $data);

        $response->assertStatus(403)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Forbidden',
                'errors' => null,
            ]);
    });

    it('should create a ' . $name . ' with invalid booking id', function () use (
        $url,
        $formatError
    ) {
        $tokenAdmin = TestHelpers::getJwtTokenAdmin();

        TestHelpers::createBookingDetail();

        $data = [
            'booking_id' => 1000,
            'status' => 'success',
        ];

        $response = $this->withHeaders(
            [
                'Authorization' => 'Bearer ' . $tokenAdmin
            ]
        )
            ->postJson($url, $data);

        $response->assertStatus(400)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'booking_id' => ['Booking ID Not Found'],
                ],
            ]);
    });

    it('should create a ' . $name . ' with invalid status', function () use (
        $url,
        $formatError
    ) {
        $tokenAdmin = TestHelpers::getJwtTokenAdmin();

        $bookingDetail = TestHelpers::createBookingDetailSuccess();

        $data = [
            'booking_id' => $bookingDetail->booking_id,
            'status' => 'success',
        ];

        $response = $this->withHeaders(
            [
                'Authorization' => 'Bearer ' . $tokenAdmin
            ]
        )
            ->postJson($url, $data);

        $response->assertStatus(400)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Booking Status Not Pending',
                'errors' => null,
            ]);
    });

    it('should create a ' . $name . ' with empty book detail', function () use (
        $url,
        $formatError
    ) {
        $tokenAdmin = TestHelpers::getJwtTokenAdmin();

        $booking = TestHelpers::createBooking();

        $data = [
            'booking_id' => $booking->id,
            'status' => 'success',
        ];

        $response = $this->withHeaders(
            [
                'Authorization' => 'Bearer ' . $tokenAdmin
            ]
        )
            ->postJson($url, $data);

        $response->assertStatus(404)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Booking not have confirmation',
                'errors' => null,
            ]);
    });
});
