<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    // ══════════════════════════════════════════
    // STUDENT PORTAL
    // ══════════════════════════════════════════
    public function index()
    {
        $student = Auth::user();
        return view('studentportal', compact('student'));
    }
}
