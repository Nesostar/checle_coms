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
        Schema::table('entry_types', function (Blueprint $table) {
            $table->string('direction')->after('name'); // Add direction column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entry_types', function (Blueprint $table) {
            $table->dropColumn('direction'); // Remove direction column
        });
    }
};
