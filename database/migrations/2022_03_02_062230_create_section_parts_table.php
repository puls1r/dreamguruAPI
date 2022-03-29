<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_parts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_section_id');
            $table->foreign('course_section_id')->references('id')->on('course_sections');
            $table->string('title');
            $table->text('text')->nullable();
            $table->string('picture')->nullable();
            $table->string('video')->nullable();
            $table->unsignedInteger('estimated_time')->nullable();
            $table->boolean('is_unlock');       //untuk parts yang dibuka secara gratis
            $table->string('slug')->unique()->nullable();
            $table->string('status');       //completed, draft, archived
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
        Schema::dropIfExists('section_parts');
    }
}
