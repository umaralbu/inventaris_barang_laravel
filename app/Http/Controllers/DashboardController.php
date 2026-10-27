<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $activeLoanItemIds = Transaction::where('type', 'borrow')
            ->where('status', 'Dipinjam')
            ->select('item_id');
        $totalBarang = Item::where('is_active', true)
            ->orWhereIn('id', $activeLoanItemIds)
            ->count();
        $barangTersedia = Item::where('is_active', true)->where('status', 'Tersedia')->count();
        $barangDipinjam = Transaction::where('type', 'borrow')
            ->where('status', 'Dipinjam')
            ->distinct('item_id')
            ->count('item_id');
        $barangRusak = Item::where('is_active', true)->where('status', 'Rusak')->count();
        $barangHilang = Item::where('is_active', true)->where('status', 'Hilang')->count();
        $lowStockItems = Item::where('is_active', true)
            ->where('status', 'Tersedia')
            ->select('name', DB::raw('SUM(quantity) as quantity'))
            ->groupBy('name')
            ->havingRaw('SUM(quantity) <= 5')
            ->orderBy('quantity', 'asc')
            ->get();

        $itemsByStatus = [
            'total' => Item::with(['category', 'location'])
                ->where(function ($query) {
                    $query->where('is_active', true)
                        ->orWhereHas('transactions', fn ($transactionQuery) => $transactionQuery->where('type', 'borrow')->where('status', 'Dipinjam'));
                })
                ->orderBy('name')
                ->get(),
            'available' => Item::where('is_active', true)->with(['category', 'location'])->where('status', 'Tersedia')->orderBy('name')->get(),
            'borrowed' => Item::with(['category', 'location'])
                ->whereHas('transactions', fn ($query) => $query->where('type', 'borrow')->where('status', 'Dipinjam'))
                ->orderBy('name')
                ->get(),
            'damaged' => Item::where('is_active', true)->with(['category', 'location'])->where('status', 'Rusak')->orderBy('name')->get(),
            'lost' => Item::where('is_active', true)->with(['category', 'location'])->where('status', 'Hilang')->orderBy('name')->get(),
        ];

        if (Auth::user()->role === 'admin') {
            $transactions = Transaction::with(['item', 'user'])
                ->latest('transaction_date')
                ->take(10)
                ->get();

            return view('dashboard.index', compact(
                'totalBarang',
                'barangTersedia',
                'barangDipinjam',
                'barangRusak',
                'barangHilang',
                'lowStockItems',
                'itemsByStatus',
                'transactions'
            ));
        }

        $transactions = Transaction::with(['item', 'user'])
            ->where('user_id', Auth::id())
            ->latest('transaction_date')
            ->take(10)
            ->get();

        $myBorrowed = Transaction::where('user_id', Auth::id())
            ->where('type', 'borrow')
            ->count();

        $myActiveBorrow = Transaction::where('user_id', Auth::id())
            ->where('type', 'borrow')
            ->where('status', 'Dipinjam')
            ->count();

        return view('dashboard.index', compact(
            'totalBarang',
            'barangTersedia',
            'barangDipinjam',
            'barangRusak',
            'barangHilang',
            'lowStockItems',
            'itemsByStatus',
            'transactions',
            'myBorrowed',
            'myActiveBorrow'
        ));
    }
}
