<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::where('is_active', true)->with(['category', 'location'])->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $items = $query->paginate(10)->appends($request->query());
        $categories = Category::all();
        $locations = Location::where('is_active', true)->orderBy('name')->get();

        return view('items.index', compact('items', 'categories', 'locations'));
    }

    public function create()
    {
        return view('items.create', [
            'itemNames' => Item::where('is_active', true)->select('name')->distinct()->orderBy('name')->pluck('name'),
            'itemBrands' => Item::where('is_active', true)->whereNotNull('brand')->where('brand', '!=', '')->select('brand')->distinct()->orderBy('brand')->pluck('brand'),
            'existingCodes' => Item::pluck('code'),
            'categories' => Category::all(),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'location_id' => ['nullable', Rule::exists('locations', 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'specification' => ['nullable', 'string'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'codes' => ['sometimes', 'required', 'array', 'size:' . (int) $request->input('quantity')],
            'codes.*' => ['required', 'string', 'max:255', 'distinct:ignore_case', Rule::unique('items', 'code')],
            'code' => ['nullable', 'string', 'max:255', Rule::unique('items', 'code')],
            'unit' => ['required', 'string', 'max:50'],
            'condition' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:Tersedia,Dipinjam,Dipindahkan,Hilang'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ]);

        $quantity = $validated['quantity'];
        $codes = $validated['codes'] ?? null;
        $firstCode = $validated['code'] ?? null;
        unset($validated['codes'], $validated['code']);
        $validated['quantity'] = 1;
        $validated['status'] = $validated['status'] ?? 'Tersedia';

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('items', 'public');
        }

        try {
            DB::transaction(function () use ($quantity, $codes, $firstCode, $validated) {
                for ($index = 0; $index < $quantity; $index++) {
                    Item::create(array_merge($validated, [
                        'code' => $codes[$index] ?? ($index === 0 && $firstCode ? $firstCode : Item::generateCode()),
                    ]));
                }
            });
        } catch (\Throwable $exception) {
            if (isset($validated['photo'])) {
                Storage::disk('public')->delete($validated['photo']);
            }
            throw $exception;
        }

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        $item->load(['category', 'location', 'transactions.user']);

        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        return view('items.edit', [
            'item' => $item,
            'categories' => Category::all(),
            'locations' => Location::where('is_active', true)
                ->orWhere('id', $item->location_id)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'location_id' => ['nullable', Rule::exists('locations', 'id')->where(function ($query) use ($item) {
                $query->where('is_active', true)->orWhere('id', $item->location_id);
            })],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'specification' => ['nullable', 'string'],
            'condition' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:Tersedia,Dipinjam,Dipindahkan,Hilang'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ]);

        if ($request->hasFile('photo')) {
            if ($item->photo && Storage::disk('public')->exists($item->photo)) {
                Storage::disk('public')->delete($item->photo);
            }

            $validated['photo'] = $request->file('photo')->store('items', 'public');
        }

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        if ($item->transactions()->exists()) {
            $item->update(['is_active' => false]);

            return redirect()->route('items.index')->with('success', 'Barang diarsipkan agar riwayat transaksinya tetap tersimpan.');
        }

        if ($item->photo && Storage::disk('public')->exists($item->photo)) {
            Storage::disk('public')->delete($item->photo);
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }

    public function exportCsv(Request $request)
    {
        $query = Item::where('is_active', true)->with(['category', 'location'])->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $items = $query->get();

        $headers = ['Kode', 'Nama', 'Kategori', 'Status', 'Kondisi', 'Lokasi', 'Jumlah', 'Satuan', 'Harga Beli', 'Tanggal Beli'];

        $callback = function () use ($items, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->code,
                    $item->name,
                    $item->category?->name ?? '-',
                    $item->status,
                    $item->condition,
                    $item->location?->name ?? '-',
                    $item->quantity,
                    $item->unit,
                    $item->purchase_price ?? 0,
                    $item->purchase_date ? $item->purchase_date->format('Y-m-d') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'items-export.csv');
    }

    public function history(Item $item)
    {
        $item->load(['transactions.user', 'category', 'location']);

        return view('items.history', compact('item'));
    }
}
