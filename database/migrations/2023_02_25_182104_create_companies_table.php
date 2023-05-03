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
            $table->string('cDescription')->default("");
            $table->string('cWebSite');
            $table->string('cLocation');
            $table->string('verification_token') -> default("");
            $table->rememberToken();
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
