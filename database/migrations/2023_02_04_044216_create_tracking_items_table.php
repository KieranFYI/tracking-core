<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_items', function (Blueprint $table) {
            $table->id();
            $table->string('hash')
                ->index();
            $table->foreignId('tracking_id')
                ->constrained('tracking')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->nullableMorphs('user');
            $table->nullableMorphs('created_by');
            $table->morphs('trackable');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracking_items');
    }
};
