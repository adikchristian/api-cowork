<?php

use App\Models\Booking\BookingModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestHelpers;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed --class=RoleSeeder');
    $this->artisan('db:seed --class=AdminSeeder');
});

describe('Booking model test', function () {
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

    it('can create a Booking', function () use ($tableName, $data) {

        $coworkPlan = TestHelpers::createCoworkPlan();

        $user = TestHelpers::createUser();

        $data['user_id'] = $user->id;
        $data['cowork_plan_id'] = $coworkPlan->id;

        BookingModel::create($data);

        $this->assertDatabaseHas($tableName, $data);
    });

    it('can update a booking', function () use ($tableName, $data) {

        $coworkPlan = TestHelpers::createCoworkPlan();

        $user = TestHelpers::createUser();

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

        $coworkPlan = TestHelpers::createCoworkPlan();

        $user = TestHelpers::createUser();

        $data['user_id'] = $user->id;
        $data['cowork_plan_id'] = $coworkPlan->id;

        $booking = BookingModel::create($data);

        $booking->delete();

        $this->assertSoftDeleted($tableName, ['id' => $booking->id]);

        $this->assertDatabaseHas($tableName, ['id' => $booking->id]);

        $this->assertNull(BookingModel::find($booking->id));
    });
});
