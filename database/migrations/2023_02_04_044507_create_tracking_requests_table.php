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
        Schema::create('tracking_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracking_id')
                ->constrained('tracking')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('tracking_item_id')
                ->constrained('tracking_items')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->nullableMorphs('user');
            $table->string('ip')
                ->index();
            $table->text('ips');
            $table->boolean('trusted_proxy');
            $table->string('user_agent')
                ->index();
            $table->timestamps();

            $table->index(['tracking_id', 'tracking_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracking_requests');
    }
};
