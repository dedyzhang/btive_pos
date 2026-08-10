<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest cannot change password.
     */
    public function test_guest_cannot_change_password()
    {
        $response = $this->postJson(route('users.change-password'), [
            'current_password' => 'old_password',
            'new_password' => 'new_password123',
            'new_password_confirmation' => 'new_password123',
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    /**
     * Test change password with wrong current password.
     */
    public function test_cannot_change_password_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct_password'),
        ]);

        $response = $this->actingAs($user)->postJson(route('users.change-password'), [
            'current_password' => 'wrong_password',
            'new_password' => 'new_password123',
            'new_password_confirmation' => 'new_password123',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Password saat ini salah.'
                 ]);
    }

    /**
     * Test change password validation rules (e.g., minimum length).
     */
    public function test_change_password_validation_rules()
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct_password'),
        ]);

        // Test min 6 characters
        $response = $this->actingAs($user)->postJson(route('users.change-password'), [
            'current_password' => 'correct_password',
            'new_password' => '12345',
            'new_password_confirmation' => '12345',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['new_password']);

        // Test confirmation matches
        $response = $this->actingAs($user)->postJson(route('users.change-password'), [
            'current_password' => 'correct_password',
            'new_password' => 'new_password123',
            'new_password_confirmation' => 'different_password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['new_password']);
    }

    /**
     * Test successful change password.
     */
    public function test_successful_change_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old_password'),
        ]);

        $response = $this->actingAs($user)->postJson(route('users.change-password'), [
            'current_password' => 'old_password',
            'new_password' => 'new_password123',
            'new_password_confirmation' => 'new_password123',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Password Anda berhasil diubah!'
                 ]);

        // Re-fetch user and verify password hash
        $user->refresh();
        $this->assertTrue(Hash::check('new_password123', $user->password));
    }
}
