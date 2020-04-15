<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTurnsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('turns', function (Blueprint $table) {
            $table->bigInteger('winning_play_id')->nullable()->unsigned();
            $table->foreign('winning_play_id')->references('id')->on('plays')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('turns', function (Blueprint $table) {
            $table->dropForeign('turns_winning_play_id_foreign');
            $table->dropColumn('winning_play_id');
        });
    }
}
