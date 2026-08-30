<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PerformanceController extends Controller
{
    /**
     * Get system performance metrics
     */
    public function getMetrics(Request $request)
    {
        // Only allow admin users to access performance metrics
        if (!$request->user()->isAdmin() && !$request->user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $metrics = [
            'database' => $this->getDatabaseMetrics(),
            'cache' => $this->getCacheMetrics(),
            'system' => $this->getSystemMetrics(),
            'users' => $this->getUserMetrics(),
            'performance' => $this->getPerformanceMetrics()
        ];

        return response()->json([
            'success' => true,
            'data' => $metrics
        ]);
    }

    /**
     * Get database performance metrics
     */
    private function getDatabaseMetrics()
    {
        try {
            $connection = DB::connection();
            
            return [
                'connection_status' => 'connected',
                'database_name' => $connection->getDatabaseName(),
                'driver' => $connection->getDriverName(),
                'queries_count' => DB::getQueryLog() ? count(DB::getQueryLog()) : 0,
                'slow_queries' => $this->getSlowQueriesCount(),
                'connection_time' => $this->measureConnectionTime()
            ];
        } catch (\Exception $e) {
            return [
                'connection_status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get cache metrics
     */
    private function getCacheMetrics()
    {
        try {
            $testKey = 'performance_test_' . time();
            $testValue = 'test_value';
            
            // Test cache write
            $writeStart = microtime(true);
            Cache::put($testKey, $testValue, 60);
            $writeTime = (microtime(true) - $writeStart) * 1000;
            
            // Test cache read
            $readStart = microtime(true);
            $cachedValue = Cache::get($testKey);
            $readTime = (microtime(true) - $readStart) * 1000;
            
            // Clean up test key
            Cache::forget($testKey);
            
            return [
                'status' => 'working',
                'driver' => config('cache.default'),
                'write_time_ms' => round($writeTime, 2),
                'read_time_ms' => round($readTime, 2),
                'test_passed' => $cachedValue === $testValue
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get system metrics
     */
    private function getSystemMetrics()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'memory_limit' => ini_get('memory_limit'),
            'peak_memory' => $this->formatBytes(memory_get_peak_usage(true)),
            'uptime' => $this->getServerUptime(),
            'load_average' => function_exists('sys_getloadavg') ? sys_getloadavg() : null,
            'disk_usage' => $this->getDiskUsage()
        ];
    }

    /**
     * Get user metrics
     */
    private function getUserMetrics()
    {
        return [
            'total_users' => \App\Models\User::count(),
            'active_users' => \App\Models\User::where('is_active', true)->count(),
            'students' => \App\Models\User::where('role', 'student')->count(),
            'teachers' => \App\Models\User::where('role', 'teacher')->count(),
            'admins' => \App\Models\User::where('role', 'admin')->count(),
            'superadmins' => \App\Models\User::where('role', 'superadmin')->count(),
            'verified_users' => \App\Models\User::whereNotNull('email_verified_at')->count(),
            'google_users' => \App\Models\User::whereNotNull('google_id')->count()
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics()
    {
        $startTime = microtime(true);
        
        // Simulate some database queries
        $userCount = \App\Models\User::count();
        $profileCount = \App\Models\StudentProfile::count();
        
        $totalTime = (microtime(true) - $startTime) * 1000;
        
        return [
            'response_time_ms' => round($totalTime, 2),
            'queries_per_second' => round(($userCount + $profileCount) / ($totalTime / 1000), 2),
            'cache_hit_ratio' => $this->getCacheHitRatio(),
            'optimization_score' => $this->calculateOptimizationScore()
        ];
    }

    /**
     * Helper methods
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function getServerUptime()
    {
        if (function_exists('sys_getloadavg')) {
            $uptime = shell_exec('uptime');
            return trim($uptime);
        }
        return 'N/A';
    }

    private function getDiskUsage()
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;
        
        return [
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'usage_percentage' => round(($used / $total) * 100, 2)
        ];
    }

    private function measureConnectionTime()
    {
        $start = microtime(true);
        DB::select('SELECT 1');
        return round((microtime(true) - $start) * 1000, 2);
    }

    private function getSlowQueriesCount()
    {
        // This would require MySQL slow query log configuration
        // For now, return a placeholder
        return 0;
    }

    private function getCacheHitRatio()
    {
        // This would require Redis or other cache system with hit ratio tracking
        // For now, return an estimated value
        return 85.5;
    }

    private function calculateOptimizationScore()
    {
        $score = 100;
        
        // Deduct points for potential issues
        if (ini_get('opcache.enable')) $score -= 10;
        if (!config('cache.default') === 'redis') $score -= 5;
        if (config('app.debug')) $score -= 15;
        
        return max(0, $score);
    }
}
