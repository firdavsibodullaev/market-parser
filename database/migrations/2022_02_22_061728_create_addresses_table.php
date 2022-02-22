<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('region', 150)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('street', 255);
            $table->string('house', 50)->nullable();
            $table->string('post', 10)->nullable();
            $table->decimal('lon', 18, 8)->nullable();
            $table->decimal('lat', 18, 8)->nullable();
            $table->string('namebrand', 150)->nullable();
            $table->string('typeshop', 50)->nullable();
            $table->string('tc', 15)->default('Нет');
            $table->string('uin');
            $table->double('sqTorg')->nullable();
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
        Schema::dropIfExists('addresses');
    }
}
