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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('taStatus');
            $table->string('taTitle');
            $table->text('taDescription')->default("");
            $table->date('taStart_date');
            $table->date('taEnd_date');

            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->foreign('trainer_id')->references('id')->on('trainers')->onDelete('set null');

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
        Schema::dropIfExists('tasks');
    }
};
