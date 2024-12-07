<?php

use App\Models\Booking\BookingModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestHelpers;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed --class=RoleSeeder');
    $this->artisan('db:seed --class=AdminSeeder');
});

$name = 'Booking Member Area';
$url = '/api/v1/booking';
$formatSuccess = TestHelpers::formatResponseSuccess();
$formatError = TestHelpers::formatResponseError();

describe('find ' . $name . ' api test', function () use (
    $url,
    $formatSuccess,
    $formatError,
    $name
) {

    it('should return a list of ' . $name, function () use (
        $url,
        $formatSuccess
    ) {
        $tokenMember = TestHelpers::getJwtTokenMember();
        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenMember]
        )->getJson($url);
        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => $response->json('data'),
            ]);
    });

    it('should get sepecific ' . $name . ' with valid code and owner', function () use (
        $url,
        $formatSuccess
    ) {
        $coworkingPlan = TestHelpers::createCoworkPlan();
        $tokenMember = TestHelpers::getJwtTokenMember();
        $userId = auth('api')->user()->id;
        $booking = BookingModel::create([
            'user_id' => $userId,
            'cowork_plan_id' => $coworkingPlan->id,
            'code' => '12344545',
            'price' => $coworkingPlan->price,
            'date' => '2024-11-27',
            'status' => 'pending',
        ]);


        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenMember]
        )
            ->getJson($url . '/' . $booking->code);
        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => $response->json('data'),
            ]);
    });

    it('should get sepecific ' . $name . ' with invalid code or owner', function () use (
        $url,
        $formatError
    ) {
        $tokenMember = TestHelpers::getJwtTokenMember();
        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenMember]
        )->getJson($url . '/1000');
        $response->assertStatus(404)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Booking Not Found',
                'errors' => null,
            ]);
    });
});

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
                    'cowork_plan_id' => ['The cowork_plan_id field is required.'],
                    'code' => ['The code field is required.'],
                    'user_id' => ['The user_id field is required.'],
                    'date' => ['The date field is required.'],
                    'price' => ['The price field is required.'],
                    'status' => ['The status field is required.'],
                ]
            ]);
    });

    it('should check validation unique code', function () use (
        $url,
        $formatError
    ) {
        $booking = TestHelpers::createBooking();

        $data = [
            'cowork_plan_id' => $booking->cowork_plan_id,
            'code' => $booking->code,
            'price' => $booking->price,
            'user_id' => $booking->user_id,
            'status' => $booking->status,
        ];

        $tokenMember = TestHelpers::getJwtTokenMember();
        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenMember]
        )
            ->postJson($url, $data);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'code' => ['The code field must be unique.'],
                ]
            ]);
    });

    it('should check validation max', function () use (
        $url,
        $formatError
    ) {
        $tokenMember = TestHelpers::getJwtTokenMember();
        $booking = TestHelpers::createBooking();
        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenMember]
        )
            ->postJson($url, [
                'cowork_plan_id' => $booking->cowork_plan_id,
                'code' => '23433445454654545454',
                'price' => $booking->price,
                'user_id' => $booking->user_id,
                'status' => $booking->status,
            ]);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'code' => ['The code field must not exceed 10 characters.'],
                ]
            ]);
    });

    it('should check validation exist cowork_plan_id', function () use (
        $url,
        $formatError
    ) {
        $tokenMember = TestHelpers::getJwtTokenMember();
        $booking = TestHelpers::createBooking();
        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenMember]
        )
            ->postJson($url, [
                'cowork_plan_id' => 1000,
                'code' => '7788555',
                'price' => $booking->price,
                'user_id' => $booking->user_id,
                'status' => $booking->status,
                'date' => '2024-12-02',
            ]);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'cowork_plan_id' => ['Cowork Plan Not Found.'],
                ]
            ]);
    });

    it('should check validation exist user_id', function () use (
        $url,
        $formatError
    ) {
        $tokenMember = TestHelpers::getJwtTokenMember();
        $booking = TestHelpers::createBooking();
        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenMember]
        )
            ->postJson($url, [
                'cowork_plan_id' => $booking->cowork_plan_id,
                'code' => '7788556',
                'price' => $booking->price,
                'user_id' => 1000,
                'status' => $booking->status,
                'date' => '2024-12-02',
            ]);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'user_id' => ['User Not Found.'],
                ]
            ]);
    });

    it('should check validation date', function () use (
        $url,
        $formatError
    ) {
        $tokenMember = TestHelpers::getJwtTokenMember();
        $booking = TestHelpers::createBooking();
        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenMember]
        )
            ->postJson($url, [
                'cowork_plan_id' => $booking->cowork_plan_id,
                'code' => '7788557',
                'price' => $booking->price,
                'user_id' => $booking->user_id,
                'status' => $booking->status,
                'date' => 'test bad date',
            ]);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'date' => ['The date field must be a valid date.'],
                ]
            ]);
    });

    it('should check validation numeric', function () use (
        $url,
        $formatError
    ) {
        $tokenMember = TestHelpers::getJwtTokenMember();
        $booking = TestHelpers::createBooking();
        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenMember]
        )
            ->postJson($url, [
                'cowork_plan_id' => $booking->cowork_plan_id,
                'code' => '7788558',
                'price' => 'string',
                'user_id' => $booking->user_id,
                'status' => $booking->status,
                'date' => '2024-12-02',
            ]);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'price' => ['The price field must be a number.'],
                ]
            ]);
    });

    it('should create a ' . $name, function () use (
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
            'price' => '1000000',
            'code' => '12344545',
            'status' => 'success',
        ];

        $response = $this->withHeaders(
            [
                'Authorization' => 'Bearer ' . $tokenMember
            ]
        )
            ->postJson($url, $data);

        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'id' => $response->json('data.id'),
                    'cowork_plan_id' => $response->json('data.cowork_plan_id'),
                    'user_id' => $response->json('data.user_id'),
                    'date' => $response->json('data.date'),
                    'price' => $response->json('data.price'),
                    'code' => $response->json('data.code'),
                    'status' => $response->json('data.status'),
                ],
            ]);
    });

    it('should create a ' . $name . ' with invalid role', function () use (
        $url,
        $formatError
    ) {
        $coworkingPlan = TestHelpers::createCoworkPlan();
        $tokenMember = TestHelpers::getJwtTokenAdmin();
        $user = auth('api')->user();


        $data = [
            'cowork_plan_id' => $coworkingPlan->id,
            'user_id' => $user->id,
            'date' => '2024-12-02',
            'price' => '1000000',
            'code' => '12344545',
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
});

describe('update ' . $name . ' api test', function () use (
    $url,
    $formatError,
    $formatSuccess,
    $name
) {
    it('should cancle a ' . $name . ' with valid owner', function () use (
        $url,
        $formatSuccess
    ) {
        $coworkingPlan = TestHelpers::createCoworkPlan();
        $tokenMember = TestHelpers::getJwtTokenMember();
        $userId = auth('api')->user()->id;
        $booking = BookingModel::create([
            'user_id' => $userId,
            'cowork_plan_id' => $coworkingPlan->id,
            'code' => '12344545',
            'price' => $coworkingPlan->price,
            'date' => '2024-11-27',
            'status' => 'pending',
        ]);

        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenMember]
        )
            ->putJson($url . "/cancle/" . $booking->code);

        $response->assertStatus(200)
            ->assertJsonStructure($formatSuccess)
            ->assertJson([
                'status' => 'success',
                'message' => 'Cancel Booking Success',
                'data' => $response->json('data'),
            ]);
    });

    it('should cancle a ' . $name . ' with invalid code or owner', function () use (
        $url,
        $formatError
    ) {
        $tokenMember = TestHelpers::getJwtTokenMember();

        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenMember]
        )
            ->putJson($url . "/cancle/1000");

        $response->assertStatus(404)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Booking Not Found',
                'errors' => null,
            ]);
    });

    it('should cancle a ' . $name . ' with invalid role', function () use (
        $url,
        $formatError
    ) {
        $tokenMember = TestHelpers::getJwtTokenAdmin();

        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $tokenMember]
        )
            ->putJson($url . "/cancle/1000");

        $response->assertStatus(403)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Forbidden',
                'errors' => null,
            ]);
    });
});
