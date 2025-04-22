<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeImageDataColumnTypeToText extends Migration
{
    public function up()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->text('image_data')->change(); // Меняем binary на text
        });
    }

    public function down()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->binary('image_data')->change();
        });
    }
}
