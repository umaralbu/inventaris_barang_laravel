<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'months' => ['nullable', 'integer', 'in:6,12,24'],
        ]);

        $months = (int) ($validated['months'] ?? 6);
        $periodStart = now()->startOfMonth()->subMonths($months - 1);
        $periodEnd = now()->endOfMonth();

        $transactionCounts = $this->completedBorrowings()
            ->whereBetween('transactions.transaction_date', [$periodStart, $periodEnd])
            ->get(['transaction_date'])
            ->countBy(fn (Transaction $transaction) => $transaction->transaction_date->format('Y-m'));

        $monthlyBorrowings = Collection::times($months, function (int $offset) use ($periodStart, $transactionCounts): array {
            $month = $periodStart->copy()->addMonths($offset - 1);
            $monthKey = $month->format('Y-m');

            return [
                'label' => $month->format('m/Y'),
                'total' => (int) ($transactionCounts->get($monthKey, 0)),
            ];
        });
        $periodBorrowingCount = $monthlyBorrowings->sum('total');
        $activeBorrowCount = Transaction::query()
            ->where('type', 'borrow')
            ->where('status', 'Dipinjam')
            ->count();
        $availableItemCount = Item::query()
            ->where('is_active', true)
            ->where('status', 'Tersedia')
            ->where('condition', '!=', 'Rusak')
            ->count();
        $topBorrowedItems = $this->completedBorrowings()
            ->join('items', 'transactions.item_id', '=', 'items.id')
            ->whereBetween('transactions.transaction_date', [$periodStart, $periodEnd])
            ->select('items.name', 'items.unit', DB::raw('COUNT(transactions.id) as total'))
            ->groupBy('items.name', 'items.unit')
            ->orderByDesc('total')
            ->orderBy('items.name')
            ->limit(5)
            ->get();

        $totalInventoryValue = Item::query()
            ->where('is_active', true)
            ->sum(DB::raw('quantity * purchase_price'));
        $chartScale = max(4, (int) (ceil(max(1, (int) $monthlyBorrowings->max('total')) / 4) * 4));
        $chartWidth = max(700, $months * 58 + 80);
        $plotX = 52;
        $plotY = 24;
        $plotWidth = $chartWidth - $plotX - 20;
        $plotHeight = 220;
        $barStep = $plotWidth / $months;

        return view('analytics.index', [
            'months' => $months,
            'monthlyBorrowings' => $monthlyBorrowings,
            'totalInventoryValue' => $totalInventoryValue,
            'periodBorrowingCount' => $periodBorrowingCount,
            'activeBorrowCount' => $activeBorrowCount,
            'availableItemCount' => $availableItemCount,
            'topBorrowedItems' => $topBorrowedItems,
            'chartScale' => $chartScale,
            'chartTicks' => range(0, $chartScale, (int) ($chartScale / 4)),
            'chartWidth' => $chartWidth,
            'plotX' => $plotX,
            'plotY' => $plotY,
            'plotWidth' => $plotWidth,
            'plotHeight' => $plotHeight,
            'barStep' => $barStep,
            'barWidth' => min(30, $barStep * 0.58),
        ]);
    }

    private function completedBorrowings(): Builder
    {
        return Transaction::query()
            ->where('transactions.type', 'borrow')
            ->whereNotIn('transactions.status', ['Menunggu Persetujuan', 'Ditolak']);
    }
}
