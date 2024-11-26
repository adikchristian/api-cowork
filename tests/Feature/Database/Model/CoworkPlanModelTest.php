<?php

use App\Models\Coworking\CoworkingModel;
use App\Models\CoworkPlan\CoworkPlanModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('coworking plan model test', function () {
    $tableName = 'cowork_plans';

    $data =  [
        'name' => 'Tropical Nomad',
        'code' => '12344545',
        'price' => '1000000',
        'benefit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    ];

    it('check model exists', function () {
        $this->assertTrue(class_exists('App\Models\CoworkPlan\CoworkPlanModel'));
    });

    it('can create a coworking plan', function() use($tableName, $data){

        $cowokring = CoworkingModel::create([
            'name' => 'Tropical Nomad',
            'address' => 'Jl. Kebon Jeruk, Jakarta',
            'phone' => '08123456789',
            'email' => 'nJn8m@example.com',
            'url' => 'https://tropicalnomad.com',
        ]);

        $data['coworking_id'] = $cowokring->id;
        
        CoworkPlanModel::create($data);

        $this->assertDatabaseHas($tableName, $data);
    });

    it('can update a coworking plan', function() use($tableName, $data){
        
        $coworking = CoworkingModel::create([
            'name' => 'Tropical Nomad',
            'address' => 'Jl. Kebon Jeruk, Jakarta',
            'phone' => '08123456789',
            'email' => 'nJn8m@example.com',
            'url' => 'https://tropicalnomad.com',
        ]);

        $data['coworking_id'] = $coworking->id;

        $coworkkPlan = CoworkPlanModel::create($data);

        $updatedData = [
            'name' => 'Tropical Nomad test update',
            'code' => '12344545',
            'price' => '1000000',
            'benefit' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ];

        $updatedData['coworking_id'] = $coworking->id;

        $coworkkPlan->update($updatedData);

        $this->assertDatabaseHas($tableName, $updatedData);
    });

    it('can deleted a coworking plan', function() use($tableName, $data){
        $coworking = CoworkingModel::create([
            'name' => 'Tropical Nomad',
            'address' => 'Jl. Kebon Jeruk, Jakarta',
            'phone' => '08123456789',
            'email' => 'nJn8m@example.com',
            'url' => 'https://tropicalnomad.com',
        ]);

        $data['coworking_id'] = $coworking->id;

        $coworkPlan = CoworkPlanModel::create($data);

        $coworkPlan->delete();

        $this->assertSoftDeleted($tableName, ['id' => $coworkPlan->id]);

        $this->assertDatabaseHas($tableName, ['id' => $coworkPlan->id]);

        $this->assertNull(CoworkPlanModel::find($coworkPlan->id));
    });
});