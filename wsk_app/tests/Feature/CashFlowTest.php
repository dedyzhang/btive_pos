<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CashFlowAccount;
use App\Models\CashFlowTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CashFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'http://localhost']);
    }

    /**
     * Test admin can access cash flow dashboard.
     */
    public function test_admin_can_access_cash_flow_dashboard()
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'Admin Keuangan',
            'role' => 'admin',
            'username' => 'admin_flow',
            'password' => Hash::make('password123'),
        ]);

        // 2. Access dashboard
        $response = $this->actingAs($admin)->get('/cashflow');

        $response->assertStatus(200);
        $response->assertSee('Saldo Kumulatif');
        $response->assertSee('Daftar Akun Keuangan');
    }

    /**
     * Test cashier or other roles are forbidden.
     */
    public function test_non_admin_cannot_access_cash_flow_dashboard()
    {
        // 1. Create Cashier
        $cashier = User::create([
            'name' => 'Kasir Toko',
            'role' => 'kasir',
            'username' => 'kasir_flow',
            'password' => Hash::make('password123'),
        ]);

        // 2. Access dashboard
        $response = $this->actingAs($cashier)->get('/cashflow');

        $response->assertStatus(403);
    }

    /**
     * Test admin can manage accounts and transactions.
     */
    public function test_admin_can_create_cash_flow_account_and_transaction()
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'Admin Keuangan',
            'role' => 'admin',
            'username' => 'admin_flow2',
            'password' => Hash::make('password123'),
        ]);

        // 2. Store account via post request
        $response = $this->actingAs($admin)->post('/cashflow/accounts', [
            'name' => 'KAS KECIL TOKO',
            'account_number' => null,
            'initial_balance' => 1000000,
            'description' => 'Kas kecil untuk pengeluaran darurat',
            'icon' => 'fa-money-bill-transfer',
            'color' => '#10b981'
        ]);

        $response->assertRedirect('/cashflow');
        $this->assertDatabaseHas('cash_flow_accounts', [
            'name' => 'KAS KECIL TOKO',
            'initial_balance' => 1000000,
            'icon' => 'fa-money-bill-transfer',
            'color' => '#10b981'
        ]);

        // Get the created account uuid
        $account = CashFlowAccount::where('name', 'KAS KECIL TOKO')->first();

        // 3. Store a transaction
        $responseTransaction = $this->actingAs($admin)->post('/cashflow/transactions', [
            'account_id' => $account->uuid,
            'type' => 'expense',
            'amount' => 150000,
            'transaction_date' => date('Y-m-d'),
            'description' => 'Membeli perlengkapan kebersihan toko',
            'reference' => 'REF-001'
        ]);

        $responseTransaction->assertRedirect('/cashflow');
        $this->assertDatabaseHas('cash_flow_transactions', [
            'account_id' => $account->uuid,
            'type' => 'expense',
            'amount' => 150000,
            'reference' => 'REF-001'
        ]);
    }

    /**
     * Test sales reconciliation with operational expense deduction.
     */
    public function test_admin_can_reconcile_with_operational_expense()
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'Admin Keuangan',
            'role' => 'admin',
            'username' => 'admin_flow_recon',
            'password' => Hash::make('password123'),
        ]);

        // 2. Create Account
        $account = CashFlowAccount::create([
            'name' => 'KAS TOKO UTAMA',
            'initial_balance' => 0
        ]);

        // 3. Store a sales reconciliation transaction with operational expense
        $response = $this->actingAs($admin)->post('/cashflow/transactions', [
            'account_id' => $account->uuid,
            'type' => 'income',
            'amount' => 850000,
            'operational_expense' => 150000,
            'transaction_date' => date('Y-m-d'),
            'description' => 'Rekonsiliasi Omzet Penjualan',
            'reference' => 'RECON-001',
            'is_sales_reconciliation' => 1,
            'reconciliation_date' => date('Y-m-d')
        ]);

        $response->assertRedirect('/cashflow');
        $this->assertDatabaseHas('cash_flow_transactions', [
            'account_id' => $account->uuid,
            'type' => 'income',
            'amount' => 850000,
            'operational_expense' => 150000,
            'is_sales_reconciliation' => 1,
            'reconciliation_date' => date('Y-m-d')
        ]);
    }

    /**
     * Test admin can create expense transaction with detailed shopping items list.
     */
    public function test_admin_can_create_expense_transaction_with_shopping_items()
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'Admin Keuangan',
            'role' => 'admin',
            'username' => 'admin_flow_items',
            'password' => Hash::make('password123'),
        ]);

        // 2. Create Account
        $account = CashFlowAccount::create([
            'name' => 'KAS TOKO UTAMA',
            'initial_balance' => 5000000
        ]);

        // 3. Post transaction with items list
        $itemsData = [
            [
                'name' => 'Gas Melon 3kg',
                'qty' => 2,
                'price' => 22000
            ],
            [
                'name' => 'Beras Pandan Wangi 10kg',
                'qty' => 1,
                'price' => 145000
            ]
        ];

        $response = $this->actingAs($admin)->post('/cashflow/transactions', [
            'account_id' => $account->uuid,
            'type' => 'expense',
            'amount' => 189000, // 2 * 22000 + 1 * 145000 = 189000
            'transaction_date' => date('Y-m-d'),
            'description' => 'Belanja mingguan bahan operasional',
            'reference' => 'BELANJA-WEEK1',
            'items' => $itemsData,
            'purchase_place' => 'Toko Berkah Utama'
        ]);

        $response->assertRedirect('/cashflow');
        
        // Assert the database has the transaction
        $this->assertDatabaseHas('cash_flow_transactions', [
            'account_id' => $account->uuid,
            'type' => 'expense',
            'amount' => 189000,
            'reference' => 'BELANJA-WEEK1',
            'purchase_place' => 'Toko Berkah Utama'
        ]);

        // Assert items JSON casting is correct and has the calculated total
        $transaction = CashFlowTransaction::where('reference', 'BELANJA-WEEK1')->first();
        $this->assertNotNull($transaction->items);
        $this->assertCount(2, $transaction->items);
        $this->assertEquals('Gas Melon 3kg', $transaction->items[0]['name']);
        $this->assertEquals(2, $transaction->items[0]['qty']);
        $this->assertEquals(22000, $transaction->items[0]['price']);
        $this->assertEquals(44000, $transaction->items[0]['total']); // 2 * 22000

        $this->assertEquals('Beras Pandan Wangi 10kg', $transaction->items[1]['name']);
        $this->assertEquals(145000, $transaction->items[1]['price']);
        $this->assertEquals(145000, $transaction->items[1]['total']); // 1 * 145000
    }

    /**
     * Test admin can manage cash flow categories and associate them.
     */
    public function test_admin_can_manage_cash_flow_categories_and_associate_them()
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'Admin Keuangan',
            'role' => 'admin',
            'username' => 'admin_flow_cat',
            'password' => Hash::make('password123'),
        ]);

        // 2. Add a category via POST
        $response = $this->actingAs($admin)->post('/cashflow/categories', [
            'name' => 'Kategori Baru Pemasukan',
            'type' => 'income'
        ]);

        $response->assertRedirect('/cashflow');
        $this->assertDatabaseHas('cash_flow_categories', [
            'name' => 'Kategori Baru Pemasukan',
            'type' => 'income'
        ]);

        $category = \App\Models\CashFlowCategory::where('name', 'Kategori Baru Pemasukan')->first();

        // 3. Create Account & Transaction with category association
        $account = CashFlowAccount::create([
            'name' => 'KAS TOKO UTAMA',
            'initial_balance' => 0
        ]);

        $responseTransaction = $this->actingAs($admin)->post('/cashflow/transactions', [
            'account_id' => $account->uuid,
            'type' => 'income',
            'amount' => 500000,
            'transaction_date' => date('Y-m-d'),
            'description' => 'Pendapatan dengan Kategori Baru',
            'category_id' => $category->uuid
        ]);

        $responseTransaction->assertRedirect('/cashflow');
        $this->assertDatabaseHas('cash_flow_transactions', [
            'account_id' => $account->uuid,
            'type' => 'income',
            'amount' => 500000,
            'category_id' => $category->uuid
        ]);

        // 4. Delete the category via DELETE
        $responseDelete = $this->actingAs($admin)->delete('/cashflow/categories/' . $category->uuid);
        $responseDelete->assertRedirect('/cashflow');
        $this->assertDatabaseMissing('cash_flow_categories', [
            'uuid' => $category->uuid
        ]);
    }

    /**
     * Test sales reconciliation with cash drawer money deduction.
     */
    public function test_admin_can_reconcile_with_cash_drawer_deduction()
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'Admin Keuangan',
            'role' => 'admin',
            'username' => 'admin_flow_drawer',
            'password' => Hash::make('password123'),
        ]);

        // 2. Create Account
        $account = CashFlowAccount::create([
            'name' => 'KAS TOKO UTAMA',
            'initial_balance' => 0
        ]);

        // 3. Store a sales reconciliation transaction with operational expense & cash drawer amount
        $response = $this->actingAs($admin)->post('/cashflow/transactions', [
            'account_id' => $account->uuid,
            'type' => 'income',
            'amount' => 750000,
            'operational_expense' => 50000,
            'cash_drawer_amount' => 200000,
            'transaction_date' => date('Y-m-d'),
            'description' => 'Rekonsiliasi Omzet Penjualan dengan Potongan Laci',
            'reference' => 'RECON-002',
            'is_sales_reconciliation' => 1,
            'reconciliation_date' => date('Y-m-d')
        ]);

        $response->assertRedirect('/cashflow');
        $this->assertDatabaseHas('cash_flow_transactions', [
            'account_id' => $account->uuid,
            'type' => 'income',
            'amount' => 750000,
            'operational_expense' => 50000,
            'cash_drawer_amount' => 200000,
            'is_sales_reconciliation' => 1,
            'reconciliation_date' => date('Y-m-d')
        ]);
    }

    /**
     * Test admin can perform internal cash transfer.
     */
    public function test_admin_can_perform_cash_transfer()
    {
        $admin = User::create([
            'name' => 'Admin Keuangan',
            'role' => 'admin',
            'username' => 'admin_transfer',
            'password' => Hash::make('password123'),
        ]);

        $accountA = CashFlowAccount::create([
            'name' => 'LACI KASIR',
            'initial_balance' => 1000000
        ]);

        $accountB = CashFlowAccount::create([
            'name' => 'BANK MANDIRI',
            'initial_balance' => 0
        ]);

        $response = $this->actingAs($admin)->post('/cashflow/transactions', [
            'account_id' => $accountA->uuid,
            'destination_account_id' => $accountB->uuid,
            'type' => 'transfer',
            'amount' => 400000,
            'transaction_date' => date('Y-m-d'),
            'description' => 'Transfer sisa kas harian ke Mandiri'
        ]);

        $response->assertRedirect('/cashflow');
        $this->assertDatabaseHas('cash_flow_transactions', [
            'account_id' => $accountA->uuid,
            'destination_account_id' => $accountB->uuid,
            'type' => 'transfer',
            'amount' => 400000
        ]);
    }

    /**
     * Test admin can reconcile daily sales split across multiple accounts.
     */
    public function test_admin_can_reconcile_with_multi_account_splits()
    {
        $admin = User::create([
            'name' => 'Admin Keuangan',
            'role' => 'admin',
            'username' => 'admin_splits',
            'password' => Hash::make('password123'),
        ]);

        $accountA = CashFlowAccount::create(['name' => 'LACI KASIR', 'initial_balance' => 0]);
        $accountB = CashFlowAccount::create(['name' => 'BANK BCA', 'initial_balance' => 0]);

        $response = $this->actingAs($admin)->post('/cashflow/transactions', [
            'type' => 'income',
            'amount' => 800000,
            'operational_expense' => 50000,
            'cash_drawer_amount' => 150000,
            'transaction_date' => date('Y-m-d'),
            'description' => 'Rekonsiliasi omzet split',
            'is_sales_reconciliation' => 1,
            'reconciliation_date' => date('Y-m-d'),
            'account_splits' => [
                $accountA->uuid => 300000,
                $accountB->uuid => 500000
            ]
        ]);

        $response->assertRedirect('/cashflow');
        
        // Assert that two transactions are created
        $this->assertDatabaseHas('cash_flow_transactions', [
            'account_id' => $accountA->uuid,
            'amount' => 300000,
            'operational_expense' => 50000, // stored on first split
            'cash_drawer_amount' => 150000, // stored on first split
            'is_sales_reconciliation' => 1
        ]);

        $this->assertDatabaseHas('cash_flow_transactions', [
            'account_id' => $accountB->uuid,
            'amount' => 500000,
            'operational_expense' => 0, // 0 on subsequent splits to prevent duplication
            'cash_drawer_amount' => 0, // 0 on subsequent splits to prevent duplication
            'is_sales_reconciliation' => 1
        ]);
    }

    /**
     * Test admin can update an existing cash flow transaction.
     */
    public function test_admin_can_update_cash_flow_transaction()
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'Admin Keuangan',
            'role' => 'admin',
            'username' => 'admin_flow_update',
            'password' => Hash::make('password123'),
        ]);

        // 2. Create Account
        $account = CashFlowAccount::create([
            'name' => 'KAS UTAMA UPDATE',
            'initial_balance' => 1000000
        ]);

        // 3. Create Transaction
        $transaction = CashFlowTransaction::create([
            'account_id' => $account->uuid,
            'type' => 'expense',
            'amount' => 100000,
            'transaction_date' => date('Y-m-d'),
            'description' => 'Membeli sapu',
            'reference' => 'SAPU-01'
        ]);

        // 4. Update Transaction via PUT request
        $response = $this->actingAs($admin)->put('/cashflow/transactions/' . $transaction->uuid, [
            'account_id' => $account->uuid,
            'type' => 'expense',
            'amount' => 120000, // Updated amount
            'transaction_date' => date('Y-m-d'),
            'description' => 'Membeli sapu dan kemoceng', // Updated description
            'reference' => 'SAPU-02' // Updated reference
        ]);

        $response->assertRedirect('/cashflow');
        $this->assertDatabaseHas('cash_flow_transactions', [
            'uuid' => $transaction->uuid,
            'amount' => 120000,
            'description' => 'Membeli sapu dan kemoceng',
            'reference' => 'SAPU-02'
        ]);
    }
}


