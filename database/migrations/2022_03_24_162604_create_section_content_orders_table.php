<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionContentOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_content_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_section_id');
            $table->foreign('course_section_id')->references('id')->on('course_sections');
            $table->string('content_id');
            $table->unsignedInteger('order');
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
        Schema::dropIfExists('section_content_orders');
    }
}
