<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConnectionAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connection_attempts', function (Blueprint $table) {
            $table->id();

            $table->timestampTz('time_execution_started');
            $table->timestampTz('time_execution_finished');
            $table->string('time_diff');
            $table->string('status');
            $table->integer('data_transferred')->nullable();
            $table->string('url_requested');
            $table->string('error_msg')->nullable();

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('connection');
    }
}
