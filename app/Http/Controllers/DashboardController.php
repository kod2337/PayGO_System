<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalEmployees' => 1234,
            'monthlyPayroll' => '₱4.5M',
            'pendingApprovals' => 23,
            'departmentBudget' => '₱12.8M'
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }
}