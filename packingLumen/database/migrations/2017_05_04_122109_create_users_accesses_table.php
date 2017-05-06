<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_accesses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('company_read');
            $table->tinyInteger('company_write');
            $table->tinyInteger('machines_read');
            $table->tinyInteger('machines_write');
            $table->tinyInteger('workers_read');
            $table->tinyInteger('workers_write');
            $table->tinyInteger('notes_read');
            $table->tinyInteger('notes_write');
            $table->tinyInteger('meetings_read');
            $table->tinyInteger('meetings_write');
            $table->timestamps();
        });

        DB::table('users_accesses')->insert(
            array(
                ['user_id' => 1, 'company_read' => 1, 'company_write' => 1, 'machines_read' => 1, 'machines_write' => 1, 'workers_read' => 1
                    , 'workers_write' => 1, 'notes_read' => 1, 'notes_write' => 1, 'meetings_read' => 1, 'meetings_write' => 1])
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_accesses');
    }
}
