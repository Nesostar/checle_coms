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
    Schema::table('expenditures', function (Blueprint $table) {

        if (!Schema::hasColumn('expenditures', 'expense_date')) {
            $table->date('expense_date')->nullable()->after('date');
        }

        if (!Schema::hasColumn('expenditures', 'payment_method')) {
            $table->enum('payment_method', ['Cash', 'Bank', 'Mobile Money'])
                  ->default('Cash')
                  ->after('amount');
        }

        if (!Schema::hasColumn('expenditures', 'reference_number')) {
            $table->string('reference_number')->nullable()->after('payment_method');
        }

        if (!Schema::hasColumn('expenditures', 'receipt')) {
            $table->string('receipt')->nullable()->after('reference_number');
        }

        if (!Schema::hasColumn('expenditures', 'transaction_type')) {
            $table->enum('transaction_type', ['Debit'])->default('Debit')->after('receipt');
        }

        if (!Schema::hasColumn('expenditures', 'is_cashbook_entry')) {
            $table->boolean('is_cashbook_entry')->default(true)->after('transaction_type');
        }

        if (!Schema::hasColumn('expenditures', 'created_by')) {
            $table->unsignedBigInteger('created_by')->nullable()->after('is_cashbook_entry');
        }
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('expenditures', function (Blueprint $table) {
        $table->dropColumn([
            'expense_date',
            'payment_method',
            'reference_number',
            'receipt',
            'transaction_type',
            'is_cashbook_entry',
            'created_by'
        ]);
    });
}
};
