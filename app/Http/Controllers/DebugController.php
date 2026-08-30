<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebugController extends Controller
{
    /**
     * Debug login redirect issue
     */
    public function debugLogin()
    {
        // Get all users with their roles
        $users = User::select('id', 'name', 'email', 'role', 'is_active')->get();
        
        echo "<h1>Debug Login Redirect Issue</h1>";
        echo "<h2>All Users in Database:</h2>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Active</th></tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user->id}</td>";
            echo "<td>{$user->name}</td>";
            echo "<td>{$user->email}</td>";
            echo "<td>{$user->role}</td>";
            echo "<td>" . ($user->is_active ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Test redirect logic
        echo "<h2>Test Redirect Logic:</h2>";
        foreach ($users as $user) {
            $redirect = $this->getRedirectUrl($user);
            echo "<p><strong>{$user->name}</strong> ({$user->role}) would redirect to: {$redirect}</p>";
        }
        
        // Check if routes exist
        echo "<h2>Route Check:</h2>";
        $routes = [
            'superadmin.dashboard' => route('superadmin.dashboard'),
            'admin.dashboard' => route('admin.dashboard'),
            'teacher.dashboard' => route('teacher.dashboard'),
            'student.portal' => route('student.portal'),
        ];
        
        foreach ($routes as $name => $url) {
            echo "<p><strong>{$name}</strong>: {$url}</p>";
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
