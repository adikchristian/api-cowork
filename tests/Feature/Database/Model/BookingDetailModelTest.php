<?php

use App\Models\BookingDetail\BookingDetailModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestHelpers;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed --class=RoleSeeder');
    $this->artisan('db:seed --class=AdminSeeder');
});

describe('Booking Detail model test', function () {
    $tableName = 'booking_details';

    $data =  [
        'date' => '2024-11-27',
        'file' => 'test',
    ];

    it('check model exists', function () {
        $this->assertTrue(class_exists('App\Models\BookingDetail\BookingDetailModel'));
    });

    it('can create a Booking Detail', function () use ($tableName, $data) {

        $booking = TestHelpers::createBooking();

        $data['booking_id'] = $booking->id;

        BookingDetailModel::create($data);

        $this->assertDatabaseHas($tableName, $data);
    });

    it('can update a Booking Detail', function () use ($tableName, $data) {

        $booking = TestHelpers::createBooking();

        $data['booking_id'] = $booking->id;

        $BookingDetail = BookingDetailModel::create($data);

        $updatedData = [
            'date' => '2024-11-28',
            'file' => 'test2',
        ];

        $updatedData['booking_id'] = $booking->id;
        $BookingDetail->update($updatedData);

        $this->assertDatabaseHas($tableName, $updatedData);
    });

    it('can deleted a Booking Detail', function () use ($tableName, $data) {

        $booking = TestHelpers::createBooking();

        $data['booking_id'] = $booking->id;

        $BookingDetail = BookingDetailModel::create($data);

        $BookingDetail->delete();

        $this->assertSoftDeleted($tableName, ['id' => $BookingDetail->id]);

        $this->assertDatabaseHas($tableName, ['id' => $BookingDetail->id]);

        $this->assertNull(BookingDetailModel::find($BookingDetail->id));
    });
});
