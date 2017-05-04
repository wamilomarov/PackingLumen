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
            $table->integer('table'); // 1 company read, 2 company write, 3 machines read, 4 machines write, 5 workers read, 6 workers write
                                      // 7 notes read, 8 notes write, 9 meetings read, 10 meetings write
            $table->timestamps();
        });

        DB::table('users_accesses')->insert(
            array(
                ['user_id' => 1, 'table' => 1],
                ['user_id' => 1, 'table' => 2],
                ['user_id' => 1, 'table' => 3],
                ['user_id' => 1, 'table' => 4],
                ['user_id' => 1, 'table' => 5],
                ['user_id' => 1, 'table' => 6],
                ['user_id' => 1, 'table' => 7],
                ['user_id' => 1, 'table' => 8],
                ['user_id' => 1, 'table' => 9],
                ['user_id' => 1, 'table' => 10])
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
