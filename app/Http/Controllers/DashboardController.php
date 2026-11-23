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
        $barangTersedia = Item::where('is_active', true)
            ->where('status', 'Tersedia')
            ->where('condition', '!=', 'Rusak')
            ->count();
        $barangDipinjam = Transaction::where('type', 'borrow')
            ->where('status', 'Dipinjam')
            ->distinct('item_id')
            ->count('item_id');
        $barangRusak = Item::where('is_active', true)->where('condition', 'Rusak')->count();
        $barangHilang = Item::where('is_active', true)->where('status', 'Hilang')->count();
        $itemsByStatus = [
            'total' => Item::query()
                ->where(function ($query) {
                    $query->where('is_active', true)
                        ->orWhereHas('transactions', fn ($transactionQuery) => $transactionQuery->where('type', 'borrow')->where('status', 'Dipinjam'));
                })
                ->select('name', 'unit', DB::raw('SUM(quantity) as quantity'))
                ->groupBy('name', 'unit')
                ->orderBy('name')
                ->get(),
            'available' => Item::where('is_active', true)
                ->where('status', 'Tersedia')
                ->where('condition', '!=', 'Rusak')
                ->select('name', 'unit', DB::raw('SUM(quantity) as quantity'))
                ->groupBy('name', 'unit')
                ->orderBy('name')
                ->get(),
            'borrowed' => Item::query()
                ->whereHas('transactions', fn ($query) => $query->where('type', 'borrow')->where('status', 'Dipinjam'))
                ->select('name', 'unit', DB::raw('SUM(quantity) as quantity'))
                ->groupBy('name', 'unit')
                ->orderBy('name')
                ->get(),
            'damaged' => Item::where('is_active', true)
                ->where('condition', 'Rusak')
                ->select('name', 'unit', DB::raw('SUM(quantity) as quantity'))
                ->groupBy('name', 'unit')
                ->orderBy('name')
                ->get(),
            'lost' => Item::where('is_active', true)
                ->where('status', 'Hilang')
                ->select('name', 'unit', DB::raw('SUM(quantity) as quantity'))
                ->groupBy('name', 'unit')
                ->orderBy('name')
                ->get(),
        ];

        $overdueQuery = Transaction::with(['item', 'user'])->overdue();
        if (Auth::user()->role !== 'admin') {
            $overdueQuery->where('user_id', Auth::id());
        }
        $overdueTransactions = $overdueQuery
            ->orderBy('due_date')
            ->orderBy('due_time')
            ->get();

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
                'itemsByStatus',
                'transactions',
                'overdueTransactions'
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

        $myBorrowedItems = Transaction::query()
            ->join('items', 'transactions.item_id', '=', 'items.id')
            ->where('transactions.user_id', Auth::id())
            ->where('transactions.type', 'borrow')
            ->select('items.name', 'items.unit', DB::raw('SUM(transactions.quantity) as quantity'))
            ->groupBy('items.name', 'items.unit')
            ->orderBy('items.name')
            ->get();

        $myActiveBorrowItems = Transaction::query()
            ->join('items', 'transactions.item_id', '=', 'items.id')
            ->where('transactions.user_id', Auth::id())
            ->where('transactions.type', 'borrow')
            ->where('transactions.status', 'Dipinjam')
            ->select('items.name', 'items.unit', DB::raw('SUM(transactions.quantity) as quantity'))
            ->groupBy('items.name', 'items.unit')
            ->orderBy('items.name')
            ->get();

        return view('dashboard.index', compact(
            'totalBarang',
            'barangTersedia',
            'barangDipinjam',
            'barangRusak',
            'barangHilang',
            'itemsByStatus',
            'transactions',
            'myBorrowed',
            'myActiveBorrow',
            'myBorrowedItems',
            'myActiveBorrowItems',
            'overdueTransactions'
        ));
    }
}
