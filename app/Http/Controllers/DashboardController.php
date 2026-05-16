<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalTables'      => Table::where('is_active', true)->count(),
            'availableTables'  => Table::where('status', 'available')->count(),
            'occupiedTables'   => Table::where('status', 'occupied')->count(),
            'todayOrders'      => Order::whereDate('created_at', today())->count(),
            'todayRevenue'     => Order::whereDate('created_at', today())
                                    ->where('status', 'completed')
                                    ->sum('total_amount'),
            'pendingOrders'    => Order::whereIn('status', ['pending', 'preparing'])->count(),
            'todayReservations'=> Reservation::whereDate('reservation_date', today())->count(),
            'totalUsers'       => User::where('status', 'active')->count(),
            'recentOrders'     => Order::with(['table', 'waiter'])
                                    ->latest()
                                    ->take(5)
                                    ->get(),
            'recentAuditLogs'  => AuditLog::with('user')
                                    ->latest()
                                    ->take(5)
                                    ->get(),
            'weeklyRevenue'    => $this->getWeeklyRevenue(),
        ];

        return view('dashboard', $data);
    }

    private function getWeeklyRevenue(): array
    {
        $days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_amount');
            $days->push([
                'day'     => $date->format('D'),
                'revenue' => (float) $revenue,
            ]);
        }
        return $days->toArray();
    }
}