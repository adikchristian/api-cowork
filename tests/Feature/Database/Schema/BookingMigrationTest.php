<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

describe('Booking Migration Test', function () {
    $tableName = 'bookings';

    it('check table exists', function () use($tableName) {
        $this->assertTrue(Schema::hasTable($tableName));
    });

    it('check table has correct columns', function () use($tableName) {
        $this->assertTrue(Schema::hasColumn($tableName, 'cowork_plan_id'));
        $this->assertTrue(Schema::hasColumn($tableName, 'code'));
        $this->assertTrue(Schema::hasColumn($tableName, 'user_id'));
        $this->assertTrue(Schema::hasColumn($tableName, 'date'));
        $this->assertTrue(Schema::hasColumn($tableName, 'price'));
        $this->assertTrue(Schema::hasColumn($tableName, 'status'));
        $this->assertTrue(Schema::hasColumn($tableName, 'created_at'));
        $this->assertTrue(Schema::hasColumn($tableName, 'updated_at'));
        $this->assertTrue(Schema::hasColumn($tableName, 'deleted_at'));
    });

    it('check table has correct data types', function () use($tableName) {
        $columns = DB::select("SHOW COLUMNS FROM {$tableName}");

        // Membuat array tipe data kolom yang diharapkan
        $expected = [
            'id' => 'bigint',
            'cowork_plan_id' => 'bigint',
            'code' => 'varchar(10)',
            'user_id' => 'bigint',
            'price' => 'decimal(20,2)',
            'status' => 'enum(\'pending\',\'success\',\'cancled\')',
            'date' => 'date',
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
        $uniqueKeyCode = DB::select("SHOW KEYS FROM {$tableName} WHERE Key_name = '{$tableName}_code_unique'");
        $this->assertNotEmpty($uniqueKeyCode);
        $this->assertEquals('code', $uniqueKeyCode[0]->Column_name);
    });

    it('check table has correct foreign keys', function () use($tableName) {
        $foreignKeyCoworkPlanId = DB::select("SHOW KEYS FROM {$tableName} WHERE Key_name = '{$tableName}_cowork_plan_id_foreign'");
        $this->assertNotEmpty($foreignKeyCoworkPlanId);
        $this->assertEquals('cowork_plan_id', $foreignKeyCoworkPlanId[0]->Column_name);

        $foreignKeyUserId = DB::select("SHOW KEYS FROM {$tableName} WHERE Key_name = '{$tableName}_user_id_foreign'");
        $this->assertNotEmpty($foreignKeyUserId);
        $this->assertEquals('user_id', $foreignKeyUserId[0]->Column_name);
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
