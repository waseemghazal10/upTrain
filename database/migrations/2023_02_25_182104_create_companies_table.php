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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('cName');
            $table->string('cEmail')->unique();
            $table->string('cPassword');
            $table->String('cPhoto')->default("");
            $table->String('cPhone_number')->default("");
            $table->text('cDescription')->default("");
            $table->string('cWebSite');
            $table->string('verification_token') -> default("");
            $table->rememberToken();

            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');

            $table->unsignedBigInteger('field_id')->nullable();
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
        Schema::dropIfExists('companies');
    }
};
