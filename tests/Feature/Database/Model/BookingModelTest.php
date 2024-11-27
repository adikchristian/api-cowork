<?php

use App\Models\Booking\BookingModel;
use App\Models\Coworking\CoworkingModel;
use App\Models\CoworkPlan\CoworkPlanModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed --class=RoleSeeder');
    $this->artisan('db:seed --class=AdminSeeder');
});

describe('coworking plan model test', function () {
    $tableName = 'bookings';

    $data =  [
        'code' => '12344545',
        'price' => '1000000',
        'date' => '2024-11-27',
        'status' => 'pending',
    ];

    it('check model exists', function () {
        $this->assertTrue(class_exists('App\Models\Booking\BookingModel'));
    });

    it('can create a coworking plan', function () use ($tableName, $data) {

        $coworking = CoworkingModel::create([
            'name' => 'Tropical Nomad',
            'address' => 'Jl. Kebon Jeruk, Jakarta',
            'phone' => '08123456789',
            'email' => 'nJn8m@example.com',
            'url' => 'https://tropicalnomad.com',
        ]);

        $coworkPlan = CoworkPlanModel::create([
            'name' => 'Tropical Nomad',
            'code' => '12344545',
            'price' => '1000000',
            'benefit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'coworking_id' => $coworking->id,
        ]);

        $user = User::create([
            'name' => 'John Doe',
            'email' => 'V0i0y@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('member');

        $data['user_id'] = $user->id;
        $data['cowork_plan_id'] = $coworkPlan->id;

        BookingModel::create($data);

        $this->assertDatabaseHas($tableName, $data);
    });

    it('can update a booking', function () use ($tableName, $data) {

        $coworking = CoworkingModel::create([
            'name' => 'Tropical Nomad',
            'address' => 'Jl. Kebon Jeruk, Jakarta',
            'phone' => '08123456789',
            'email' => 'nJn8m@example.com',
            'url' => 'https://tropicalnomad.com',
        ]);

        $coworkPlan = CoworkPlanModel::create([
            'name' => 'Tropical Nomad',
            'code' => '12344545',
            'price' => '1000000',
            'benefit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'coworking_id' => $coworking->id,
        ]);

        $user = User::create([
            'name' => 'John Doe',
            'email' => 'V0i0y@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('member');

        $data['user_id'] = $user->id;
        $data['cowork_plan_id'] = $coworkPlan->id;

        $booking = BookingModel::create($data);

        $updatedData = [
            'code' => '12344545',
            'price' => '1000000',
            'date' => '2024-11-28',
            'status' => 'pending',
        ];

        $data['user_id'] = $user->id;
        $data['cowork_plan_id'] = $coworkPlan->id;

        $booking->update($updatedData);

        $this->assertDatabaseHas($tableName, $updatedData);
    });

    it('can deleted a booking', function () use ($tableName, $data) {
        $coworking = CoworkingModel::create([
            'name' => 'Tropical Nomad',
            'address' => 'Jl. Kebon Jeruk, Jakarta',
            'phone' => '08123456789',
            'email' => 'nJn8m@example.com',
            'url' => 'https://tropicalnomad.com',
        ]);

        $coworkPlan = CoworkPlanModel::create([
            'name' => 'Tropical Nomad',
            'code' => '12344545',
            'price' => '1000000',
            'benefit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'coworking_id' => $coworking->id,
        ]);

        $user = User::create([
            'name' => 'John Doe',
            'email' => 'V0i0y@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('member');

        $data['user_id'] = $user->id;
        $data['cowork_plan_id'] = $coworkPlan->id;

        $booking = BookingModel::create($data);

        $booking->delete();

        $this->assertSoftDeleted($tableName, ['id' => $booking->id]);

        $this->assertDatabaseHas($tableName, ['id' => $booking->id]);

        $this->assertNull(BookingModel::find($booking->id));
    });
});
