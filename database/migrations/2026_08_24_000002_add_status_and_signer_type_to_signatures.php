<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signatures', function (Blueprint $table) {
            $table->string('signer_type', 20)->default('other')->after('signer_name')->comment('签收人类型：merchant/driver/other');
            $table->tinyInteger('status')->unsigned()->default(1)->after('signer_type')->comment('状态：1待签收，2已签收');
            $table->softDeletes()->after('signed_at');
        });
    }

    public function down(): void
    {
        Schema::table('signatures', function (Blueprint $table) {
            $table->dropColumn(['signer_type', 'status']);
            $table->dropSoftDeletes();
        });
    }
};
