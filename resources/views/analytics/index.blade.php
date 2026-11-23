@extends('layouts.app')

@section('title', 'Analitik Inventaris')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="mb-1">Analitik Inventaris</h4>
            <p class="text-secondary mb-0">Ringkasan nilai barang dan aktivitas peminjaman.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xxl-3">
            <section class="card analytics-summary-card h-100 p-3" aria-labelledby="inventoryValueHeading">
                <div class="d-flex align-items-center gap-3">
                    <div class="analytics-icon analytics-icon-value" aria-hidden="true"><i class="bi bi-cash-stack"></i></div>
                    <div class="min-w-0">
                        <h5 class="analytics-summary-label" id="inventoryValueHeading">Total Nilai Inventaris</h5>
                        <p class="analytics-summary-value">Rp {{ number_format((float) $totalInventoryValue, 0, ',', '.') }}</p>
                        <p class="analytics-summary-note">Harga satuan × jumlah unit barang aktif</p>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-12 col-sm-6 col-xxl-3">
            <section class="card analytics-summary-card h-100 p-3" aria-labelledby="periodBorrowingHeading">
                <div class="d-flex align-items-center gap-3">
                    <div class="analytics-icon analytics-icon-borrowing" aria-hidden="true"><i class="bi bi-bar-chart-line"></i></div>
                    <div class="min-w-0">
                        <h5 class="analytics-summary-label" id="periodBorrowingHeading">Peminjaman Periode Ini</h5>
                        <p class="analytics-summary-value">{{ number_format($periodBorrowingCount, 0, ',', '.') }}</p>
                        <p class="analytics-summary-note">Transaksi dalam {{ $months }} bulan terakhir</p>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-12 col-sm-6 col-xxl-3">
            <section class="card analytics-summary-card h-100 p-3" aria-labelledby="activeBorrowHeading">
                <div class="d-flex align-items-center gap-3">
                    <div class="analytics-icon analytics-icon-active" aria-hidden="true"><i class="bi bi-box-arrow-up-right"></i></div>
                    <div class="min-w-0">
                        <h5 class="analytics-summary-label" id="activeBorrowHeading">Barang Sedang Dipinjam</h5>
                        <p class="analytics-summary-value">{{ number_format($activeBorrowCount, 0, ',', '.') }}</p>
                        <p class="analytics-summary-note">Transaksi dengan status Dipinjam</p>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-12 col-sm-6 col-xxl-3">
            <section class="card analytics-summary-card h-100 p-3" aria-labelledby="availableItemHeading">
                <div class="d-flex align-items-center gap-3">
                    <div class="analytics-icon analytics-icon-available" aria-hidden="true"><i class="bi bi-box-seam"></i></div>
                    <div class="min-w-0">
                        <h5 class="analytics-summary-label" id="availableItemHeading">Barang Tersedia</h5>
                        <p class="analytics-summary-value">{{ number_format($availableItemCount, 0, ',', '.') }}</p>
                        <p class="analytics-summary-note">Unit aktif dalam kondisi siap digunakan</p>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xxl-8">
            <section class="card h-100 p-3 p-md-4" aria-labelledby="borrowingChartHeading">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h5 class="mb-1" id="borrowingChartHeading">Peminjaman Berdasarkan Waktu</h5>
                        <p class="text-secondary small mb-0">Jumlah transaksi peminjaman per bulan</p>
                    </div>
                    <form method="GET" action="{{ route('admin.analytics') }}" class="d-flex align-items-center gap-2">
                        <label for="months" class="small text-secondary">Periode</label>
                        <select id="months" name="months" class="form-select form-select-sm">
                            @foreach([6 => '6 bulan', 12 => '12 bulan', 24 => '24 bulan'] as $value => $label)
                                <option value="{{ $value }}" @selected($months === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-primary" type="submit">Terapkan</button>
                    </form>
                </div>

                @if($periodBorrowingCount === 0)
                    <p class="alert alert-light border small mb-3" role="status">Belum ada transaksi peminjaman pada periode ini.</p>
                @endif

                <div class="analytics-chart-scroll">
                    <svg class="analytics-chart" viewBox="0 0 {{ $chartWidth }} 316" role="img" aria-labelledby="analyticsChartTitle analyticsChartDescription">
                        <title id="analyticsChartTitle">Jumlah peminjaman selama {{ $months }} bulan terakhir</title>
                        <desc id="analyticsChartDescription">Grafik batang menunjukkan jumlah transaksi tiap bulan. Sumbu vertikal menunjukkan jumlah transaksi.</desc>
                        @foreach($chartTicks as $tick)
                            @php
                                $tickY = $plotY + $plotHeight - ($tick / $chartScale * $plotHeight);
                            @endphp
                            <line x1="{{ $plotX }}" y1="{{ $tickY }}" x2="{{ $plotX + $plotWidth }}" y2="{{ $tickY }}" class="analytics-chart-gridline" />
                            <text x="{{ $plotX - 10 }}" y="{{ $tickY + 4 }}" text-anchor="end" class="analytics-chart-axis-label">{{ $tick }}</text>
                        @endforeach
                        <text x="15" y="{{ $plotY + ($plotHeight / 2) }}" text-anchor="middle" class="analytics-chart-axis-title" transform="rotate(-90 15 {{ $plotY + ($plotHeight / 2) }})">Transaksi</text>

                        @foreach($monthlyBorrowings as $index => $month)
                            @php
                                $barHeight = $month['total'] / $chartScale * $plotHeight;
                                $barX = $plotX + ($index * $barStep) + (($barStep - $barWidth) / 2);
                                $barY = $plotY + $plotHeight - $barHeight;
                                $valueY = max($plotY + 12, $barY - 8);
                                $labelY = $plotY + $plotHeight + 25;
                                $showLabel = $months <= 12 || $loop->first || $loop->last || $loop->iteration % 2 === 1;
                            @endphp
                            <g>
                                <title>{{ $month['label'] }}: {{ $month['total'] }} transaksi peminjaman</title>
                                <rect x="{{ $barX }}" y="{{ $barY }}" width="{{ $barWidth }}" height="{{ $barHeight }}" rx="5" class="analytics-chart-bar" />
                                <text x="{{ $barX + ($barWidth / 2) }}" y="{{ $valueY }}" text-anchor="middle" class="analytics-chart-value">{{ $month['total'] }}</text>
                                @if($showLabel)
                                    <text x="{{ $barX + ($barWidth / 2) }}" y="{{ $labelY }}" text-anchor="middle" class="analytics-chart-month-label">{{ $month['label'] }}</text>
                                @endif
                            </g>
                        @endforeach
                    </svg>
                </div>
            </section>
        </div>

        <div class="col-12 col-xxl-4">
            <section class="card h-100 p-3 p-md-4" aria-labelledby="topBorrowedHeading">
                <div class="mb-3">
                    <h5 class="mb-1" id="topBorrowedHeading">Barang Paling Sering Dipinjam</h5>
                    <p class="text-secondary small mb-0">Peringkat selama {{ $months }} bulan terakhir</p>
                </div>

                @php
                    $topBorrowedMax = max(1, (int) $topBorrowedItems->max('total'));
                @endphp
                @forelse($topBorrowedItems as $index => $item)
                    @php
                        $itemBorrowingWidth = $item->total / $topBorrowedMax * 100;
                    @endphp
                    <div class="top-borrowed-item">
                        <div class="d-flex align-items-center gap-3">
                            <span class="top-borrowed-rank">{{ $index + 1 }}</span>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="min-w-0">
                                        <p class="top-borrowed-name text-truncate mb-0">{{ $item->name }}</p>
                                        <p class="top-borrowed-unit small mb-0">{{ $item->unit }}</p>
                                    </div>
                                    <span class="top-borrowed-count">{{ number_format($item->total, 0, ',', '.') }}</span>
                                </div>
                                <div class="top-borrowed-track" role="progressbar" aria-label="Peminjaman {{ $item->name }}" aria-valuenow="{{ $item->total }}" aria-valuemin="0" aria-valuemax="{{ $topBorrowedMax }}">
                                    <div class="top-borrowed-bar" style="width: {{ $itemBorrowingWidth }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="analytics-empty-state" role="status">
                        <i class="bi bi-inbox" aria-hidden="true"></i>
                        <p class="mb-0">Belum ada data peminjaman pada periode ini.</p>
                    </div>
                @endforelse
            </section>
        </div>
    </div>

    <style>
        .analytics-summary-card {
            border: 1px solid var(--border);
            border-radius: 14px;
        }

        .analytics-summary-card .min-w-0,
        .top-borrowed-item .min-w-0 {
            min-width: 0;
        }

        .analytics-icon {
            display: grid;
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            place-items: center;
            border-radius: 14px;
            font-size: 1.35rem;
        }

        .analytics-icon-value {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .analytics-icon-borrowing {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .analytics-icon-active {
            background: rgba(217, 185, 128, .18);
            color: var(--warning);
        }

        .analytics-icon-available {
            background: rgba(111, 159, 119, .16);
            color: var(--success);
        }

        .analytics-summary-label {
            overflow: hidden;
            margin: 0 0 .25rem;
            color: var(--muted);
            font-size: .85rem;
            font-weight: 600;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .analytics-summary-value {
            overflow-wrap: anywhere;
            margin: 0 0 .2rem;
            color: var(--text);
            font-size: clamp(1.2rem, 2vw, 1.55rem);
            font-weight: 700;
            line-height: 1.25;
        }

        .analytics-summary-note {
            margin: 0;
            color: var(--muted);
            font-size: .75rem;
            line-height: 1.4;
        }

        .analytics-chart-scroll {
            overflow-x: auto;
            padding: .25rem 0 .5rem;
        }

        .analytics-chart {
            display: block;
            width: 100%;
            min-width: {{ $chartWidth }}px;
            height: auto;
            overflow: visible;
        }

        .analytics-chart-gridline {
            stroke: var(--border);
            stroke-dasharray: 3 5;
            stroke-width: 1;
        }

        .analytics-chart-axis-label,
        .analytics-chart-month-label {
            fill: var(--muted);
            font-family: inherit;
            font-size: 11px;
        }

        .analytics-chart-axis-title {
            fill: var(--muted);
            font-family: inherit;
            font-size: 10px;
        }

        .analytics-chart-value {
            fill: var(--text);
            font-family: inherit;
            font-size: 11px;
            font-weight: 700;
        }

        .analytics-chart-bar {
            fill: var(--primary);
            transition: opacity .15s ease;
        }

        .analytics-chart-bar:hover {
            opacity: .72;
        }

        .top-borrowed-item {
            padding: .85rem 0;
            border-bottom: 1px solid var(--border);
        }

        .top-borrowed-item:last-child {
            border-bottom: 0;
        }

        .top-borrowed-rank {
            display: grid;
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            place-items: center;
            border-radius: 10px;
            background: var(--primary-soft);
            color: var(--primary);
            font-weight: 700;
        }

        .top-borrowed-name {
            color: var(--text);
            font-weight: 600;
        }

        .top-borrowed-unit,
        .top-borrowed-count {
            color: var(--muted);
        }

        .top-borrowed-count {
            font-size: .875rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .top-borrowed-track {
            height: 6px;
            margin-top: .55rem;
            overflow: hidden;
            border-radius: 999px;
            background: var(--primary-soft);
        }

        .top-borrowed-bar {
            height: 100%;
            border-radius: inherit;
            background: var(--primary);
        }

        .analytics-empty-state {
            display: grid;
            min-height: 180px;
            align-content: center;
            justify-items: center;
            gap: .65rem;
            color: var(--muted);
            text-align: center;
        }

        .analytics-empty-state i {
            color: var(--primary);
            font-size: 2rem;
        }

        @media (prefers-reduced-motion: reduce) {
            .analytics-chart-bar {
                transition: none;
            }
        }
    </style>
@endsection
