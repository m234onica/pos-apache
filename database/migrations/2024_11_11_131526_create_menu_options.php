<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('選項名稱');
            $table->integer('price')->comment('加購價格');
            $table->enum('type', ['BASIC', 'CLUB', 'RICE', 'SPICY', 'ADD', 'SIZE', 'HEAT', 'ADVANCED', 'RICE_ADVANCED'])
                ->default('BASIC')->comment('類型');
            $table->boolean('status')->default(false)->comment('狀態, true:上架, false:下架');
            $table->timestamps();
        });

        Schema::create('menu_option_refs', function (Blueprint $table) {
            $table->increments('id');

            // 將 menu_id 和 menu_option_id 定義為 unsignedInteger
            $table->unsignedInteger('menu_id');
            $table->foreign('menu_id')->references('id')->on('menu')->onDelete('cascade');

            $table->unsignedInteger('menu_option_id');
            $table->foreign('menu_option_id')->references('id')->on('menu_options')->onDelete('cascade');

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
        Schema::dropIfExists('menu_option_refs');
        Schema::dropIfExists('menu_options');
    }
}
