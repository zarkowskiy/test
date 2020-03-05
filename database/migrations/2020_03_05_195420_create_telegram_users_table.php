<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->unsignedBigInteger('idTelegramUser')->primary()->unique();
            $table->unsignedBigInteger('idCity')->nullable();
            $table->string('username',256)->nullable();
            $table->string('lastname',256)->nullable();
            $table->string('firstname',256)->nullable();
        });
        Schema::table('telegram_users',function (Blueprint $table){
            $table->foreign('idCity')
                ->references('idCity')
                ->on('cities')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telegram_users',function (Blueprint $table) {
            $table->dropForeign('telegram_users_idcity_foreign');
        });
        Schema::dropIfExists('telegram_users');
    }
}
