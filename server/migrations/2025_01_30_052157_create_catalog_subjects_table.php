<?php

use Fleetbase\Support\Utils;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $databaseName = Utils::getFleetbaseDatabaseName();

        Schema::connection(config('storefront.connection.db'))->create('catalog_subjects', function (Blueprint $table) use ($databaseName) {
            $table->bigIncrements('id');
            // No literal default: Str::uuid() here was evaluated once at migration time (same value for every row,
            // different per install). HasUuid generates each uuid; a raw insert without one now fails explicitly.
            $table->uuid('uuid')->unique();
            $table->foreignUuid('catalog_uuid')
                ->references('uuid')
                ->on('catalogs')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->string('subject_type');
            $table->uuid('subject_uuid');
            $table->foreignUuid('company_uuid')
                ->nullable()
                ->references('uuid')
                ->on(new Expression($databaseName . '.companies'))
                ->onUpdate('CASCADE')
                ->onDelete('SET NULL');

            $table->foreignUuid('created_by_uuid')
                ->nullable()
                ->references('uuid')
                ->on(new Expression($databaseName . '.users'))
                ->onUpdate('CASCADE')
                ->onDelete('SET NULL');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['subject_type', 'subject_uuid'], 'catalog_subject_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(config('storefront.connection.db'))->dropIfExists('catalog_subjects');
    }
};
