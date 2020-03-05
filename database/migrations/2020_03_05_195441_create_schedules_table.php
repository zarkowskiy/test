<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->bigIncrements('idSchedule');
            $table->unsignedBigInteger('idPlace');
            $table->text('scheduledJSON');
        });
        Schema::table('schedules',function (Blueprint $table){
            $table->foreign('idPlace')
                ->references('idPlace')
                ->on('places')
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
        Schema::table('schedules',function (Blueprint $table) {
            $table->dropForeign('schedules_idplace_foreign');
        });
        Schema::dropIfExists('schedules');
    }
}
