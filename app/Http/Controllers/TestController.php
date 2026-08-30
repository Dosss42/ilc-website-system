<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    /**
     * Test login redirect manually
     */
    public function testLogin()
    {
        echo "<h1>Login Debug Test</h1>";
        
        // Test specific admin user
        $admin = User::where('role', 'admin')->first();
        
        if ($admin) {
            echo "<h2>Found Admin User:</h2>";
            echo "<p>ID: {$admin->id}</p>";
            echo "<p>Name: {$admin->name}</p>";
            echo "<p>Email: {$admin->email}</p>";
            echo "<p>Role: {$admin->role}</p>";
            echo "<p>Active: " . ($admin->is_active ? 'Yes' : 'No') . "</p>";
            echo "<p>Email Verified: " . ($admin->email_verified_at ? 'Yes' : 'No') . "</p>";
            
            // Test redirect logic
            $redirectUrl = $this->getRedirectUrl($admin);
            echo "<p><strong>Should redirect to:</strong> {$redirectUrl}</p>";
            
            // Test actual route
            try {
                $route = route('admin.dashboard');
                echo "<p><strong>Route exists:</strong> {$route}</p>";
            } catch (\Exception $e) {
                echo "<p><strong>Route error:</strong> {$e->getMessage()}</p>";
            }
        } else {
            echo "<h2>No admin user found in database!</h2>";
        }
        
        // Test all users
        echo "<h2>All Users:</h2>";
        $users = User::all();
        foreach ($users as $user) {
            echo "<p><strong>{$user->name}</strong> - Role: {$user->role} - Active: " . ($user->is_active ? 'Yes' : 'No') . "</p>";
        }
    }
    
    private function getRedirectUrl($user)
    {
        return match($user->role) {
            'superadmin' => route('superadmin.dashboard'),
            'admin' => route('admin.dashboard'),
            'teacher' => route('teacher.dashboard'),
            default => route('student.portal'),
        };
    }
}
