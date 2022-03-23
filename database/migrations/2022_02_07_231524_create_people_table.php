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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('name',30);
            $table->string('lastNameP',30);
            $table->string('lastNameM',30);
            $table->string('gender',9);
            $table->string('bloodGroup',2);
            $table->string('rhFactor',8);
            $table->date('birthDate');
            $table->string('phone',15);
            $table->string('street',25);
            $table->string('avenue',25);
            $table->string('postalCode',5);
            $table->string('photo',60);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
};
