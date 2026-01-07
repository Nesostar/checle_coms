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
    Schema::create('entry_types', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('item_id');
        $table->string('name'); // e.g Purchase, Restock
        $table->string('description')->nullable();
        $table->timestamps();

        $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entry_types');
    }
};
