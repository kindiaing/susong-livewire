<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sku_barcodes', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->unsignedBigInteger('supplier_id')->nullable()->comment('供应商ID，供应商条码时必填');
            $table->tinyInteger('barcode_type')->unsigned()->default(1)->comment('条码类型：1厂家条码，2供应商条码，3内部条码，4备用条码');
            $table->string('barcode_code', 50)->comment('条码值');
            $table->tinyInteger('is_default')->unsigned()->default(0)->comment('是否默认条码：0否，1是');
            $table->tinyInteger('is_enabled')->unsigned()->default(1)->comment('是否启用：0禁用，1启用');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->index('sku_id');
            $table->index('supplier_id');
            $table->index('barcode_type');
            $table->index('barcode_code');
            $table->unique(['sku_id', 'supplier_id', 'barcode_type', 'barcode_code'], 'uk_sku_supplier_type_code');
            $table->comment('SKU条码表');
        });

        Schema::create('sku_suppliers', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->unsignedBigInteger('supplier_id')->comment('供应商ID');
            $table->tinyInteger('is_default')->unsigned()->default(0)->comment('是否默认供应商：0否，1是');
            $table->bigInteger('purchase_price')->default(0)->comment('该供应商采购参考价');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('是否启用：0禁用，1启用');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['sku_id', 'supplier_id'], 'uk_sku_supplier');
            $table->index('supplier_id');
            $table->index('status');
            $table->comment('SKU供应商关联表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sku_suppliers');
        Schema::dropIfExists('sku_barcodes');
    }
};
