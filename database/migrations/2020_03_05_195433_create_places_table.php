<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->bigIncrements('idPlace');
            $table->unsignedBigInteger('idCity');
            $table->string('name',256);
            $table->string('address',512);
            $table->string('description',512);
        });
        Schema::table('places',function (Blueprint $table){
            $table->foreign('idCity')
                ->references('idCity')
                ->on('cities')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('places',function (Blueprint $table) {
            $table->dropForeign('places_idcity_foreign');
        });
        Schema::dropIfExists('places');
    }
}
