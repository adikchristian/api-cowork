<?php

use App\Models\Booking\BookingModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Helpers\TestHelpers;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed --class=RoleSeeder');
    $this->artisan('db:seed --class=AdminSeeder');
});

$name = 'Booking Detail Member Area';
$url = '/api/v1/booking-detail';
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
        $tokenMember = TestHelpers::getJwtTokenMember();
        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenMember]
        )
            ->postJson($url);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'booking_id' => ['Booking ID is required'],
                    'file' => ['File is required'],
                    'date' => ['Date is required'],
                ]
            ]);
    });

    it('should create a ' . $name.' with valid owner and upload', function () use (
        $url,
        $formatSuccess
    ) {
        $coworkingPlan = TestHelpers::createCoworkPlan();
        $tokenMember = TestHelpers::getJwtTokenMember();
        $user = auth('api')->user();


        $data = [
            'cowork_plan_id' => $coworkingPlan->id,
            'user_id' => $user->id,
            'date' => '2024-12-02',
            'price' => $coworkingPlan->price,
            'code' => '12344545',
            'status' => 'success',
        ];

        $booking = BookingModel::create($data);

        Storage::fake('local');

        $file = UploadedFile::fake()->image('test.jpg');

        $dataDetail = [
            'booking_id' => $booking->id,
            'date' => '2024-12-02',
            'file' => $file,
        ];

        $response = $this->withHeaders(
            [
                'Authorization' => 'Bearer ' . $tokenMember
            ]
        )
            ->postJson($url, $dataDetail);

        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => $response->json('data'),
            ]);
        Storage::disk('local')
        ->exists('booking/' . $file->hashName());
    });

    it('should create a ' . $name . ' with invalid role', function () use (
        $url,
        $formatError
    ) {
        $tokenMember = TestHelpers::getJwtTokenAdmin();

        $booking = TestHelpers::createBooking();

        $file = UploadedFile::fake()->image('test.jpg');

        $data = [
            'booking_id' => $booking->id,
            'date' => '2024-12-02',
            'file' => $file,
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

    it('should create a ' . $name . ' with invalid owner', function () use (
        $url,
        $formatError
    ) {
        $tokenMember = TestHelpers::getJwtTokenMember();

        $booking = TestHelpers::createBooking();

        $file = UploadedFile::fake()->image('test.jpg');

        $data = [
            'booking_id' => $booking->id,
            'date' => '2024-12-02',
            'file' => $file,
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
                'message' => 'You are not allowed to do this action',
                'errors' => null,
            ]);
    });
});
