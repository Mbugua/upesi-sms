<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInboxcolumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $schema::create('inbox',function(Blueprint $table){
            $table->addColumn()->string('date');
            $table->addColumn()->string('from');
            $table->addColumn()->string('messageid');
            $table->addColumn()->string('linkid');
            $table->addColumn()->text('message');
            $table->addColumn()->string('to');
            $table->addColumn()->string('networkcode');
            $tabl->addColumn()->string('network');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $schema::dropIfExists('inbox');
    }
}
