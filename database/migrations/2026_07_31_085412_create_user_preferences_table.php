<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->string('pref_key', 100)->comment('偏好键（如 col_vis_Org_SupplierList）');
            $table->json('pref_value')->nullable()->comment('偏好值（JSON）');
            $table->timestamps();

            $table->unique(['user_id', 'pref_key']);
            $table->index('user_id');
            $table->comment('用户偏好表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
