<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 财务对账测试数据 Seeder
 *
 * 包含：充值记录、供应商结算（含明细+付款）、应收账款（含收款）、发票、授权更正
 */
class FinanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRecharges();
        $this->seedSupplierSettlements();
        $this->seedReceivables();
        $this->seedInvoices();
        $this->seedCorrectionAuthorizations();
    }

    protected function seedRecharges(): void
    {
        $now = now();
        $operatorUser = DB::table('users')->where('username', 'operator1')->first();

        $recharges = [
            ['merchant' => '味之初餐饮店', 'amount' => 5000000, 'payment_method' => 3, 'transaction_no' => 'RCH-20260720-001', 'status' => 2, 'approval_status' => 2],
            ['merchant' => '家常菜馆',    'amount' => 3000000, 'payment_method' => 2, 'transaction_no' => 'RCH-20260722-001', 'status' => 2, 'approval_status' => 2],
            ['merchant' => '鲜之味快餐店', 'amount' => 10000000, 'payment_method' => 3, 'transaction_no' => 'RCH-20260725-001', 'status' => 1, 'approval_status' => 1],
        ];

        foreach ($recharges as $item) {
            $merchant = DB::table('merchants')->where('name', $item['merchant'])->first();
            if (! $merchant) continue;
            if (DB::table('recharges')->where('transaction_no', $item['transaction_no'])->exists()) continue;

            DB::table('recharges')->insert([
                'merchant_id' => $merchant->id, 'amount' => $item['amount'],
                'payment_method' => $item['payment_method'], 'transaction_no' => $item['transaction_no'],
                'status' => $item['status'], 'approval_status' => $item['approval_status'],
                'operator_id' => $operatorUser?->id, 'remark' => '示例充值',
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    protected function seedSupplierSettlements(): void
    {
        $now = now();
        $supplierGreen = DB::table('suppliers')->where('name', '绿野蔬菜种植基地')->first();
        $supplierMeat = DB::table('suppliers')->where('name', '丰润肉业有限公司')->first();
        $po1 = DB::table('purchase_orders')->where('order_no', 'PO-20260725-001')->first();

        if ($supplierGreen && ! DB::table('supplier_settlements')->where('settlement_no', 'SS-20260728-001')->exists()) {
            $settlementId = DB::table('supplier_settlements')->insertGetId([
                'settlement_no' => 'SS-20260728-001', 'supplier_id' => $supplierGreen->id,
                'start_date' => '2026-07-01', 'end_date' => '2026-07-28',
                'total_amount' => 440000, 'service_fee' => 5000, 'payable_amount' => 435000,
                'return_amount' => 0, 'paid_amount' => 435000, 'status' => 3,
                'settled_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ]);

            if ($po1) {
                DB::table('supplier_settlement_items')->insert([
                    'supplier_settlement_id' => $settlementId, 'purchase_order_id' => $po1->id,
                    'amount' => 440000, 'created_at' => $now, 'updated_at' => $now,
                ]);

                DB::table('settlement_payments')->insert([
                    'settlement_id' => $settlementId, 'amount' => 435000,
                    'payment_method' => 1, 'transaction_no' => 'PAY-20260728-001',
                    'operator_id' => DB::table('users')->where('username', 'cashier1')->value('id'),
                    'approval_status' => 2, 'remark' => '银行转账付款',
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        if ($supplierMeat && ! DB::table('supplier_settlements')->where('settlement_no', 'SS-20260728-002')->exists()) {
            DB::table('supplier_settlements')->insert([
                'settlement_no' => 'SS-20260728-002', 'supplier_id' => $supplierMeat->id,
                'start_date' => '2026-07-01', 'end_date' => '2026-07-28',
                'total_amount' => 260000, 'service_fee' => 3000, 'payable_amount' => 257000,
                'return_amount' => 0, 'paid_amount' => 0, 'status' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    protected function seedReceivables(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();
        $order2 = DB::table('orders')->where('order_no', 'ORD-20260728-002')->first();

        if ($order1 && ! DB::table('receivables')->where('receivable_no', 'RCV-20260728-001')->exists()) {
            $rcvId = DB::table('receivables')->insertGetId([
                'receivable_no' => 'RCV-20260728-001', 'order_id' => $order1->id,
                'merchant_id' => $order1->merchant_id, 'original_amount' => 23000,
                'adjusted_amount' => 23000, 'discrepancy_amount' => 0, 'return_amount' => 0,
                'strategy_discount_amount' => 0, 'received_amount' => 23000, 'status' => 3,
                'settlement_type' => 1, 'settled_at' => $now, 'approval_status' => 2,
                'created_at' => $now, 'updated_at' => $now,
            ]);

            DB::table('receivable_payments')->insert([
                'receivable_id' => $rcvId, 'amount' => 23000,
                'payment_method' => 1, 'transaction_no' => 'RP-20260728-001',
                'operator_id' => DB::table('users')->where('username', 'cashier1')->value('id'),
                'approval_status' => 2, 'remark' => '余额扣款',
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        if ($order2 && ! DB::table('receivables')->where('receivable_no', 'RCV-20260728-002')->exists()) {
            DB::table('receivables')->insert([
                'receivable_no' => 'RCV-20260728-002', 'order_id' => $order2->id,
                'merchant_id' => $order2->merchant_id, 'original_amount' => 74500,
                'adjusted_amount' => 74500, 'discrepancy_amount' => 0, 'return_amount' => 0,
                'strategy_discount_amount' => 0, 'received_amount' => 0, 'status' => 1,
                'settlement_type' => 2, 'due_date' => now()->addDays(15)->toDateString(),
                'approval_status' => 2, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    protected function seedInvoices(): void
    {
        $now = now();
        $merchant1 = DB::table('merchants')->where('name', '味之初餐饮店')->first();

        if ($merchant1 && ! DB::table('invoices')->where('invoice_no', 'INV-20260728-001')->exists()) {
            DB::table('invoices')->insert([
                'invoice_no' => 'INV-20260728-001', 'type' => 1,
                'target_id' => $merchant1->id, 'title' => '味之初餐饮店',
                'amount' => 23000, 'file_url' => '/uploads/invoices/demo-inv-001.pdf',
                'status' => 2, 'applied_at' => $now, 'issued_at' => $now,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    protected function seedCorrectionAuthorizations(): void
    {
        $now = now();
        $order1 = DB::table('orders')->where('order_no', 'ORD-20260728-001')->first();
        $operatorUser = DB::table('users')->where('username', 'operator1')->first();

        if ($order1 && ! DB::table('correction_authorizations')->where('order_id', $order1->id)->exists()) {
            DB::table('correction_authorizations')->insert([
                'order_id' => $order1->id, 'operator_id' => $operatorUser?->id ?? 1,
                'reason' => '订单金额需要调整',
                'before_data' => json_encode(['final_amount' => 23000]),
                'after_data' => json_encode(['final_amount' => 19000]),
                'authorized_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
