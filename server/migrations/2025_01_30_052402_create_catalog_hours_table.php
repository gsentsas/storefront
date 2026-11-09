<?php

use Fleetbase\Support\Utils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $databaseName = Utils::getFleetbaseDatabaseName();

        Schema::connection(config('storefront.connection.db'))->create('catalog_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            // No literal default: Str::uuid() here was evaluated once at migration time (same value for every row,
            // different per install). HasUuid generates each uuid; a raw insert without one now fails explicitly.
            $table->uuid('uuid')->unique();
            $table->foreignUuid('catalog_uuid')
                ->references('uuid')->on('catalogs')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->string('day_of_week');
            $table->time('start')->nullable();
            $table->time('end')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(config('storefront.connection.db'))->dropIfExists('catalog_hours');
    }
};
