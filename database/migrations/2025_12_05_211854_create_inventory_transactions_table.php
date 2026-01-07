<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {

            $table->id();

            // Later you will add item_id with the other migration
            $table->foreignId('entry_type_id')->constrained('entry_types')->onDelete('cascade');

            $table->integer('quantity');
            $table->string('note')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
