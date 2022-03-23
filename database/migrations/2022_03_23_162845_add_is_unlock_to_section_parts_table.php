<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsUnlockToSectionPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section_parts', function (Blueprint $table) {
            $table->boolean('is_unlock')->after('status');       //untuk parts yang dibuka secara gratis
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('section_parts', function (Blueprint $table) {
            //
        });
    }
}
