<?php

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

$name = 'Auth';
$url = '/api/v1/auth/';
$formatSuccess = TestHelpers::formatResponseSuccess();
$formatError = TestHelpers::formatResponseError();

describe('Login api test', function ()
use (
    $formatError,
    $formatSuccess,
    $url
) {
    it('should login with valid credential', function ()
    use (
        $url,
        $formatSuccess
    ) {
        $response = $this->post($url . 'login', [
            'email' => 'admin@mail.com',
            'password' => '12345678',
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    'token' => $response->json('data.token'),
                ],
            ]);
    });

    it('should login with invalid credential', function ()
    use (
        $url,
        $formatError
    ) {
        $response = $this->post($url . 'login', [
            'email' => 'admin@mail.com',
            'password' => '123456789',
        ]);
        $response->assertStatus(401)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Unauthorized',
                'errors' => null,
            ]);
    });

    it('should check validation required', function ()
    use (
        $url,
        $formatError
    ) {
        $response = $this->post($url . 'login');
        $response->assertStatus(400)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'email' => ['Email is required'],
                    'password' => ['Password is required'],
                ],
            ]);
    });

    it('should check validation email', function ()
    use (
        $url,
        $formatError
    ) {
        $response = $this->post($url . 'login', [
            'email' => 'admin',
            'password' => '123456789',
        ]);
        $response->assertStatus(400)
            ->assertJsonStructure(
                $formatError
            )->assertJson([
                'status' => 'error',
                'message' => 'Validation Error',
                'errors' => [
                    'email' => ['Email is not valid'],
                ],
            ]);
    });
});

describe('Get Profile api test', function ()
use (
    $formatError,
    $formatSuccess,
    $url
) {
    it('should get profile', function ()
    use (
        $url,
        $formatSuccess
    ) {
        $response = $this->get($url . 'profile');
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

describe('Logout api test', function ()
use (
    $formatError,
    $formatSuccess,
    $url
) {
    it('should logout', function ()
    use (
        $url,
        $formatSuccess
    ) {
        $response = $this->post($url . 'logout');
        $response->assertStatus(200)
            ->assertJsonStructure(
                $formatSuccess
            )->assertJson([
                'status' => 'success',
                'message' => 'Logout Success',
                'data' => null,
            ]);
    });
});
