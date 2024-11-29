<?php

use App\Models\BookingApproval\BookingApprovalModel;
use App\Models\BookingDetail\BookingDetailModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestHelpers;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed --class=RoleSeeder');
    $this->artisan('db:seed --class=AdminSeeder');
});

describe('Booking Approval model test', function () {
    $tableName = 'booking_approvals';

    $data =  [
        'status' => 'pending',
        'description' => 'test',
    ];

    it('check model exists', function () {
        $this->assertTrue(class_exists('App\Models\BookingApproval\BookingApprovalModel'));
    });

    it('can create a Booking Approval', function () use ($tableName, $data) {

        $booking = TestHelpers::createBooking();

        $data['user_id'] = $booking->user_id;

        $data['booking_id'] = $booking->id;

        BookingApprovalModel::create($data);

        $this->assertDatabaseHas($tableName, $data);
    });

    it('can update a Booking Approval', function () use ($tableName, $data) {

        $booking = TestHelpers::createBooking();

        $data['booking_id'] = $booking->id;

        $data['user_id'] = $booking->user_id;

        $BookingApproval = BookingApprovalModel::create($data);

        $updatedData = [
            'status' => 'success',
            'description' => 'test2',
        ];

        $updatedData['booking_id'] = $booking->id;
        $updatedData['user_id'] = $booking->user_id;

        $BookingApproval->update($updatedData);

        $this->assertDatabaseHas($tableName, $updatedData);
    });

    it('can deleted a Booking Approval', function () use ($tableName, $data) {

        $booking = TestHelpers::createBooking();

        $data['booking_id'] = $booking->id;

        $data['user_id'] = $booking->user_id;

        $BookingApproval = BookingApprovalModel::create($data);

        $BookingApproval->delete();

        $this->assertSoftDeleted($tableName, ['id' => $BookingApproval->id]);

        $this->assertDatabaseHas($tableName, ['id' => $BookingApproval->id]);

        $this->assertNull(BookingApprovalModel::find($BookingApproval->id));
    });
});
