<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'http://localhost']);
    }

    /**
     * Test admin can create a custom role.
     */
    public function test_admin_can_create_and_manage_roles()
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'role' => 'admin',
            'username' => 'admin_role_test',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($admin)->post(route('roles.store'), [
            'name' => 'supervisor',
            'permissions' => ['access_cashier', 'manage_products'],
        ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', [
            'name' => 'supervisor',
        ]);

        $role = Role::where('name', 'supervisor')->first();
        $this->assertContains('access_cashier', $role->permissions);
        $this->assertContains('manage_products', $role->permissions);
    }

    /**
     * Test user with custom role gets route authorization based on permissions.
     */
    public function test_custom_role_authorizes_correct_routes()
    {
        // 1. Create a custom role in DB
        Role::create([
            'name' => 'supervisor',
            'permissions' => ['access_cashier', 'manage_products'],
        ]);

        // 2. Create user with supervisor role
        $supervisor = User::create([
            'name' => 'Staff Supervisor',
            'role' => 'supervisor',
            'username' => 'super_staff',
            'password' => Hash::make('password123'),
        ]);

        // 3. Supervisor has 'manage_products' permission - can view products index
        $responseProducts = $this->actingAs($supervisor)->get(route('products.index'));
        $responseProducts->assertStatus(200);

        // 4. Supervisor does NOT have 'manage_settings' permission - gets 403 Forbidden
        $responseSettings = $this->actingAs($supervisor)->get(route('settings.index'));
        $responseSettings->assertStatus(403);
    }

    /**
     * Test admin can customize the default cashier role.
     */
    public function test_admin_can_customize_cashier_role_permissions()
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'role' => 'admin',
            'username' => 'admin_cash_cust',
            'password' => Hash::make('password123'),
        ]);

        // We already have cashier seeded from migration, find or create it
        $cashierRole = Role::firstOrCreate(
            ['name' => 'cashier'],
            ['permissions' => ['access_cashier', 'view_reports']]
        );

        $cashier = User::create([
            'name' => 'Kasir Toko',
            'role' => 'cashier',
            'username' => 'cashier_custom_test',
            'password' => Hash::make('password123'),
        ]);

        // Cashier cannot manage settings initially
        $responseBefore = $this->actingAs($cashier)->get(route('settings.index'));
        $responseBefore->assertStatus(403);

        // Customize cashier permissions by adding 'manage_settings'
        $responseUpdate = $this->actingAs($admin)->put(route('roles.update', $cashierRole->uuid), [
            'name' => 'cashier',
            'permissions' => ['access_cashier', 'view_reports', 'manage_settings'],
        ]);
        $responseUpdate->assertRedirect(route('roles.index'));

        // Cashier can now access settings
        $responseAfter = $this->actingAs($cashier)->get(route('settings.index'));
        $responseAfter->assertStatus(200);
    }

    /**
     * Test admin has master super-user permission.
     */
    public function test_admin_has_full_master_access()
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'role' => 'admin',
            'username' => 'admin_master_test',
            'password' => Hash::make('password123'),
        ]);

        // Admin can access settings
        $responseSettings = $this->actingAs($admin)->get(route('settings.index'));
        $responseSettings->assertStatus(200);

        // Admin can access products
        $responseProducts = $this->actingAs($admin)->get(route('products.index'));
        $responseProducts->assertStatus(200);
    }
}
