<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Using raw SQL to alter ENUM values
        DB::statement("ALTER TABLE deposits MODIFY COLUMN role ENUM('admin', 'cashier', 'staff') NULL");
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE deposits MODIFY COLUMN role ENUM('admin', 'cashier') NULL");
    }
};
