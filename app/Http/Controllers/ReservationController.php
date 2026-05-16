<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with('table')
            ->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->paginate(15);

        $stats = [
            'today'     => Reservation::whereDate('reservation_date', today())->count(),
            'pending'   => Reservation::where('status', 'pending')->count(),
            'confirmed' => Reservation::where('status', 'confirmed')->count(),
            'upcoming'  => Reservation::whereDate('reservation_date', '>=', today())
                            ->whereIn('status', ['pending', 'confirmed'])
                            ->count(),
        ];

        return view('reservations.index', compact('reservations', 'stats'));
    }

    public function create()
    {
        $tables = Table::where('is_active', true)
                    ->orderBy('name')
                    ->get();

        return view('reservations.create', compact('tables'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id'         => 'required|exists:tables,id',
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'nullable|string|max:20',
            'customer_email'   => 'nullable|email|max:255',
            'party_size'       => 'required|integer|min:1',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required',
            'notes'            => 'nullable|string',
        ]);

        $validated['status'] = 'confirmed';

        Reservation::create($validated);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation created successfully.');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('table');
        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $tables = Table::where('is_active', true)->orderBy('name')->get();
        return view('reservations.edit', compact('reservation', 'tables'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'table_id'         => 'required|exists:tables,id',
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'nullable|string|max:20',
            'customer_email'   => 'nullable|email|max:255',
            'party_size'       => 'required|integer|min:1',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'notes'            => 'nullable|string',
        ]);

        $reservation->update($validated);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation updated successfully.');
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed,no_show',
        ]);

        $reservation->update(['status' => $request->status]);

        // Update table status if confirmed
        if ($request->status === 'confirmed') {
            $reservation->table->update(['status' => 'reserved']);
        }

        // Free up table if cancelled or no show
        if (in_array($request->status, ['cancelled', 'no_show'])) {
            $reservation->table->update(['status' => 'available']);
        }

        return back()->with('success', 'Reservation status updated.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->table->update(['status' => 'available']);
        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation deleted.');
    }
}