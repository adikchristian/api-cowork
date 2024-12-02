<?php

namespace Tests\Helpers;

use App\Models\Booking\BookingModel;
use App\Models\Coworking\CoworkingModel;
use App\Models\CoworkPlan\CoworkPlanModel;
use App\Models\User;

class TestHelpers
{

    public static function createCoworking()
    {
        $coworking = CoworkingModel::create([
            'name' => 'Tropical Nomad',
            'address' => 'Jl. Kebon Jeruk, Jakarta',
            'phone' => '08123456789',
            'email' => 'nJn8m@example.com',
            'url' => 'https://tropicalnomad.com',
        ]);
        return $coworking;
    }
    public static function createCoworkPlan()
    {
        $coworking = self::createCoworking();

        $coworkPlan = CoworkPlanModel::create([
            'name' => 'Tropical Nomad',
            'code' => '12344545',
            'price' => '1000000',
            'benefit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'coworking_id' => $coworking->id,
        ]);

        return $coworkPlan;
    }

    public static function createUser()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'V0i0y@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('member');

        return $user;
    }

    public static function createBooking()
    {
        $coworkPlan = self::createCoworkPlan();

        $user = self::createUser();

        $booking = BookingModel::create([
            'user_id' => $user->id,
            'cowork_plan_id' => $coworkPlan->id,
            'code' => '12344545',
            'price' => '1000000',
            'date' => '2024-11-27',
            'status' => 'pending',
        ]);

        return $booking;
    }

    public static function formatResponseSuccess()
    {
        return ['status', 'message', 'data'];
    }

    public static function formatResponseError()
    {
        return ['status', 'message', 'errors'];
    }

    public static function getJwtTokenAdmin()
    {
        // Buat user menggunakan factory
        $user = User::factory()->create();
        $user->assignRole('admin');

        // Atur token JWT dengan library auth JWT
        $token = auth('api')->login($user);

        return $token;
    }

    public static function getJwtTokenMember()
    {
        // Buat user menggunakan factory
        $user = User::factory()->create();
        $user->assignRole('member');

        // Atur token JWT dengan library auth JWT
        $token = auth('api')->login($user);

        return $token;
    }
}
