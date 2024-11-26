<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

describe('Coworking Migration Test', function () {
    $tableName = 'coworkings';

    it('check table exists', function () {
        $this->assertTrue(Schema::hasTable('coworkings'));
    });

    it('check table has correct columns', function () {
        $this->assertTrue(Schema::hasColumn('coworkings', 'name'));
        $this->assertTrue(Schema::hasColumn('coworkings', 'address'));
        $this->assertTrue(Schema::hasColumn('coworkings', 'phone'));
        $this->assertTrue(Schema::hasColumn('coworkings', 'email'));
        $this->assertTrue(Schema::hasColumn('coworkings', 'url'));
        $this->assertTrue(Schema::hasColumn('coworkings', 'created_at'));
        $this->assertTrue(Schema::hasColumn('coworkings', 'updated_at'));
        $this->assertTrue(Schema::hasColumn('coworkings', 'deleted_at'));
    });

    it('check table has correct data types', function () use($tableName) {
        $columns = DB::select("SHOW COLUMNS FROM {$tableName}");

        // Membuat array tipe data kolom yang diharapkan
        $expected = [
            'id' => 'bigint',
            'name' => 'varchar(255)',
            'address' => 'varchar(255)',
            'phone' => 'varchar(255)',
            'email' => 'varchar(255)',
            'url' => 'varchar(255)',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
        ];

        // Periksa setiap kolom dan tipe datanya
        foreach ($columns as $column) {
            $this->assertArrayHasKey($column->Field, $expected);
            $this->assertStringContainsString($expected[$column->Field], $column->Type, "Kolom {$column->Field} memiliki tipe data yang salah.");
        }
    });

    it('check table has correct primary key', function () use($tableName) {
        $primaryKey = DB::select("SHOW KEYS FROM {$tableName} WHERE Key_name = 'PRIMARY'");
        $this->assertNotEmpty($primaryKey);
        $this->assertEquals('id', $primaryKey[0]->Column_name);
    });

    it('check table has correct unique keys', function () use($tableName) {
        $uniqueKeyPhone = DB::select("SHOW KEYS FROM {$tableName} WHERE Key_name = '{$tableName}_phone_unique'");
        $this->assertNotEmpty($uniqueKeyPhone);
        $this->assertEquals('phone', $uniqueKeyPhone[0]->Column_name);

        $uniqueKeyEmail = DB::select("SHOW KEYS FROM {$tableName} WHERE Key_name = '{$tableName}_email_unique'");
        $this->assertNotEmpty($uniqueKeyEmail);
        $this->assertEquals('email', $uniqueKeyEmail[0]->Column_name);
    });

    it('table is deleted after rollback', function () use($tableName) {
       // Pastikan table ada sebelum rollback
       $this->assertTrue(Schema::hasTable($tableName));

       // Jalankan rollback
       Artisan::call('migrate:rollback', ['--step' => 1]);

       // Pastikan table tidak ada setelah rollback
       $this->assertFalse(Schema::hasTable($tableName));; 
    });
});
