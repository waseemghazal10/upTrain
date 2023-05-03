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
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->String('tPhone_number');
            $table->String('tPhoto')->default("");
            $table->timestamps();

            $table->unsignedBigInteger('user_id')->nullable();;
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('company_id')->nullable();;
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trainers');
    }
};
