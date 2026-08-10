<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transactions;
use App\Models\TransactionDetails;
use App\Models\Products;
use App\Models\Categories;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'http://localhost']);
    }

    /**
     * Test admin can access admin dashboard.
     */
    public function test_admin_can_access_admin_dashboard()
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'Admin Utama',
            'role' => 'admin',
            'username' => 'admin_dash',
            'password' => Hash::make('password123'),
        ]);

        // 2. Create Category and Product
        $category = Categories::create([
            'nama' => 'Minuman',
            'icon' => 'fa-mug-hot',
            'color' => '#3b82f6',
            'sort' => 1
        ]);

        $product = Products::create([
            'category_id' => $category->uuid,
            'name' => 'Kopi Susu Premium',
            'price' => 25000,
            'cost_price' => 10000,
            'stock' => 100,
            'is_active' => 1
        ]);

        // 3. Create Paid Transaction for Today
        $transaction = Transactions::create([
            'invoice_number' => 'INV-20260602-0001',
            'user_id' => $admin->uuid,
            'customer_name' => 'John Doe',
            'order_type' => 'dine-in',
            'subtotal' => 25000,
            'discount' => 0,
            'tax' => 2500,
            'service_charge' => 0,
            'total' => 27500,
            'status' => 'paid',
            'paid_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'paid_method' => 'QRIS',
            'total_paid' => 27500
        ]);

        TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_id' => $product->uuid,
            'product_name' => 'Kopi Susu Premium',
            'price' => 25000,
            'qty' => 1,
            'subtotal' => 25000
        ]);

        // 4. Access admin dashboard
        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('DASHBOARD');
        $response->assertSee('Omzet Hari Ini');
        $response->assertSee('Laba Kotor');
        $response->assertSee('Total Transaksi');
        $response->assertSee('Item Terjual');
        $response->assertSee('Kopi Susu Premium');
        $response->assertSee('QRIS');
    }

    /**
     * Test cashier or other roles are forbidden.
     */
    public function test_non_admin_cannot_access_admin_dashboard()
    {
        // 1. Create Cashier
        $cashier = User::create([
            'name' => 'Kasir Toko',
            'role' => 'kasir',
            'username' => 'kasir_dash',
            'password' => Hash::make('password123'),
        ]);

        // 2. Access admin dashboard
        $response = $this->actingAs($cashier)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /**
     * Test cashier can access cashier dashboard (home) and see best seller products.
     */
    public function test_cashier_dashboard_displays_best_sellers()
    {
        // 1. Create Cashier
        $cashier = User::create([
            'name' => 'Kasir Toko',
            'role' => 'kasir',
            'username' => 'kasir_dash_sellers',
            'password' => Hash::make('password123'),
        ]);

        // 2. Create Category and Products
        $category = Categories::create([
            'nama' => 'Makanan',
            'icon' => 'fa-utensils',
            'color' => '#ef4444',
            'sort' => 1
        ]);

        // Create 12 products
        $products = [];
        for ($i = 1; $i <= 12; $i++) {
            $products[$i] = Products::create([
                'category_id' => $category->uuid,
                'name' => 'Product ' . $i,
                'price' => 10000 * $i,
                'cost_price' => 5000 * $i,
                'stock' => 100,
                'is_active' => 1
            ]);
        }

        // Create transaction details with different quantities
        // Product 1 to 10 will have higher quantities
        // Product 11 and 12 will have low/no quantities
        $transaction = Transactions::create([
            'invoice_number' => 'INV-20260602-0002',
            'user_id' => $cashier->uuid,
            'customer_name' => 'Guest',
            'order_type' => 'dine-in',
            'subtotal' => 100000,
            'discount' => 0,
            'tax' => 10000,
            'service_charge' => 0,
            'total' => 110000,
            'status' => 'paid',
            'paid_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'paid_method' => 'Cash',
            'total_paid' => 110000
        ]);

        for ($i = 1; $i <= 10; $i++) {
            TransactionDetails::create([
                'order_id' => $transaction->uuid,
                'product_id' => $products[$i]->uuid,
                'product_name' => $products[$i]->name,
                'price' => $products[$i]->price,
                'qty' => 15 - $i, // high qty (14, 13, ..., 5)
                'subtotal' => $products[$i]->price * (15 - $i)
            ]);
        }

        // Product 11 has qty 1
        TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_id' => $products[11]->uuid,
            'product_name' => $products[11]->name,
            'price' => $products[11]->price,
            'qty' => 1,
            'subtotal' => $products[11]->price
        ]);

        // 3. Access cashier dashboard
        $response = $this->actingAs($cashier)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Best Seller');
        $response->assertSee('BEST SELLER'); // badge check
    }

    /**
     * Test admin user is redirected to admin dashboard on login.
     */
    public function test_admin_user_is_redirected_to_admin_dashboard_on_login()
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'Admin Utama',
            'role' => 'admin',
            'username' => 'admin_redirect_test',
            'password' => Hash::make('password123'),
        ]);

        // 2. Perform Login
        $response = $this->post('/login', [
            'username' => 'admin_redirect_test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
    }

    /**
     * Test authenticated admin is redirected to admin dashboard when accessing root.
     */
    public function test_authenticated_admin_is_redirected_to_admin_dashboard_when_accessing_root()
    {
        // 1. Create Admin
        $admin = User::create([
            'name' => 'Admin Utama',
            'role' => 'admin',
            'username' => 'admin_root_test',
            'password' => Hash::make('password123'),
        ]);

        // 2. Access root route while authenticated
        $response = $this->actingAs($admin)->get('/');

        $response->assertRedirect('/admin/dashboard');
    }

    /**
     * Test admin can sort categories in settings.
     */
    public function test_admin_can_sort_categories_in_settings()
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'role' => 'admin',
            'username' => 'admin_sort_cat',
            'password' => Hash::make('password123'),
        ]);

        $cat1 = Categories::create([
            'nama' => 'Makanan',
            'icon' => 'fa-utensils',
            'color' => '#ef4444',
            'sort' => 1
        ]);

        $cat2 = Categories::create([
            'nama' => 'Minuman',
            'icon' => 'fa-mug-hot',
            'color' => '#3b82f6',
            'sort' => 2
        ]);

        $response = $this->actingAs($admin)->post(route('settings.category.sort'), [
            'urutan' => [
                [
                    'uuid' => $cat2->uuid,
                    'sort' => 1,
                    'nama' => $cat2->nama,
                    'icon' => $cat2->icon,
                    'color' => $cat2->color,
                ],
                [
                    'uuid' => $cat1->uuid,
                    'sort' => 2,
                    'nama' => $cat1->nama,
                    'icon' => $cat1->icon,
                    'color' => $cat1->color,
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(1, Categories::findOrFail($cat2->uuid)->sort);
        $this->assertEquals(2, Categories::findOrFail($cat1->uuid)->sort);
    }

    /**
     * Test print receipt items are sorted by category sort order.
     */
    public function test_print_receipt_items_are_sorted_by_category_sort_order()
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'role' => 'admin',
            'username' => 'admin_print_sort',
            'password' => Hash::make('password123'),
        ]);

        // Category Makanan (sort = 2)
        $catFood = Categories::create([
            'nama' => 'Makanan',
            'icon' => 'fa-utensils',
            'color' => '#ef4444',
            'sort' => 2
        ]);

        // Category Minuman (sort = 1)
        $catDrink = Categories::create([
            'nama' => 'Minuman',
            'icon' => 'fa-mug-hot',
            'color' => '#3b82f6',
            'sort' => 1
        ]);

        $prodFood = Products::create([
            'category_id' => $catFood->uuid,
            'name' => 'Nasi Goreng',
            'price' => 15000,
            'cost_price' => 7000,
            'stock' => 100,
            'is_active' => 1
        ]);

        $prodDrink = Products::create([
            'category_id' => $catDrink->uuid,
            'name' => 'Es Teh Manis',
            'price' => 5000,
            'cost_price' => 2000,
            'stock' => 100,
            'is_active' => 1
        ]);

        $transaction = Transactions::create([
            'invoice_number' => 'INV-20260602-9999',
            'user_id' => $admin->uuid,
            'status' => 'paid',
            'paid_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'total' => 20000,
            'subtotal' => 20000,
            'total_paid' => 20000
        ]);

        // Detail Makanan created first in database
        $itemFood = TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_id' => $prodFood->uuid,
            'product_name' => $prodFood->name,
            'price' => 15000,
            'qty' => 1,
            'subtotal' => 15000
        ]);

        // Detail Minuman created second
        $itemDrink = TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_id' => $prodDrink->uuid,
            'product_name' => $prodDrink->name,
            'price' => 5000,
            'qty' => 1,
            'subtotal' => 5000
        ]);

        // Access the print endpoint
        $response = $this->actingAs($admin)->get(route('transaction.print.check', $transaction->uuid));

        $response->assertStatus(200);
        $data = $response->json();
        
        $printedItems = $data['transaction']['order_item'];

        // Confirm that the items are returned sorted by category sort value.
        // Minuman (sort = 1) should be first, even though Makanan was created first.
        $this->assertEquals($itemDrink->uuid, $printedItems[0]['uuid']);
        $this->assertEquals($itemFood->uuid, $printedItems[1]['uuid']);
    }

    /**
     * Test sales report items are sorted by category sort order, and then by qty descending.
     */
    public function test_sales_report_excel_export_is_sorted_by_category_and_qty()
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'role' => 'admin',
            'username' => 'admin_report_sort',
            'password' => Hash::make('password123'),
        ]);

        // Category Makanan (sort = 2)
        $catFood = Categories::create([
            'nama' => 'Makanan',
            'icon' => 'fa-utensils',
            'color' => '#ef4444',
            'sort' => 2
        ]);

        // Category Minuman (sort = 1)
        $catDrink = Categories::create([
            'nama' => 'Minuman',
            'icon' => 'fa-mug-hot',
            'color' => '#3b82f6',
            'sort' => 1
        ]);

        $prodFood1 = Products::create([
            'category_id' => $catFood->uuid,
            'name' => 'Nasi Goreng',
            'price' => 15000,
            'cost_price' => 7000,
            'stock' => 100,
            'is_active' => 1
        ]);

        $prodFood2 = Products::create([
            'category_id' => $catFood->uuid,
            'name' => 'Ayam Bakar',
            'price' => 20000,
            'cost_price' => 10000,
            'stock' => 100,
            'is_active' => 1
        ]);

        $prodDrink1 = Products::create([
            'category_id' => $catDrink->uuid,
            'name' => 'Es Teh Manis',
            'price' => 5000,
            'cost_price' => 2000,
            'stock' => 100,
            'is_active' => 1
        ]);

        $prodDrink2 = Products::create([
            'category_id' => $catDrink->uuid,
            'name' => 'Kopi Susu',
            'price' => 10000,
            'cost_price' => 4000,
            'stock' => 100,
            'is_active' => 1
        ]);

        $today = Carbon::now()->format('Y-m-d');

        $transaction = Transactions::create([
            'invoice_number' => 'INV-20260607-0001',
            'user_id' => $admin->uuid,
            'status' => 'paid',
            'paid_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'total' => 100000,
            'subtotal' => 100000,
            'total_paid' => 100000
        ]);

        // Drink 1 (qty = 3)
        TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_id' => $prodDrink1->uuid,
            'product_name' => $prodDrink1->name,
            'price' => 5000,
            'qty' => 3,
            'subtotal' => 15000
        ]);

        // Drink 2 (qty = 8)
        TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_id' => $prodDrink2->uuid,
            'product_name' => $prodDrink2->name,
            'price' => 10000,
            'qty' => 8,
            'subtotal' => 80000
        ]);

        // Food 1 (qty = 5)
        TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_id' => $prodFood1->uuid,
            'product_name' => $prodFood1->name,
            'price' => 15000,
            'qty' => 5,
            'subtotal' => 75000
        ]);

        // Food 2 (qty = 10)
        TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_id' => $prodFood2->uuid,
            'product_name' => $prodFood2->name,
            'price' => 20000,
            'qty' => 10,
            'subtotal' => 200000
        ]);

        // Uncategorized manual item (qty = 15)
        TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_id' => null,
            'product_name' => 'Manual Ice Cream',
            'price' => 8000,
            'qty' => 15,
            'subtotal' => 120000
        ]);

        // Get the report endpoint
        $response = $this->actingAs($admin)->get('/activity/' . $today . '/report?start_date=' . $today . '&end_date=' . $today);

        $response->assertStatus(200);
        $data = $response->json();
        
        $items = array_values($data['summary']['items']);

        // Assert count
        $this->assertCount(5, $items);

        // Expected sorted order:
        // 1. Kopi Susu (Minuman, sort=1, qty=8)
        // 2. Es Teh Manis (Minuman, sort=1, qty=3)
        // 3. Ayam Bakar (Makanan, sort=2, qty=10)
        // 4. Nasi Goreng (Makanan, sort=2, qty=5)
        // 5. Manual Ice Cream (Uncategorized, sort=999999, qty=15)

        $this->assertEquals('Kopi Susu', $items[0]['name']);
        $this->assertEquals('Es Teh Manis', $items[1]['name']);
        $this->assertEquals('Ayam Bakar', $items[2]['name']);
        $this->assertEquals('Nasi Goreng', $items[3]['name']);
        $this->assertEquals('Manual Ice Cream', $items[4]['name']);
    }
}
