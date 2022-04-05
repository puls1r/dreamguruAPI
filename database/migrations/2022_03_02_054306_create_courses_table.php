<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')->references('id')->on('users');
            $table->string('title')->unique();
            $table->integer('price')->unsigned();
            $table->text('desc');
            $table->string('level');        //beginner, intermediate, advanced
            $table->string('thumbnail');
            $table->string('hero_background');
            $table->time('estimated_time');
            $table->string('trailer')->nullable();
            $table->string('language');
            $table->string('status');       //completed, draft, archived
            $table->boolean('is_on_discount');
            $table->integer('discount_price')->unsigned()->nullable();
            $table->string('slug')->unique();
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
        Schema::dropIfExists('courses');
    }
}
