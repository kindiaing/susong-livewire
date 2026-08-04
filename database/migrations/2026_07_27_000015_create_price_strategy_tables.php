<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 价格策略主表
        Schema::create('price_strategies', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('name', 100)->comment('策略名称');
            $table->string('code', 50)->nullable()->unique()->comment('策略编码');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('类型：1促销，2临时改价');
            $table->tinyInteger('target_type')->unsigned()->default(1)->comment('作用对象：1供应商，2商家，3全部');
            $table->tinyInteger('scope')->unsigned()->default(3)->comment('作用范围：1采购，2销售，3通用');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->tinyInteger('approval_status')->unsigned()->default(1)->comment('审核状态：1待审核，2已通过，3已拒绝');
            $table->timestamp('start_at')->nullable()->comment('生效开始时间');
            $table->timestamp('end_at')->nullable()->comment('生效结束时间');
            $table->unsignedBigInteger('created_by')->nullable()->comment('创建人ID');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('target_type');
            $table->index('status');
            $table->index('approval_status');
            $table->index(['start_at', 'end_at']);
            $table->comment('价格策略主表');
        });

        // 价格策略明细表
        Schema::create('price_strategy_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('price_strategy_id')->comment('价格策略ID');
            $table->unsignedBigInteger('target_id')->default(0)->comment('作用对象ID：supplier_id/merchant_id，0表示全部');
            $table->unsignedBigInteger('category_id')->nullable()->comment('商品分类ID');
            $table->unsignedBigInteger('product_id')->nullable()->comment('商品ID');
            $table->unsignedBigInteger('sku_id')->nullable()->comment('SKU ID');
            $table->tinyInteger('price_type')->unsigned()->default(1)->comment('价格类型：1固定价，2折扣率，3成本加权');
            $table->bigInteger('price_value')->default(0)->comment('固定价格');
            $table->integer('discount_rate')->default(10000)->comment('折扣率%');
            $table->integer('cost_weight_rate')->default(10000)->comment('成本加权率%');
            $table->bigInteger('min_quantity')->default(0)->comment('最小起量');
            $table->timestamp('effective_start_at')->nullable()->comment('明细生效开始时间');
            $table->timestamp('effective_end_at')->nullable()->comment('明细生效结束时间');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamps();

            $table->index('price_strategy_id');
            $table->index('target_id');
            $table->index('category_id');
            $table->index('product_id');
            $table->index('sku_id');
            $table->index(['effective_start_at', 'effective_end_at']);
            $table->comment('价格策略明细表');
        });

        // 改价/促销记录表
        Schema::create('price_change_logs', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->tinyInteger('source_type')->unsigned()->default(1)->comment('来源：1促销，2临时改价，3手动改价');
            $table->unsignedBigInteger('source_id')->nullable()->comment('来源策略ID');
            $table->tinyInteger('target_type')->unsigned()->default(1)->comment('作用单据：1订单，2采购单，3应收，4应付');
            $table->unsignedBigInteger('target_id')->comment('单据ID');
            $table->unsignedBigInteger('target_item_id')->nullable()->comment('单据明细ID');
            $table->bigInteger('original_price')->default(0)->comment('改价前单价');
            $table->bigInteger('new_price')->default(0)->comment('改价后单价');
            $table->bigInteger('quantity')->default(0)->comment('数量');
            $table->bigInteger('amount_diff')->default(0)->comment('金额差异');
            $table->unsignedBigInteger('operator_id')->nullable()->comment('操作人ID');
            $table->json('role_ids')->nullable()->comment('操作人角色ID数组');
            $table->string('reason', 255)->nullable()->comment('改价原因');
            $table->json('before_data')->nullable()->comment('改价前数据');
            $table->json('after_data')->nullable()->comment('改价后数据');
            $table->timestamp('created_at')->nullable()->comment('创建时间');

            $table->index('source_type');
            $table->index('source_id');
            $table->index('target_type');
            $table->index('target_id');
            $table->index('target_item_id');
            $table->index('operator_id');
            $table->comment('改价/促销记录表');
        });

        // ── 促销活动体系 ──────────────────────────

        // 促销活动主表（8种活动类型）
        Schema::create('promotions', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->string('name', 100)->comment('活动名称');
            $table->tinyInteger('promo_type')->unsigned()->default(1)->comment('促销类型：1普通促销，2满减，3优惠券，4组合套餐，5清仓临期，6拼团，7秒杀，8会员折扣');
            $table->string('promo_code', 50)->nullable()->unique()->comment('活动编码');
            $table->text('description')->nullable()->comment('活动描述');
            $table->tinyInteger('scope_type')->unsigned()->default(1)->comment('适用范围：1全场，2指定分类，3指定商品');
            $table->timestamp('start_at')->nullable()->comment('开始时间');
            $table->timestamp('end_at')->nullable()->comment('结束时间');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->unsignedBigInteger('created_by')->nullable()->comment('创建人ID');
            $table->timestamps();
            $table->softDeletes();

            $table->index('promo_type');
            $table->index('status');
            $table->index(['start_at', 'end_at']);
            $table->comment('促销活动主表');
        });

        // 活动商品明细表
        Schema::create('promotion_skus', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('promotion_id')->comment('促销活动ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->tinyInteger('price_type')->unsigned()->default(1)->comment('定价方式：1固定价，2折扣率');
            $table->bigInteger('fixed_price')->default(0)->comment('促销固定单价（厘）');
            $table->integer('discount_rate')->default(10000)->comment('折扣率（万分比）');
            $table->tinyInteger('second_price_type')->unsigned()->default(1)->comment('第二件定价：1无，2固定价，3折扣率');
            $table->bigInteger('second_fixed_price')->default(0)->comment('第二件固定单价（厘）');
            $table->integer('second_discount_rate')->default(10000)->comment('第二件折扣率（万分比）');
            $table->unsignedInteger('max_quantity')->default(0)->comment('限购数量');
            $table->unsignedInteger('max_per_customer')->default(0)->comment('每人限购');
            $table->unsignedInteger('stock_limit')->default(0)->comment('活动库存限量');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamps();

            $table->index('promotion_id');
            $table->index('sku_id');
            $table->index('status');
            $table->comment('活动商品明细表');
        });

        // 满减活动表
        Schema::create('promotion_full_reductions', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('promotion_id')->comment('促销活动ID');
            $table->tinyInteger('threshold_type')->unsigned()->default(1)->comment('门槛类型：1按金额，2按件数');
            $table->bigInteger('threshold_amount')->default(0)->comment('门槛金额/件数');
            $table->tinyInteger('reduction_type')->unsigned()->default(1)->comment('减免方式：1固定减，2折扣率，3赠品');
            $table->bigInteger('reduction_amount')->default(0)->comment('减免金额（厘）');
            $table->integer('discount_rate')->default(10000)->comment('折扣率（万分比）');
            $table->unsignedBigInteger('gift_sku_id')->nullable()->comment('赠品SKU ID');
            $table->unsignedInteger('gift_quantity')->default(0)->comment('赠品数量');
            $table->tinyInteger('is_stacked')->unsigned()->default(0)->comment('是否可叠加：0否，1是');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();

            $table->index('promotion_id');
            $table->comment('满减活动表');
        });

        // 优惠券表
        Schema::create('promotion_coupons', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('promotion_id')->comment('促销活动ID');
            $table->string('coupon_code', 50)->comment('优惠券编码');
            $table->string('name', 100)->comment('优惠券名称');
            $table->tinyInteger('coupon_type')->unsigned()->default(1)->comment('类型：1满减券，2折扣券，3抵扣券，4运费券');
            $table->bigInteger('threshold_amount')->default(0)->comment('使用门槛金额（厘）');
            $table->bigInteger('reduction_amount')->default(0)->comment('抵扣金额（厘）');
            $table->integer('discount_rate')->default(10000)->comment('折扣率（万分比）');
            $table->bigInteger('max_discount')->default(0)->comment('最大优惠上限（厘）');
            $table->unsignedInteger('total_quantity')->default(0)->comment('发放总量');
            $table->unsignedInteger('claimed_quantity')->default(0)->comment('已领取数量');
            $table->unsignedInteger('used_quantity')->default(0)->comment('已使用数量');
            $table->unsignedInteger('per_user_limit')->default(1)->comment('每人限领');
            $table->unsignedInteger('valid_days')->default(30)->comment('领取后有效天数');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamps();
            $table->softDeletes();

            $table->index('promotion_id');
            $table->index('coupon_code');
            $table->index('status');
            $table->comment('优惠券表');
        });

        // 组合套餐表
        Schema::create('promotion_bundles', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('promotion_id')->comment('促销活动ID');
            $table->string('bundle_name', 100)->comment('套餐名称');
            $table->bigInteger('bundle_price')->default(0)->comment('套餐价（厘）');
            $table->bigInteger('original_total')->default(0)->comment('原价合计（厘）');
            $table->unsignedInteger('bundle_quantity')->default(1)->comment('每组最低件数');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamps();

            $table->index('promotion_id');
            $table->comment('组合套餐表');
        });

        // 套餐明细表
        Schema::create('promotion_bundle_items', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('bundle_id')->comment('套餐ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->unsignedInteger('quantity')->default(1)->comment('数量');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();

            $table->index('bundle_id');
            $table->index('sku_id');
            $table->comment('套餐明细表');
        });

        // 清仓临期活动表
        Schema::create('promotion_clearances', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('promotion_id')->comment('促销活动ID');
            $table->tinyInteger('clearance_type')->unsigned()->default(1)->comment('类型：1清仓，2临期');
            $table->date('expiry_date')->nullable()->comment('临期截止日期');
            $table->integer('discount_rate')->default(10000)->comment('折扣率（万分比）');
            $table->bigInteger('fixed_price')->default(0)->comment('清仓固定价（厘）');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamps();

            $table->index('promotion_id');
            $table->comment('清仓临期活动表');
        });

        // 拼团活动表
        Schema::create('promotion_group_buys', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('promotion_id')->comment('促销活动ID');
            $table->bigInteger('group_price')->default(0)->comment('拼团价（厘）');
            $table->unsignedInteger('min_group_size')->default(2)->comment('最少成团人数');
            $table->unsignedInteger('max_group_size')->default(10)->comment('最多拼团人数');
            $table->unsignedInteger('time_limit')->default(1440)->comment('拼团时限（分钟）');
            $table->tinyInteger('virtual_join')->unsigned()->default(0)->comment('虚拟凑团：0否，1是');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamps();

            $table->index('promotion_id');
            $table->comment('拼团活动表');
        });

        // 秒杀活动表
        Schema::create('promotion_flash_sales', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('promotion_id')->comment('促销活动ID');
            $table->bigInteger('flash_price')->default(0)->comment('秒杀价（厘）');
            $table->unsignedInteger('total_stock')->default(0)->comment('秒杀总库存');
            $table->unsignedInteger('sold_stock')->default(0)->comment('已售库存');
            $table->unsignedInteger('per_user_limit')->default(1)->comment('每人限购');
            $table->timestamp('flash_start_at')->nullable()->comment('秒杀开始时间');
            $table->timestamp('flash_end_at')->nullable()->comment('秒杀结束时间');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamps();

            $table->index('promotion_id');
            $table->comment('秒杀活动表');
        });

        // 会员等级折扣表
        Schema::create('promotion_member_discounts', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('promotion_id')->default(0)->comment('促销活动ID（0=全局常驻规则）');
            $table->tinyInteger('member_level')->unsigned()->default(1)->comment('会员等级：1普通，2银卡，3金卡，4钻石');
            $table->integer('discount_rate')->default(10000)->comment('折扣率（万分比，9500=95折）');
            $table->tinyInteger('is_permanent')->unsigned()->default(0)->comment('是否常驻：0否，1是');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamps();

            $table->index('promotion_id');
            $table->index('member_level');
            $table->comment('会员等级折扣表');
        });

        // 门店差异化价格表
        Schema::create('store_sku_prices', function (Blueprint $table) {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('store_id')->comment('门店ID');
            $table->unsignedBigInteger('sku_id')->comment('SKU ID');
            $table->tinyInteger('price_type')->unsigned()->default(1)->comment('价格类型：1零售价上浮下调，2独立零售价，3会员价覆盖');
            $table->tinyInteger('adjust_mode')->unsigned()->default(1)->comment('调整方式：1固定金额，2百分比，3直接覆盖');
            $table->bigInteger('adjust_value')->default(0)->comment('调整值（金额=厘，百分比=万分比）');
            $table->tinyInteger('member_level')->unsigned()->default(0)->comment('会员等级：0不限定，1普通，2银卡，3金卡，4钻石');
            $table->timestamp('effective_at')->nullable()->comment('生效时间');
            $table->timestamp('expire_at')->nullable()->comment('失效时间');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('状态：0禁用，1启用');
            $table->timestamps();

            $table->index(['store_id', 'sku_id']);
            $table->index('status');
            $table->index(['effective_at', 'expire_at']);
            $table->comment('门店差异化价格表');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_sku_prices');
        Schema::dropIfExists('promotion_member_discounts');
        Schema::dropIfExists('promotion_flash_sales');
        Schema::dropIfExists('promotion_group_buys');
        Schema::dropIfExists('promotion_clearances');
        Schema::dropIfExists('promotion_bundle_items');
        Schema::dropIfExists('promotion_bundles');
        Schema::dropIfExists('promotion_coupons');
        Schema::dropIfExists('promotion_full_reductions');
        Schema::dropIfExists('promotion_skus');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('price_change_logs');
        Schema::dropIfExists('price_strategy_items');
        Schema::dropIfExists('price_strategies');
    }
};
