<?php

use App\Models\Coworking\CoworkingModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('coworking model test', function () {
    $tableName = 'coworkings';

    $data =  [
        'name' => 'Tropical Nomad',
        'address' => 'Jl. Kebon Jeruk, Jakarta',
        'phone' => '08123456789',
        'email' => 'nJn8m@example.com',
        'url' => 'https://tropicalnomad.com',
    ];

    it('check model exists', function () {
        $this->assertTrue(class_exists('App\Models\Coworking\CoworkingModel'));
    });

    it('can create a coworking space', function() use($tableName, $data){
        
        CoworkingModel::create($data);

        $this->assertDatabaseHas($tableName, $data);
    });

    it('can update a coworking space', function() use($tableName, $data){
        
        $coworking = CoworkingModel::create($data);

        $updatedData = [
            'name' => 'Tropical Nomad test update',
            'address' => 'Jl. Kebon Jeruk, Jakarta',
            'phone' => '08123456789',
            'email' => 'nJn8m@example.com',
            'url' => 'https://tropicalnomad.com',
        ];

        $coworking->update($updatedData);

        $this->assertDatabaseHas($tableName, $updatedData);
    });

    it('can deleted a coworking space', function() use($tableName, $data){
        
        $coworking = CoworkingModel::create($data);

        $coworking->delete();

        $this->assertSoftDeleted($tableName, ['id' => $coworking->id]);

        $this->assertDatabaseHas($tableName, ['id' => $coworking->id]);

        $this->assertNull(CoworkingModel::find($coworking->id));
    });
});