<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Transactions;
use App\Models\TransactionDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KitchenQueueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'http://localhost']);
    }

    /**
     * Test authorized and unauthorized access to kitchen queue.
     */
    public function test_kitchen_queue_access_control()
    {
        // 1. Create a Dapur user
        $dapur = User::create([
            'name' => 'Tim Dapur',
            'role' => 'dapur',
            'username' => 'dapur_staf',
            'password' => Hash::make('password123'),
        ]);

        // 2. Create a Cashier user
        $cashier = User::create([
            'name' => 'Kasir Toko',
            'role' => 'cashier',
            'username' => 'cashier_staf',
            'password' => Hash::make('password123'),
        ]);

        // 3. Create Admin user
        $admin = User::create([
            'name' => 'Admin Utama',
            'role' => 'admin',
            'username' => 'admin_staf',
            'password' => Hash::make('password123'),
        ]);

        // Dapur has view_kitchen_queue fallback, should get 200
        $responseDapur = $this->actingAs($dapur)->get(route('kitchen.queue'));
        $responseDapur->assertStatus(200);

        // Cashier does NOT have permission, should get 403
        $responseCashier = $this->actingAs($cashier)->get(route('kitchen.queue'));
        $responseCashier->assertStatus(403);

        // Admin has super-admin bypass, should get 200
        $responseAdmin = $this->actingAs($admin)->get(route('kitchen.queue'));
        $responseAdmin->assertStatus(200);
    }

    /**
     * Test that kitchen queue live-updates strictly strips all price/cost columns.
     */
    public function test_kitchen_queue_live_updates_hides_prices()
    {
        $dapur = User::create([
            'name' => 'Tim Dapur',
            'role' => 'dapur',
            'username' => 'dapur_staf',
            'password' => Hash::make('password123'),
        ]);

        // Create a transaction that is currently cooking
        $transaction = Transactions::create([
            'invoice_number' => 'INV-TEST-001',
            'user_id' => $dapur->uuid,
            'customer_name' => 'Budi Utomo',
            'order_type' => 'dine_in',
            'subtotal' => 100000,
            'tax' => 10000,
            'total' => 110000,
            'status' => 'process',
            'kitchen_status' => 'cooking'
        ]);

        // Create transaction details with price details
        TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_name' => 'Nasi Goreng Spesial',
            'price' => 50000,
            'qty' => 2,
            'note' => 'Pedas sedang, tanpa timun',
            'subtotal' => 100000
        ]);

        // Call the live updates API
        $response = $this->actingAs($dapur)->get(route('kitchen.live-updates'));
        $response->assertStatus(200);

        // Verify JSON response has items but NO price fields
        $json = $response->json();
        
        $this->assertNotEmpty($json['cooking']);
        $cookingOrder = $json['cooking'][0];
        
        // Assert basic details are present
        $this->assertEquals('Budi Utomo', $cookingOrder['customer_name']);
        $this->assertEquals('dine_in', $cookingOrder['order_type']);
        $this->assertEquals('Nasi Goreng Spesial', $cookingOrder['order_item'][0]['product_name']);
        $this->assertEquals(2, $cookingOrder['order_item'][0]['qty']);
        $this->assertEquals('Pedas sedang, tanpa timun', $cookingOrder['order_item'][0]['note']);

        // Assert price details are COMPLETELY REMOVED from the API response payload
        $this->assertArrayNotHasKey('price', $cookingOrder);
        $this->assertArrayNotHasKey('subtotal', $cookingOrder);
        $this->assertArrayNotHasKey('total', $cookingOrder);
        $this->assertArrayNotHasKey('tax', $cookingOrder);
        $this->assertArrayNotHasKey('discount', $cookingOrder);
        $this->assertArrayNotHasKey('price', $cookingOrder['order_item'][0]);
        $this->assertArrayNotHasKey('subtotal', $cookingOrder['order_item'][0]);
    }

    /**
     * Test kitchen status transitions from cooking to ready.
     */
    public function test_kitchen_status_update()
    {
        $dapur = User::create([
            'name' => 'Tim Dapur',
            'role' => 'dapur',
            'username' => 'dapur_staf',
            'password' => Hash::make('password123'),
        ]);

        $transaction = Transactions::create([
            'invoice_number' => 'INV-TEST-002',
            'user_id' => $dapur->uuid,
            'customer_name' => 'Siti',
            'order_type' => 'take_away',
            'status' => 'process',
            'kitchen_status' => 'cooking'
        ]);

        // Update status to ready
        $response = $this->actingAs($dapur)->post(route('kitchen.status.update', $transaction->uuid), [
            'status' => 'ready'
        ]);

        $response->assertStatus(200);
        
        // Assert status was updated in database
        $this->assertDatabaseHas('transactions', [
            'uuid' => $transaction->uuid,
            'kitchen_status' => 'ready'
        ]);
    }

    /**
     * Test toggling an individual item completion status.
     */
    public function test_kitchen_item_status_update()
    {
        $dapur = User::create([
            'name' => 'Tim Dapur',
            'role' => 'dapur',
            'username' => 'dapur_staf',
            'password' => Hash::make('password123'),
        ]);

        $transaction = Transactions::create([
            'invoice_number' => 'INV-TEST-003',
            'user_id' => $dapur->uuid,
            'status' => 'process',
            'kitchen_status' => 'cooking'
        ]);

        $item = TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_name' => 'Ayam Bakar Madu',
            'price' => 30000,
            'qty' => 1,
            'subtotal' => 30000,
            'is_kitchen_done' => false
        ]);

        // Update item to done
        $response = $this->actingAs($dapur)->post(route('kitchen.item.status.update', $item->uuid), [
            'is_kitchen_done' => true
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('transaction_details', [
            'uuid' => $item->uuid,
            'is_kitchen_done' => 1
        ]);

        // Toggle back to not done
        $response = $this->actingAs($dapur)->post(route('kitchen.item.status.update', $item->uuid), [
            'is_kitchen_done' => false
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('transaction_details', [
            'uuid' => $item->uuid,
            'is_kitchen_done' => 0
        ]);
    }

    /**
     * Test cascading status updates to child items when order is finalized/reverted.
     */
    public function test_kitchen_status_cascade()
    {
        $dapur = User::create([
            'name' => 'Tim Dapur',
            'role' => 'dapur',
            'username' => 'dapur_staf',
            'password' => Hash::make('password123'),
        ]);

        $transaction = Transactions::create([
            'invoice_number' => 'INV-TEST-004',
            'user_id' => $dapur->uuid,
            'status' => 'process',
            'kitchen_status' => 'cooking'
        ]);

        $item1 = TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_name' => 'Jus Alpukat',
            'price' => 15000,
            'qty' => 1,
            'subtotal' => 15000,
            'is_kitchen_done' => false
        ]);

        $item2 = TransactionDetails::create([
            'order_id' => $transaction->uuid,
            'product_name' => 'Jus Mangga',
            'price' => 15000,
            'qty' => 1,
            'subtotal' => 15000,
            'is_kitchen_done' => false
        ]);

        // Complete the entire order cooking status
        $responseReady = $this->actingAs($dapur)->post(route('kitchen.status.update', $transaction->uuid), [
            'status' => 'ready'
        ]);
        $responseReady->assertStatus(200);

        // Verify BOTH details are marked done
        $this->assertDatabaseHas('transaction_details', ['uuid' => $item1->uuid, 'is_kitchen_done' => 1]);
        $this->assertDatabaseHas('transaction_details', ['uuid' => $item2->uuid, 'is_kitchen_done' => 1]);

        // Revert the entire order to cooking status
        $responseCooking = $this->actingAs($dapur)->post(route('kitchen.status.update', $transaction->uuid), [
            'status' => 'cooking'
        ]);
        $responseCooking->assertStatus(200);

        // Verify BOTH details are reset to not done
        $this->assertDatabaseHas('transaction_details', ['uuid' => $item1->uuid, 'is_kitchen_done' => 0]);
        $this->assertDatabaseHas('transaction_details', ['uuid' => $item2->uuid, 'is_kitchen_done' => 0]);
    }
}
