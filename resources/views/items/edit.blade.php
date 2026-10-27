@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
    <div class="card p-4">
        <h4 class="mb-3">Edit Barang</h4>
        <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kode Inventaris</label>
                    <input type="text" name="code" class="form-control" value="{{ old('code', $item->code) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">Pilih kategori</option>
                        @foreach(App\Models\Category::all() as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lokasi</label>
                    <select name="location_id" class="form-select">
                        <option value="">Pilih lokasi</option>
                        @foreach(App\Models\Location::where('is_active', true)->orWhere('id', $item->location_id)->orderBy('name')->get() as $location)
                            <option value="{{ $location->id }}" {{ old('location_id', $item->location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Merek</label>
                    <input type="text" name="brand" class="form-control" value="{{ old('brand', $item->brand) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Spesifikasi</label>
                    <textarea name="specification" class="form-control" rows="3">{{ old('specification', $item->specification) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Keterangan</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $item->description) }}</textarea>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $item->quantity) }}" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Satuan</label>
                    <input type="text" name="unit" class="form-control" value="{{ old('unit', $item->unit) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kondisi</label>
                    <select name="condition" class="form-select">
                        @foreach(['Baik', 'Rusak', 'Perlu Perbaikan'] as $cond)
                            <option value="{{ $cond }}" {{ old('condition', $item->condition) == $cond ? 'selected' : '' }}>{{ $cond }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['Tersedia', 'Dipinjam', 'Dipindahkan', 'Hilang'] as $status)
                            <option value="{{ $status }}" {{ old('status', $item->status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Pembelian</label>
                    <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', optional($item->purchase_date)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price', $item->purchase_price) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Foto Barang</label>
                    <input type="file" name="photo" class="form-control">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('items.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
@endsection
