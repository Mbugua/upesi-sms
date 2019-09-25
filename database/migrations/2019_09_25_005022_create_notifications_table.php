<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phoneNumber');
            $table->string('failureReason');
            $table->integer('retryCount')->nullable();
            $table->string('messageID');
            $table->string('status');
            $table->string('networkCode');
            $table->string('network');
            $table->timestamps();

            $table->index(['phoneNumber']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification');
    }
}
