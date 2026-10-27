<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryInventorySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_library_catalog_has_unique_assets_and_archives_legacy_loan_safely(): void
    {
        $oldLocation = Location::create(['name' => 'Ruang IT', 'description' => 'Lokasi lama']);
        $oldItem = Item::create([
            'code' => 'INV-0002',
            'name' => 'Barang Lama Dipinjam',
            'location_id' => $oldLocation->id,
            'quantity' => 0,
            'status' => 'Dipinjam',
        ]);
        $borrow = Transaction::create([
            'item_id' => $oldItem->id,
            'type' => 'borrow',
            'status' => 'Dipinjam',
            'from_location' => 'Ruang IT',
            'to_location' => 'Ruang IT',
            'to_person' => 'Staf Lama',
            'quantity' => 1,
            'transaction_date' => now(),
        ]);

        $this->seed(\Database\Seeders\LibraryInventorySeeder::class);

        $this->assertSame(7, Location::where('is_active', true)->count());
        $this->assertSame(5, Category::count());
        $this->assertSame(100, Item::where('is_active', true)->count());
        $this->assertSame(100, Item::where('is_active', true)->distinct('code')->count('code'));
        $this->assertFalse($oldItem->fresh()->is_active);
        $this->assertFalse($oldLocation->fresh()->is_active);
        $this->assertDatabaseHas('transactions', ['id' => $borrow->id, 'item_id' => $oldItem->id]);
        $this->assertDatabaseHas('items', [
            'code' => 'INV-38390',
            'name' => 'Mesin Laminating',
            'category_id' => Category::where('name', 'Peralatan Kantor')->value('id'),
            'location_id' => Location::where('name', 'Ruang Kepala Perpustakaan')->value('id'),
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('items', [
            'code' => 'INV-72604',
            'name' => 'Sapu Lantai',
            'category_id' => Category::where('name', 'Peralatan Kebersihan')->value('id'),
            'location_id' => Location::where('name', 'Ruang Baca')->value('id'),
            'quantity' => 1,
        ]);

        $this->seed(\Database\Seeders\LibraryInventorySeeder::class);

        $this->assertSame(100, Item::where('is_active', true)->count());
        $this->assertDatabaseHas('transactions', ['id' => $borrow->id, 'item_id' => $oldItem->id]);
    }
}