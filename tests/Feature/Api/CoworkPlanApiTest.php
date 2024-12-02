<?php

use App\Models\CoworkPlan\CoworkPlanModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestHelpers;

use function Pest\Laravel\withHeaders;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed --class=RoleSeeder');
    $this->artisan('db:seed --class=AdminSeeder');

    $token = TestHelpers::getJwtTokenAdmin();

    withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ]);
});

$name = 'Coworking Plan';
$url = '/api/v1/master-data/cowork-plans';
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
        $response = $this->get($url);
        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => $response->json('data'),
            ]);
    });

    it('should get sepecific ' . $name . ' with valid id', function () use (
        $url,
        $formatSuccess
    ) {
        $coworkingPlan = TestHelpers::createCoworkPlan();

        $response = $this->get($url . '/' . $coworkingPlan->id);
        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => $coworkingPlan->toArray(),
            ]);
    });

    it('should get sepecific ' . $name . ' with invalid id', function () use (
        $url,
        $formatError
    ) {
        $response = $this->get($url . '/1000');
        $response->assertStatus(404)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Coworking Plan Not Found',
                'errors' => null,
            ]);
    });

    it('should get Coworking Plan with Coworking ID', function () use (
        $url,
        $formatSuccess
    ) {
        $coworkingPlan = TestHelpers::createCoworkPlan();

        $response = $this->get($url . '/' . $coworkingPlan->coworking_id . '/coworking');
        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => $response->json('data'),
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
        $response = $this->post($url);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'name' => ['The name field is required.'],
                    'code' => ['The code field is required.'],
                    'price' => ['The price field is required.'],
                    'coworking_id' => ['The coworking_id field is required.'],
                    'benefit' => ['The benefit field is required.'],
                ]
            ]);
    });

    it('should check validation unique code', function () use (
        $url,
        $formatError
    ) {
        $coworkingPlan = TestHelpers::createCoworkPlan();

        $data = [
            'name' => 'Tropical Nomad',
            'code' => $coworkingPlan->code,
            'price' => $coworkingPlan->price,
            'coworking_id' => $coworkingPlan->coworking_id,
            'benefit' => $coworkingPlan->benefit,
        ];

        $response = $this->post($url, $data);

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

    it('should create a ' . $name, function () use (
        $url,
        $formatSuccess
    ) {
        $coworking = TestHelpers::createCoworking();

        $data = [
            'name' => 'Monthly',
            'code' => '1234567890',
            'coworking_id' => $coworking->id,
            'price' => '200000',
            'benefit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ];

        $response = $this->post($url, $data);

        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => $data,
            ]);
    });
});

describe('update ' . $name . ' api test', function () use (
    $url,
    $formatError,
    $formatSuccess,
    $name
) {
    it('should check validation required', function () use (
        $url,
        $formatError
    ) {
        $response = $this->put($url . '/1');

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'name' => ['The name field is required.'],
                    'code' => ['The code field is required.'],
                    'price' => ['The price field is required.'],
                    'coworking_id' => ['The coworking_id field is required.'],
                    'benefit' => ['The benefit field is required.'],
                ]
            ]);
    });

    it('should check validation unique code', function () use (
        $url,
        $formatError
    ) {
        $coworkingPlan = TestHelpers::createCoworkPlan();

        $coworkingNew = CoworkPlanModel::create([
            'name' => 'Tropical Nomad',
            'code' => '7987544',
            'price' => '1000000',
            'coworking_id' => $coworkingPlan->coworking_id,
            'benefit' => 'test benefit',
        ]);

        $data = [
            'name' => 'Tropical Nomad test',
            'code' => $coworkingPlan->code,
            'price' => '3000000',
            'coworking_id' => $coworkingPlan->coworking_id,
            'benefit' => $coworkingPlan->benefit,
            'id' => $coworkingNew->id,
        ];

        $response = $this->put($url.'/'.$coworkingNew->id, $data);

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

    it('should update a ' . $name . ' with valid id', function () use (
        $url,
        $formatSuccess
    ) {
        $coworkPlan = TestHelpers::createCoworkPlan();

        $data = [
            'name' => $coworkPlan->name . ' Test Update',
            'code' => $coworkPlan->code,
            'price' => $coworkPlan->price,
            'coworking_id' => $coworkPlan->coworking_id,
            'benefit' => $coworkPlan->benefit,
            'id' => $coworkPlan->id
        ];

        $response = $this->put($url . '/' . $coworkPlan->id, $data);

        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => $data,
            ]);
    });

    it('should update a ' . $name . ' with invalid id', function () use (
        $url,
        $formatError
    ) {
        $coworkPlan = TestHelpers::createCoworkPlan();

        $data = [
            'name' => $coworkPlan->name . ' Test Update',
            'code' => $coworkPlan->code,
            'price' => $coworkPlan->price,
            'coworking_id' => $coworkPlan->coworking_id,
            'benefit' => $coworkPlan->benefit,
            'id' => $coworkPlan->id
        ];

        $response = $this->put($url . '/1000', $data);

        $response->assertStatus(404)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Coworking Plan Not Found',
                'errors' => null,
            ]);
    });
});

describe('delete ' . $name . ' api test', function () use (
    $url,
    $formatError,
    $formatSuccess,
    $name
) {
    it('should delete' . $name . ' with valid id', function () use (
        $url,
        $formatSuccess
    ) {
        $coworkingPlan = TestHelpers::createCoworkPlan();

        $response = $this->delete($url.'/'.$coworkingPlan->id);
        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Coworking Plan deleted successfully',
                'data' => null,
            ]);
    });

    it('should get sepecific ' . $name . ' with invalid id', function () use (
        $url,
        $formatError
    ) {
        $response = $this->delete($url.'/1000');
        $response->assertStatus(404)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Coworking Plan Not Found',
                'errors' => null,
            ]);
    });
});
