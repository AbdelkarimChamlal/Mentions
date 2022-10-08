<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMentionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mentions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('platform')->nullable();
            $table->text('platform_id')->nullable();
            $table->mediumText('content')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->text('url')->nullable();
            $table->text('sender_name')->nullable();
            $table->text('sender_url')->nullable();
            $table->text('sender_avatar')->nullable();
            $table->text('sender_username')->nullable();
            $table->mediumText('custom_data')->nullable();
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
        Schema::dropIfExists('mentions');
    }
}
