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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->String('pTitle');
            $table->date('pStart_date');
            $table->date('pEnd_date');
            $table->String('pPhoto')->default("");
            $table->text('pDetails')->default("");

            $table->unsignedBigInteger('branch_id')->nullable();;
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');

            $table->unsignedBigInteger('company_id')->nullable();;
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');

            $table->unsignedBigInteger('trainer_id')->nullable();;
            $table->foreign('trainer_id')->references('id')->on('trainers')->onDelete('set null');

            $table->unsignedBigInteger('field_id')->nullable();;
            $table->foreign('field_id')->references('id')->on('fields')->onDelete('set null');


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
        Schema::dropIfExists('programs');
    }
};
