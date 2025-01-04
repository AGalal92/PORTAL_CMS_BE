<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('category_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable(); // Custom name for the category
            $table->text('description')->nullable(); // Additional description
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_details');
    }
}
