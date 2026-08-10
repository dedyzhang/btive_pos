<?php

namespace Tests\Feature;

use App\Models\Categories;
use App\Models\Products;
use App\Models\Tables;
use App\Models\Transactions;
use App\Models\TransactionDetails;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderTest extends TestCase
{
    use RefreshDatabase;

    private $table;
    private $product;
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create a Table
        $this->table = Tables::create([
            'name' => 'Meja Test 99',
            'color' => 'bg-blue-100',
            'sort' => 1,
            'status' => 'empty'
        ]);

        // 2. Create Category and Product
        $category = Categories::create([
            'nama' => 'Minuman',
            'color' => 'bg-blue-100',
            'icon' => 'fa-glass',
            'sort' => 1
        ]);

        $this->product = Products::create([
            'name' => 'Es Teh Manis',
            'category_id' => $category->uuid,
            'price' => 5000,
            'is_active' => 1,
            'picture' => ''
        ]);

        // 3. Create Admin user as fallback
        $this->admin = User::create([
            'name' => 'Dedy Admin',
            'username' => 'dedy',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);
    }

    /**
     * Test customers can access self-order menu page.
     */
    public function test_customer_can_access_self_order_menu()
    {
        $response = $this->get(route('customer.order.table', $this->table->uuid));

        $response->assertStatus(200);
        $response->assertSee('Meja Test 99');
        $response->assertSee('Es Teh Manis');
    }

    /**
     * Test customer self-order submission.
     */
    public function test_customer_can_submit_order()
    {
        $payload = [
            'customer_name' => 'Budi',
            'items' => [
                [
                    'product_id' => $this->product->uuid,
                    'qty' => 3,
                    'note' => 'Es sedikit'
                ]
            ]
        ];

        $response = $this->postJson(route('customer.order.submit', $this->table->uuid), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        // Verify transaction is created in database
        $this->assertDatabaseHas('transactions', [
            'table_id' => $this->table->uuid,
            'customer_name' => 'Budi (Self)',
            'order_type' => 'dine_in',
            'status' => 'process',
            'kitchen_status' => 'cooking',
            'subtotal' => 15000,
            'total' => 15000 // tax is 0 by default
        ]);

        // Verify transaction detail is created in database
        $this->assertDatabaseHas('transaction_details', [
            'product_id' => $this->product->uuid,
            'product_name' => 'Es Teh Manis',
            'price' => 5000,
            'qty' => 3,
            'note' => 'Es sedikit',
            'subtotal' => 15000
        ]);
    }

    /**
     * Test customers can view their order status page.
     */
    public function test_customer_can_view_order_status()
    {
        // Create dummy transaction
        $transaction = Transactions::create([
            'invoice_number' => 'QR-TEST',
            'user_id' => $this->admin->uuid,
            'table_id' => $this->table->uuid,
            'customer_name' => 'Budi (Self)',
            'order_type' => 'dine_in',
            'status' => 'process',
            'kitchen_status' => 'cooking',
            'subtotal' => 5000,
            'total' => 5000
        ]);

        $response = $this->get(route('customer.order.status', $transaction->uuid));

        $response->assertStatus(200);
        $response->assertSee('QR-TEST');
        $response->assertSee('Budi (Self)');
    }

    /**
     * Test customers can retrieve live status updates via AJAX.
     */
    public function test_customer_can_retrieve_live_status()
    {
        // Create dummy transaction
        $transaction = Transactions::create([
            'invoice_number' => 'QR-TEST',
            'user_id' => $this->admin->uuid,
            'table_id' => $this->table->uuid,
            'customer_name' => 'Budi (Self)',
            'order_type' => 'dine_in',
            'status' => 'process',
            'kitchen_status' => 'cooking',
            'subtotal' => 5000,
            'total' => 5000
        ]);

        $response = $this->get(route('customer.order.liveStatus', $transaction->uuid));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'process',
            'kitchen_status' => 'cooking'
        ]);
    }
}
