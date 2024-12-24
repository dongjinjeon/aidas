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
        Schema::create('request_money', function (Blueprint $table) {
            $table->id();
            $table->string('identifier');
            $table->unsignedBigInteger('user_id');
            $table->decimal('request_amount', 28, 8);
            $table->string('request_currency');
            $table->decimal('exchange_rate', 28, 8);
            $table->decimal('percent_charge', 28, 8);
            $table->decimal('fixed_charge', 28, 8);
            $table->decimal('total_charge', 28, 8);
            $table->decimal('total_payable', 28, 8)->nullable();
            $table->string('link')->nullable();
            $table->string('remark')->nullable();
            $table->tinyInteger('status');
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
        Schema::dropIfExists('request_money');
    }
};
