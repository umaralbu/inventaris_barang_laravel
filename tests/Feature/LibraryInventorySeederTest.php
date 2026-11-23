<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryInventorySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_catalog_replaces_active_items_and_preserves_loan_history(): void
    {
        $oldLocation = Location::create(['name' => 'Ruang IT', 'description' => 'Lokasi lama']);
        $oldItem = Item::create([
            'code' => 'INV-0002',
            'name' => 'Barang Lama Dipinjam',
            'location_id' => $oldLocation->id,
            'quantity' => 1,
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

<<<<<<< HEAD
        $this->assertSame(8, Location::where('is_active', true)->count());
        $this->assertSame(50, Item::where('is_active', true)->count());
        $this->assertSame(51, Item::count());
        $this->assertSame(51, Item::distinct('code')->count('code'));
=======
        $this->assertSame(7, Location::where('is_active', true)->count());
        $this->assertSame(5, Category::count());
        $this->assertSame(100, Item::where('is_active', true)->count());
        $this->assertSame(100, Item::where('is_active', true)->distinct('code')->count('code'));
>>>>>>> e25ccdfd4d6d1172fe50c618a8832ff52a1632fe
        $this->assertFalse($oldItem->fresh()->is_active);
        $this->assertFalse($oldLocation->fresh()->is_active);
        $this->assertDatabaseHas('transactions', ['id' => $borrow->id, 'item_id' => $oldItem->id]);
        $this->assertDatabaseHas('items', [
            'code' => 'INV-38390',
            'name' => 'Mesin Laminating',
<<<<<<< HEAD
            'brand' => 'Fellowes',
=======
            'category_id' => Category::where('name', 'Peralatan Kantor')->value('id'),
>>>>>>> e25ccdfd4d6d1172fe50c618a8832ff52a1632fe
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

    public function test_demo_catalog_replaces_all_items_without_transaction_history(): void
    {
        $oldLocation = Location::create(['name' => 'Lokasi Lama']);
        Item::create([
            'code' => 'OLD-001',
            'name' => 'Barang Lama',
            'location_id' => $oldLocation->id,
            'quantity' => 1,
        ]);

        $this->seed(\Database\Seeders\LibraryInventorySeeder::class);

        $this->assertDatabaseCount('items', 50);
        $this->assertDatabaseCount('categories', 5);
        $this->assertSame(8, Location::where('is_active', true)->count());
        $this->assertDatabaseMissing('items', ['code' => 'OLD-001']);
        $this->assertSame(
            [10, 10, 10, 10, 10],
            Category::withCount('items')->orderBy('name')->pluck('items_count')->all()
        );
        $this->assertSame(50, Item::distinct('code')->count('code'));
    }
}