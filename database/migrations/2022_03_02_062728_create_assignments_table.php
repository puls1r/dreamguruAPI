<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_section_id');
            $table->foreign('course_section_id')->references('id')->on('course_sections');
            $table->string('title');
            $table->text('instruction');
            $table->string('picture')->nullable();
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
        Schema::dropIfExists('assignments');
    }
}
