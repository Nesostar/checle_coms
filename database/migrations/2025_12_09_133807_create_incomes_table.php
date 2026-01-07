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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_source_id')->constrained('income_sources')->onDelete('cascade');
            $table->string('title');
            $table->decimal('amount', 10, 2);
            $table->date('income_date');
            $table->text('description')->nullable();
            $table->enum('received_by', ['Cash', 'Bank', 'Mobile Money'])->default('Cash');
            $table->string('document')->nullable(); // file upload
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incomes');
    }
};
