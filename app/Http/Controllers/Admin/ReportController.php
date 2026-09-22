<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function earnings(Request $request)
    {
        $transactions = \App\Models\Transaction::with(['user', 'driver'])->latest()->paginate(25);
        $totalEarnings = \App\Models\Transaction::where('type', 'credit')->sum('amount');
        
        if ($request->has('export') && $request->export == 'csv') {
            return $this->exportCsv($transactions, 'earnings_report.csv');
        }

        return view('backend.reports.earnings', compact('transactions', 'totalEarnings'));
    }

    public function drivers(Request $request)
    {
        $drivers = \App\Models\DriverRegistration::latest()->paginate(25);
        
        if ($request->has('export') && $request->export == 'csv') {
            return $this->exportCsv($drivers, 'driver_performance.csv');
        }

        return view('backend.reports.drivers', compact('drivers'));
    }

    public function bookings(Request $request)
    {
        $bookings = \App\Models\Booking::with(['user'])->latest()->paginate(25);
        
        if ($request->has('export') && $request->export == 'csv') {
            return $this->exportCsv($bookings, 'booking_statistics.csv');
        }

        return view('backend.reports.bookings', compact('bookings'));
    }

    protected function exportCsv($data, $filename)
    {
        // Simulated CSV Export Logic
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Date', 'Status / Detail']); // Simplified Sample Headers

            foreach ($data as $row) {
                fputcsv($file, [$row->id, $row->created_at, 'System Export Row']);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
