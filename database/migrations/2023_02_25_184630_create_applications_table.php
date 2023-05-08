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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->integer('status') -> default(0);
            $table->String('cv')->default("");
            $table->String('details')->default("");
            $table->timestamps();

            $table->unsignedBigInteger('program_id')->nullable();;
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('set null');

            $table->unsignedBigInteger('student_id')->nullable();;
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
};
