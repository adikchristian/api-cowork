<?php

use App\Models\Coworking\CoworkingModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestHelpers;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed --class=RoleSeeder');
    $this->artisan('db:seed --class=AdminSeeder');
});

$name = 'Coworking';
$url = '/api/v1/master-data/coworkings';
$formatSuccess = TestHelpers::formatResponseSuccess();
$formatError = TestHelpers::formatResponseError();

describe('find ' . $name . ' api test', function () use (
    $url,
    $formatSuccess,
    $formatError
) {

    it('should return a list of coworkings', function () use (
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

    it('should get sepecific coworking with valid id', function () use (
        $url,
        $formatSuccess
    ) {
        $coworking = TestHelpers::createCoworking();

        $response = $this->get($url.'/'.$coworking->id);
        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => $coworking->toArray(),
            ]);
    });

    it('should get sepecific coworking with invalid id', function () use (
        $url,
        $formatError
    ) {
        $response = $this->get($url.'/1000');
        $response->assertStatus(404)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Coworking Not Found',
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
        $response = $this->post($url);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'name' => ['The name field is required.'],
                    'address' => ['The address field is required.'],
                    'phone' => ['The phone field is required.'],
                    'email' => ['The email field is required.'],
                    'url' => ['The url field is required.'],
                ]
            ]);
    });

    it('should check validation unique email', function () use (
        $url,
        $formatError
    ) {
        $coworing = TestHelpers::createCoworking();

        $data = [
            'name' => 'Tropical Nomad',
            'address' => 'Jl. Raya Kuta, Kuta, Badung,',
            'phone' => '08123456710',
            'email' => $coworing->email,
            'url' => 'https://tropicalnomad.com',
        ];

        $response = $this->post($url, $data);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'email' => ['The email field must be unique.'],
                ]
            ]);
    });

    it('should check validation unique phone', function () use (
        $url,
        $formatError
    ) {
        $coworing = TestHelpers::createCoworking();

        $data = [
            'name' => 'Tropical Nomad',
            'address' => 'Jl. Raya Kuta, Kuta, Badung,',
            'phone' => $coworing->phone,
            'email' => 'nJn81m@example.com',
            'url' => 'https://tropicalnomad.com',
        ];

        $response = $this->post($url, $data);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'phone' => ['The phone field must be unique.'],
                ]
            ]);
    });

    it('should create a ' . $name, function () use (
        $url,
        $formatSuccess
    ) {
        $data = [
            'name' => 'Tropical Nomad',
            'address' => 'Jl. Raya Kuta, Kuta, Badung,',
            'phone' => '08123456710',
            'email' => 'nJn81m@example.com',
            'url' => 'https://tropicalnomad.com',
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
                    'address' => ['The address field is required.'],
                    'phone' => ['The phone field is required.'],
                    'email' => ['The email field is required.'],
                    'url' => ['The url field is required.'],
                ]
            ]);
    });

    it('should check validation unique email', function () use (
        $url,
        $formatError
    ) {
        $coworing = TestHelpers::createCoworking();

        $coworing2 = CoworkingModel::create([
            'name' => 'Test Name',
            'address' => 'test address',
            'phone' => '082145678902',
            'email' => 'nJn82m@example.com',
            'url' => 'https://testapi.com',
        ]);

        $data = [
            'name' => 'Tropical Nomad',
            'address' => 'Jl. Raya Kuta, Kuta, Badung,',
            'phone' => '08123456710',
            'email' => $coworing->email,
            'url' => 'https://tropicalnomad.com',
            'id' => $coworing2->id
        ];

        $response = $this->put($url . '/' . $coworing2->id, $data);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'email' => ['The email field must be unique.'],
                ]
            ]);
    });

    it('should check validation unique phone', function () use (
        $url,
        $formatError
    ) {
        $coworing = TestHelpers::createCoworking();

        $coworing2 = CoworkingModel::create([
            'name' => 'Test Name',
            'address' => 'test address',
            'phone' => '082145678902',
            'email' => 'nJn82m@example.com',
            'url' => 'https://testapi.com',
        ]);


        $data = [
            'name' => 'Tropical Nomad',
            'address' => 'Jl. Raya Kuta, Kuta, Badung,',
            'phone' => $coworing->phone,
            'email' => 'nJn82m@example.com',
            'url' => 'https://tropicalnomad.com',
            'id' => $coworing2->id
        ];

        $response = $this->put($url . '/' . $coworing2->id, $data);

        $response->assertStatus(400)
            ->assertJsonStructure($formatError)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'phone' => ['The phone field must be unique.'],
                ]
            ]);
    });

    it('should update a ' . $name . ' with valid id', function () use (
        $url,
        $formatSuccess
    ) {
        $coworing = TestHelpers::createCoworking();

        $data = [
            'name' => $coworing->name . ' Test Update',
            'address' => $coworing->address . ' Test Update',
            'phone' => $coworing->phone,
            'email' => $coworing->email,
            'url' => $coworing->url,
            'id' => $coworing->id
        ];

        $response = $this->put($url . '/' . $coworing->id, $data);

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
        $coworing = TestHelpers::createCoworking();

        $data = [
            'name' => $coworing->name . ' Test Update',
            'address' => $coworing->address . ' Test Update',
            'phone' => $coworing->phone,
            'email' => $coworing->email,
            'url' => $coworing->url,
            'id' => $coworing->id
        ];

        $response = $this->put($url . '/1000', $data);

        $response->assertStatus(404)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Coworking Not Found',
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
    it('should delete coworking with valid id', function () use (
        $url,
        $formatSuccess
    ) {
        $coworking = TestHelpers::createCoworking();

        $response = $this->delete($url.'/'.$coworking->id);
        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Coworking deleted successfully',
                'data' => null,
            ]);
    });

    it('should get sepecific coworking with invalid id', function () use (
        $url,
        $formatError
    ) {
        $response = $this->delete($url.'/1000');
        $response->assertStatus(404)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Coworking Not Found',
                'errors' => null,
            ]);
    });
});
