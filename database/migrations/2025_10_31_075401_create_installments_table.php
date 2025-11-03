<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paylater_transaction_id');
            $table->date('due_date');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->date('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('paylater_transaction_id')->references('id')->on('paylater_transactions')->onDelete('cascade');

            $table->index('paylater_transaction_id');
            $table->index('status');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('installments');
    }
}