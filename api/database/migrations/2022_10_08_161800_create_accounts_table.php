<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('platform')->nullable();
            $table->text('platform_id')->nullable();
            $table->text('username')->nullable();
            $table->text('email')->nullable();
            $table->text('profile_link')->nullable();
            $table->text('avatar')->nullable();
            $table->mediumText('access_data')->nullable();
            $table->text('access_data_last_updated')->nullable();
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
        Schema::dropIfExists('accounts');
    }
}
