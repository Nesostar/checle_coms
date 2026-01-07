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
    Schema::create('sale_items', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('sale_id');
        $table->unsignedBigInteger('item_id');
        $table->integer('quantity');
        $table->decimal('price', 10, 2);
        $table->decimal('subtotal', 10, 2);
        $table->timestamps();

        $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_items');
    }
};
