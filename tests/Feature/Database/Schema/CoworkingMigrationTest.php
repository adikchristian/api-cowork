<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('can run database migrations', function () {
    $this->artisan('migrate')->assertExitCode(0);

    $this->assertTrue(Schema::hasTable('coworkings'));

    $expectedColumns = [
        'id',
        'name',
        'address',
        'phone',
        'email',
        'url',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    foreach ($expectedColumns as $column) {
        $this->assertTrue(Schema::hasColumn('coworkings', $column), "Kolom $column tidak ditemukan di tabel posts");
    }
});
