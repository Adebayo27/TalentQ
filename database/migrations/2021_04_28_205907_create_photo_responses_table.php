<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotoResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photo_responses', function (Blueprint $table) {
            $table->id();
            $table->integer('photo_request_id');
            $table->integer('user_id');
            $table->string('thumbnail')->nullable();
            $table->string('high_resolution')->nullable();
            $table->longText('comment')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('photo_responses');
    }
}
