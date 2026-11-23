@if($overdueTransactions->isNotEmpty())
    <div id="overdue-notice" class="alert alert-danger mb-4" role="alert" aria-live="polite">
        <h5 class="alert-heading fs-6 fw-bold">Peringatan pengembalian terlambat</h5>
        <p class="mb-2">Ada {{ $overdueTransactions->count() }} barang yang belum dikembalikan melewati batas waktu:</p>
        <ul class="mb-0 ps-3">
            @foreach($overdueTransactions as $overdueTransaction)
                <li>
                    <strong>{{ $overdueTransaction->item?->name ?? 'Barang tidak diketahui' }}</strong>
                    ({{ $overdueTransaction->item?->code ?? '-' }}) —
                    dipinjam oleh {{ $overdueTransaction->to_person ?? $overdueTransaction->user?->name ?? '-' }},
                    jatuh tempo {{ $overdueTransaction->formatted_due_date }}.
                </li>
            @endforeach
        </ul>
    </div>
@endif
