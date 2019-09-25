<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutboxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outbox', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference');
            $table->string('to');
            $table->text('message');
            $table->string('from');
            //update message id after sending outboxsms
            $table->string('messageID')->nullable();
            $table->string('retries');
            $table->float('cost',8,2)->nullable();
            $table->timestamps();

            $table->index(['reference']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oubox');
    }
}
