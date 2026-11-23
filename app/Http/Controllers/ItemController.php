<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::where('is_active', true)->with(['category', 'location'])->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
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
        $searchItems = Item::where('is_active', true)
            ->select('name', 'code', 'brand')
            ->orderBy('name')
            ->orderBy('code')
            ->get();

        return view('items.index', compact('items', 'categories', 'locations', 'searchItems'));
    }

    public function create()
    {
        $brandPhotos = Item::where('is_active', true)
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->whereNotNull('photo')
            ->where('photo', '!=', '')
            ->orderBy('id', 'desc')
            ->get()
            ->unique(fn ($item) => mb_strtolower(trim($item->brand)))
            ->mapWithKeys(function ($item) {
                return [trim($item->brand) => [
                    'path' => $item->photo,
                    'url' => asset('storage/' . $item->photo),
                ]];
            })
            ->toArray();

        $brandsByItemName = Item::where('is_active', true)
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->select('name', 'brand')
            ->distinct()
            ->orderBy('brand')
            ->get()
            ->groupBy('name')
            ->map(fn ($group) => $group->pluck('brand')->unique()->values())
            ->toArray();

        return view('items.create', [
            'itemNames' => Item::where('is_active', true)->select('name')->distinct()->orderBy('name')->pluck('name'),
            'itemBrands' => Item::where('is_active', true)->whereNotNull('brand')->where('brand', '!=', '')->select('brand')->distinct()->orderBy('brand')->pluck('brand'),
            'brandsByItemName' => $brandsByItemName,
            'brandPhotos' => $brandPhotos,
            'existingCodes' => Item::pluck('code'),
            'categories' => Category::all(),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        if ($request->has('items')) {
            return $this->storeMany($request);
        }

        if ($request->has('purchase_price') && is_string($request->purchase_price)) {
            $cleanedPrice = preg_replace('/[^0-9]/', '', $request->purchase_price);
            $request->merge(['purchase_price' => $cleanedPrice !== '' ? (int)$cleanedPrice : null]);
        }

        $request->merge([
            'unit' => $request->filled('unit') ? $request->input('unit') : 'pcs',
            'condition' => $request->filled('condition') ? $request->input('condition') : 'Baik',
            'status' => $request->filled('status') ? $request->input('status') : 'Tersedia',
        ]);

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
            'condition' => ['required', 'in:Baik,Rusak,Perlu Perbaikan'],
            'status' => ['required', 'in:Tersedia,Dipinjam,Hilang'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ], [
            'codes.*.distinct' => 'Setiap unit harus memiliki kode inventaris yang berbeda (tidak boleh duplikat).',
            'codes.*.unique' => 'Kode inventaris :input sudah terdaftar dalam sistem. Harap gunakan kode lain.',
            'codes.*.required' => 'Kode inventaris unit wajib diisi.',
            'codes.size' => 'Jumlah kode inventaris harus sesuai dengan jumlah unit (:size unit).',
        ]);

        $quantity = $validated['quantity'];
        $codes = $validated['codes'] ?? null;
        $firstCode = $validated['code'] ?? null;
        unset($validated['codes'], $validated['code']);
        $validated['quantity'] = 1;
        $validated['status'] = $validated['status'] ?? 'Tersedia';

        $photoUploaded = false;

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('items', 'public');
            $photoUploaded = true;
        } elseif (!empty($validated['brand'])) {
            $existingPhoto = Item::whereRaw('LOWER(TRIM(brand)) = ?', [mb_strtolower(trim($validated['brand']))])
                ->whereNotNull('photo')
                ->where('photo', '!=', '')
                ->latest('id')
                ->value('photo');

            if ($existingPhoto) {
                $validated['photo'] = $existingPhoto;
            }
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
            if ($photoUploaded && isset($validated['photo'])) {
                Storage::disk('public')->delete($validated['photo']);
            }
            throw $exception;
        }

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    private function storeMany(Request $request)
    {
        $rows = $request->input('items', []);
        $rows = is_array($rows)
            ? array_map(fn ($row) => is_array($row) ? $row : [], $rows)
            : [];
        $rules = [
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*' => ['required', 'array'],
        ];

        foreach ($rows as $index => $row) {
            $quantity = filter_var($row['quantity'] ?? null, FILTER_VALIDATE_INT);
            $codeCount = $quantity !== false && $quantity > 0 ? $quantity : 0;
            $rules["items.{$index}.category_id"] = ['nullable', 'exists:categories,id'];
            $rules["items.{$index}.location_id"] = ['nullable', Rule::exists('locations', 'id')->where('is_active', true)];
            $rules["items.{$index}.name"] = ['required', 'string', 'max:255'];
            $rules["items.{$index}.brand"] = ['nullable', 'string', 'max:255'];
            $rules["items.{$index}.specification"] = ['nullable', 'string'];
            $rules["items.{$index}.description"] = ['nullable', 'string'];
            $rules["items.{$index}.quantity"] = ['required', 'integer', 'min:1', 'max:1000'];
            $rules["items.{$index}.codes"] = ['required', 'array', "size:{$codeCount}"];
            $rules["items.{$index}.codes.*"] = ['required', 'string', 'max:255', 'distinct:ignore_case', Rule::unique('items', 'code')];
            $rules["items.{$index}.unit"] = ['required', 'string', 'max:50'];
            $rules["items.{$index}.condition"] = ['required', 'in:Baik,Rusak,Perlu Perbaikan'];
            $rules["items.{$index}.status"] = ['required', 'in:Tersedia,Dipinjam,Hilang'];
            $rules["items.{$index}.purchase_date"] = ['nullable', 'date'];
            $rules["items.{$index}.purchase_price"] = ['nullable', 'numeric', 'min:0'];
            $rules["items.{$index}.photo"] = ['nullable', 'image', 'mimes:jpg,jpeg,png'];
        }

        $validator = Validator::make($request->all(), $rules);
        $validator->after(function ($validator) use ($rows) {
            $seenCodes = [];
            foreach ($rows as $index => $row) {
                foreach (($row['codes'] ?? []) as $codeIndex => $code) {
                    if (!is_string($code) || trim($code) === '') {
                        continue;
                    }
                    $normalizedCode = mb_strtolower(trim($code));
                    if (isset($seenCodes[$normalizedCode])) {
                        $validator->errors()->add("items.{$index}.codes.{$codeIndex}", 'Setiap unit harus memiliki kode inventaris yang berbeda.');
                    } else {
                        $seenCodes[$normalizedCode] = true;
                    }
                }
            }
        });
        $validated = $validator->validate();

        $storedPhotos = [];
        try {
            foreach ($validated['items'] as $index => &$row) {
                if ($request->hasFile("items.{$index}.photo")) {
                    $row['photo'] = $request->file("items.{$index}.photo")->store('items', 'public');
                    $storedPhotos[] = $row['photo'];
                } elseif (!empty($row['brand'])) {
                    $existingPhoto = Item::whereRaw('LOWER(TRIM(brand)) = ?', [mb_strtolower(trim($row['brand']))])
                        ->whereNotNull('photo')
                        ->where('photo', '!=', '')
                        ->latest('id')
                        ->value('photo');

                    if ($existingPhoto) {
                        $row['photo'] = $existingPhoto;
                    } else {
                        unset($row['photo']);
                    }
                } else {
                    unset($row['photo']);
                }
            }
            unset($row);

            DB::transaction(function () use ($validated) {
                foreach ($validated['items'] as $row) {
                    $codes = $row['codes'];
                    unset($row['codes']);
                    $row['quantity'] = 1;

                    foreach ($codes as $code) {
                        Item::create(array_merge($row, ['code' => $code]));
                    }
                }
            });
        } catch (\Throwable $exception) {
            foreach ($storedPhotos as $photo) {
                Storage::disk('public')->delete($photo);
            }
            throw $exception;
        }

        return redirect()->route('items.index')->with('success', 'Semua barang berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        $item->load(['category', 'location', 'transactions.user']);

        return view('items.show', compact('item'));
    }

    public function photo(Item $item)
    {
        abort_unless($item->photo && Storage::disk('public')->exists($item->photo), 404);

        return Storage::disk('public')->response($item->photo);
    }

    public function edit(Item $item)
    {
        $brandsByItemName = Item::where('is_active', true)
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->select('name', 'brand')
            ->distinct()
            ->orderBy('brand')
            ->get()
            ->groupBy('name')
            ->map(fn ($group) => $group->pluck('brand')->unique()->values())
            ->toArray();

        if ($item->name && $item->brand) {
            $brandsByItemName[$item->name] = array_values(array_unique(array_merge(
                $brandsByItemName[$item->name] ?? [],
                [$item->brand]
            )));
        }

        return view('items.edit', [
            'item' => $item,
            'categories' => Category::all(),
            'locations' => Location::where('is_active', true)
                ->orWhere('id', $item->location_id)
                ->orderBy('name')
                ->get(),
            'itemNames' => Item::where('is_active', true)->select('name')->distinct()->orderBy('name')->pluck('name'),
            'itemBrands' => Item::where('is_active', true)->whereNotNull('brand')->where('brand', '!=', '')->select('brand')->distinct()->orderBy('brand')->pluck('brand'),
            'brandsByItemName' => $brandsByItemName,
        ]);
    }

    public function update(Request $request, Item $item)
    {
        if ($request->has('purchase_price') && is_string($request->purchase_price)) {
            $cleanedPrice = preg_replace('/[^0-9]/', '', $request->purchase_price);
            $request->merge(['purchase_price' => $cleanedPrice !== '' ? (int)$cleanedPrice : null]);
        }

        $request->merge([
            'unit' => $request->filled('unit') ? $request->input('unit') : 'pcs',
            'condition' => $request->filled('condition') ? $request->input('condition') : 'Baik',
            'status' => $request->filled('status') ? $request->input('status') : 'Tersedia',
        ]);

        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'location_id' => ['nullable', Rule::exists('locations', 'id')->where(function ($query) use ($item) {
                $query->where('is_active', true)->orWhere('id', $item->location_id);
            })],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'specification' => ['nullable', 'string'],
            'quantity' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'condition' => ['required', 'in:Baik,Rusak,Perlu Perbaikan'],
            'status' => ['required', 'in:Tersedia,Dipinjam,Hilang'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ]);

        $hasActiveBorrow = $item->transactions()
            ->where('type', 'borrow')
            ->where('status', 'Dipinjam')
            ->exists();

        if ($hasActiveBorrow && $validated['status'] === 'Hilang') {
            throw ValidationException::withMessages([
                'status' => 'Pinjaman barang ini masih aktif. Laporkan kehilangan dari detail transaksi agar admin dapat meninjau dan menutup pinjamannya dengan benar.',
            ]);
        }

        if ($hasActiveBorrow && ($validated['status'] !== 'Dipinjam' || $validated['condition'] === 'Rusak')) {
            throw ValidationException::withMessages([
                'status' => 'Barang masih memiliki pinjaman aktif. Perbarui status atau kondisi setelah pengembalian, atau gunakan alur laporan kehilangan.',
            ]);
        }

        $wasLost = $item->status === 'Hilang';

        if ($request->hasFile('photo')) {
            if ($item->photo && Storage::disk('public')->exists($item->photo)) {
                Storage::disk('public')->delete($item->photo);
            }

            $validated['photo'] = $request->file('photo')->store('items', 'public');
        }

        DB::transaction(function () use ($item, $validated, $wasLost) {
            $lostBorrow = $wasLost
                ? Transaction::where('item_id', $item->id)
                    ->where('type', 'borrow')
                    ->where('status', 'Hilang')
                    ->latest('transaction_date')
                    ->first()
                : null;

            $item->update($validated);

            if ($wasLost && $validated['status'] !== 'Hilang') {
                $lostBorrow?->update(['status' => 'Ditemukan']);

                Transaction::create([
                    'item_id' => $item->id,
                    'user_id' => Auth::id(),
                    'type' => 'recovery',
                    'status' => 'Ditemukan',
                    'from_location' => $item->location?->name,
                    'to_location' => $item->location?->name,
                    'from_person' => 'Tidak diketahui',
                    'to_person' => Auth::user()->name,
                    'quantity' => $item->quantity,
                    'transaction_date' => now('Asia/Jakarta'),
                    'condition' => $item->condition,
                    'notes' => 'Barang ditemukan dan diperiksa. Status barang diperbarui menjadi ' . $item->status . '.',
                ]);
            }
        });

        $successMessage = $wasLost && $validated['status'] !== 'Hilang'
            ? 'Barang diperbarui. Pastikan kondisi fisik dan statusnya sudah sesuai setelah ditemukan.'
            : 'Barang berhasil diperbarui.';

        return redirect()->route('items.index')->with('success', $successMessage);
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
