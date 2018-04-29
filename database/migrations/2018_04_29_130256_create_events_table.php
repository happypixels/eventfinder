<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('venue_id')->unsigned();
            $table->integer('agent_id')->unsigned();
            $table->string('agent_event_id');
            $table->string('title');
            $table->longtext('description')->nullable();
            $table->string('url')->nullable();
            $table->string('image')->nullable();
            $table->string('image_url')->nullable();
            $table->decimal('min_price', 8, 2)->nullable();
            $table->decimal('max_price', 8, 2)->nullable();
            $table->boolean('is_cancelled')->default(0);
            $table->boolean('is_sold_out')->default(0);
            $table->dateTime('event_starts_at')->nullable();
            $table->dateTime('sale_starts_at')->nullable();
            $table->dateTime('sale_ends_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['agent_id', 'agent_event_id']);
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function ($table) {
            $table->dropForeign('events_venue_id_foreign');
            $table->dropForeign('events_agent_id_foreign');
        });

        Schema::dropIfExists('events');
    }
}
