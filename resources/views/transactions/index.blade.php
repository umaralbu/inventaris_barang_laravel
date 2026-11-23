@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="mb-0">Daftar Transaksi</h4>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('transactions.export', request()->query()) }}" class="btn btn-outline-secondary">Ekspor CSV</a>
            @if(auth()->user()->role !== 'admin')
                <a href="{{ route('transactions.borrow') }}" class="btn btn-primary">Ambil Barang</a>
                <a href="{{ route('transactions.return') }}" class="btn btn-outline-light">Kembalikan Barang</a>
            @endif
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('transactions.transfer') }}" class="btn btn-secondary">Pindahkan Barang</a>
            @endif
        </div>
    </div>

    @include('transactions.partials.overdue-notice')

    <div class="card p-3 mb-4">
        <form class="row g-3" method="GET" action="{{ route('transactions.index') }}">
            <div class="col-md-2">
                <label class="form-label">Jenis</label>
                <select name="type" class="form-select" data-searchable-select aria-label="Filter jenis transaksi">
                    <option value="">Semua</option>
                    <option value="borrow" {{ request('type') == 'borrow' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="loss" {{ request('type') == 'loss' ? 'selected' : '' }}>Laporan Hilang</option>
                    <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="lost" {{ request('type') == 'lost' ? 'selected' : '' }}>Hilang</option>
                    <option value="found" {{ request('type') == 'found' ? 'selected' : '' }}>Ditemukan</option>
                    <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Dipindahkan</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Waktu (Jam)</label>
                <input type="time" name="time" value="{{ request('time') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">User</label>
                <select name="user_id" class="form-select" data-searchable-select aria-label="Filter pengguna">
                    <option value="">Semua</option>
                    @foreach(App\Models\User::all() as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button class="btn btn-primary flex-fill" type="submit">Filter</button>
                @if(request()->hasAny(['type', 'date', 'time', 'user_id']))
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-light">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card p-3">
        <div class="table-wrap table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Kode Barang</th>
                        <th>Dipinjam oleh</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Tanggal & Waktu</th>
                        <th>Jatuh Tempo</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        @php
                            $status = $transaction->status ?? match ($transaction->type) {
                                'borrow' => 'Dipinjam',
                                'return' => 'Dikembalikan',
                                'transfer' => 'Dipindahkan',
                                default => 'Aktif',
                            };
                            $badgeClass = match ($status) {
                                'Dipinjam' => 'bg-warning text-dark',
                                'Menunggu Persetujuan' => 'bg-secondary',
                                'Dikembalikan' => 'bg-success',
                                'Hilang' => 'bg-dark',
                                'Ditemukan' => 'bg-success',
                                'Ditolak' => 'bg-danger',
                                'Dipindahkan' => 'bg-info',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <td>
                                <div>{{ $transaction->item->name ?? '-' }}</div>
                                @if($transaction->item?->brand)
                                    <small class="text-secondary opacity-75">{{ $transaction->item->brand }}</small>
                                @endif
                            </td>
                            <td>{{ $transaction->item->code ?? '-' }}</td>
                            <td>{{ $transaction->to_person ?? $transaction->user->name ?? '-' }}</td>
                            <td>{{ $transaction->clean_notes }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }}">
                                    {{ $status }}
                                </span>
                                @if($transaction->type === 'borrow' && $transaction->lossReports->contains(fn ($report) => $report->status === 'Menunggu Konfirmasi'))
                                    <div><span class="badge bg-warning text-dark mt-1">Laporan hilang menunggu konfirmasi</span></div>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $transaction->formatted_transaction_date }}</td>
                            <td class="text-nowrap">
                                @if($transaction->type === 'borrow' && $transaction->due_date)
                                    {{ $transaction->formatted_due_date }}
                                    @if($transaction->is_overdue)
                                        <span class="badge bg-danger ms-1">Terlambat</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-secondary">Detail</a>
                                    @if(auth()->user()->role === 'admin' && $transaction->type === 'borrow')
                                        @if($transaction->status === 'Menunggu Persetujuan')
                                            <form action="{{ route('transactions.approve', $transaction) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                                            </form>
                                            <form action="{{ route('transactions.reject', $transaction) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">Tolak</button>
                                            </form>
                                        @endif
                                        @if(in_array($transaction->status, ['Menunggu Persetujuan', 'Ditolak'], true))
                                            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengajuan pinjaman ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                            </form>
                                        @endif
                                    @endif
                                    @if(auth()->user()->role === 'admin' && $transaction->type === 'borrow')
                                        @php($pendingLossReport = $transaction->lossReports->firstWhere('status', 'Menunggu Konfirmasi'))
                                        @if($pendingLossReport)
                                            <form action="{{ route('transactions.confirm-lost', $pendingLossReport) }}" method="POST" class="d-inline" onsubmit="return confirm('Konfirmasi barang hilang? Pinjaman akan ditutup sebagai hilang, bukan dikembalikan.');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">Konfirmasi Hilang</button>
                                            </form>
                                            <form action="{{ route('transactions.reject-lost', $pendingLossReport) }}" method="POST" class="d-inline" onsubmit="return confirm('Tolak laporan kehilangan ini? Pinjaman akan tetap aktif.');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Tolak Laporan</button>
                                            </form>
                                        @endif
                                    @endif
                                    @if(auth()->user()->role === 'admin' && $transaction->type === 'borrow' && $transaction->status === 'Dikembalikan' && $transaction->returnTransaction)
                                        <button type="button"
                                                class="btn btn-sm btn-danger"
                                                title="Hapus tracking pengembalian"
                                                aria-label="Hapus tracking pengembalian"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteTransactionWarningModal"
                                                data-delete-url="{{ route('transactions.destroy', $transaction) }}">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-secondary">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $transactions->links() }}
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
        <style>
            .transaction-delete-dialog {
                width: calc(100% - 2rem);
                max-width: 540px;
            }

            .transaction-delete-panel {
                overflow: hidden;
                border: 0;
                border-radius: 18px;
                background: #fff;
                box-shadow: 0 16px 40px rgba(15, 23, 42, .18);
                color: #111827;
                font-family: 'Poppins', sans-serif;
                font-weight: 500;
            }

            .transaction-delete-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: .75rem;
                padding: 1.25rem 1.5rem .5rem;
                border: 0;
            }

            .transaction-delete-heading {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .transaction-delete-icon {
                display: inline-flex;
                flex: 0 0 60px;
                width: 60px;
                height: 60px;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background: #fee2e2;
                color: #b91c1c;
                font-size: 1.6rem;
            }

            .transaction-delete-title {
                margin: 0;
                color: #111827;
                font-size: clamp(1rem, 1.35vw, 1.3rem);
                font-weight: 700;
                line-height: 1.2;
            }

            .transaction-delete-close {
                display: inline-flex;
                flex: 0 0 40px;
                width: 40px;
                height: 40px;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 50%;
                background: #f3f4f6;
                color: #9ca3af;
                font-size: 1rem;
            }

            .transaction-delete-close:hover {
                background: #e5e7eb;
                color: #4b5563;
            }

            .transaction-delete-body {
                padding: .65rem 1.5rem;
            }

            .transaction-delete-copy {
                margin: 0 0 .9rem;
                color: #111827;
                font-size: clamp(.95rem, 1.3vw, 1.05rem);
                line-height: 1.55;
            }

            .transaction-delete-stock-note {
                display: flex;
                align-items: center;
                gap: .7rem;
                padding: .7rem .9rem;
                border-radius: 9px;
                background: #eef3f8;
                color: #475569;
                font-size: .9rem;
                line-height: 1.5;
            }

            .transaction-delete-stock-note i {
                flex: 0 0 auto;
                font-size: 1.1rem;
            }

            .transaction-delete-footer {
                display: flex;
                justify-content: flex-end;
                gap: .65rem;
                padding: .9rem 1.5rem 1.25rem;
                border: 0;
            }

            .transaction-delete-button {
                min-height: 42px;
                padding: .55rem 1rem;
                border-radius: 8px;
                font-family: 'Poppins', sans-serif;
                font-size: .9rem;
                font-weight: 500;
            }

            .transaction-delete-cancel {
                border: 1px solid #475569;
                background: #fff;
                color: #374151;
            }

            .transaction-delete-cancel:hover {
                background: #f3f4f6;
                color: #111827;
            }

            .transaction-delete-confirm {
                border: 1px solid #dc2626;
                background: #dc2626;
                color: #fff;
            }

            .transaction-delete-confirm:hover {
                border-color: #b91c1c;
                background: #b91c1c;
                color: #fff;
            }

            @media (max-width: 575.98px) {
                .transaction-delete-header {
                    padding: 1rem 1rem .5rem;
                }

                .transaction-delete-heading {
                    gap: .7rem;
                }

                .transaction-delete-icon {
                    flex-basis: 48px;
                    width: 48px;
                    height: 48px;
                    font-size: 1.3rem;
                }

                .transaction-delete-close {
                    flex-basis: 34px;
                    width: 34px;
                    height: 34px;
                }

                .transaction-delete-body {
                    padding: .6rem 1rem;
                }

                .transaction-delete-footer {
                    flex-wrap: wrap;
                    padding: .75rem 1rem 1rem;
                }

                .transaction-delete-button {
                    min-height: 38px;
                    padding: .5rem .75rem;
                    font-size: .82rem;
                }
            }
        </style>

        <div class="modal fade" id="deleteTransactionWarningModal" tabindex="-1" aria-labelledby="deleteTransactionWarningModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered transaction-delete-dialog">
                <div class="modal-content transaction-delete-panel">
                    <div class="modal-header transaction-delete-header">
                        <div class="transaction-delete-heading">
                            <span class="transaction-delete-icon" aria-hidden="true">
                                <i class="bi bi-exclamation-triangle"></i>
                            </span>
                            <h5 class="modal-title transaction-delete-title" id="deleteTransactionWarningModalLabel">Hapus Riwayat Transaksi?</h5>
                        </div>
                        <button type="button" class="transaction-delete-close" data-bs-dismiss="modal" aria-label="Tutup">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="modal-body transaction-delete-body">
                        <p class="transaction-delete-copy">
                            Catatan peminjaman dan pengembalian ini akan <strong>dihapus permanen</strong> dan tidak dapat
                            <strong>dipulihkan kembali</strong>.
                        </p>
                        <div class="transaction-delete-stock-note">
                            <i class="bi bi-info-circle" aria-hidden="true"></i>
                            <span>Tenang, jumlah stok barang tidak akan berubah.</span>
                        </div>
                    </div>
                    <div class="modal-footer transaction-delete-footer">
                        <button type="button" class="btn transaction-delete-button transaction-delete-cancel" data-bs-dismiss="modal">Batal</button>
                        <form id="deleteTransactionWarningForm" action="" method="POST" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn transaction-delete-button transaction-delete-confirm">
                                Ya, Hapus Permanen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const deleteModal = document.getElementById('deleteTransactionWarningModal');
                const deleteForm = document.getElementById('deleteTransactionWarningForm');

                if (!deleteModal || !deleteForm) return;

                deleteModal.addEventListener('show.bs.modal', function (event) {
                    const trigger = event.relatedTarget;
                    if (!(trigger instanceof HTMLElement)) return;
                    deleteForm.action = trigger.dataset.deleteUrl;
                });
            });
        </script>
    @endif
@endsection
