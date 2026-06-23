<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add soft deletes to food_items
        Schema::table('food_items', function (Blueprint $table) {
            if (!Schema::hasColumn('food_items', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Modify food_item_id to nullable using raw SQL
        DB::statement('ALTER TABLE sale_items DROP FOREIGN KEY IF EXISTS sale_items_food_item_id_foreign');
        DB::statement('ALTER TABLE sale_items MODIFY food_item_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE sale_items ADD CONSTRAINT sale_items_food_item_id_foreign 
                      FOREIGN KEY (food_item_id) REFERENCES food_items(id) ON DELETE SET NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_items', function (Blueprint $table) {
            if (Schema::hasColumn('food_items', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        DB::statement('ALTER TABLE sale_items DROP FOREIGN KEY IF EXISTS sale_items_food_item_id_foreign');
        DB::statement('ALTER TABLE sale_items MODIFY food_item_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE sale_items ADD CONSTRAINT sale_items_food_item_id_foreign 
                      FOREIGN KEY (food_item_id) REFERENCES food_items(id) ON DELETE CASCADE');
    
    }
};
