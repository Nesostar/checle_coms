<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenditures', function (Blueprint $table) {
            $table->id();

            // Link to expenditure category
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')
                  ->references('id')
                  ->on('expenditure_categories')
                  ->onDelete('set null');

            // Main details
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();

            // Dates
            $table->date('date');              // already in your system
            $table->date('expense_date')->nullable(); // added for reports

            // Payment info
            $table->enum('payment_method', ['Cash', 'Bank', 'Mobile Money'])->default('Cash');
            $table->string('reference_number')->nullable();  // M-Pesa / Bank ref.

            // File upload
            $table->string('receipt')->nullable();

            // Cashbook & report support
            $table->enum('transaction_type', ['Debit'])->default('Debit');
            $table->boolean('is_cashbook_entry')->default(true);

            // User tracking
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenditures');
    }
};
