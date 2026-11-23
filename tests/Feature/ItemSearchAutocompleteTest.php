<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchAutocompleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_search_suggests_active_items_with_name_code_and_brand(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Item::create([
            'code' => 'INV-SUGGEST-001',
            'name' => 'Alat Demo',
            'brand' => 'Canon',
            'quantity' => 1,
            'unit' => 'Unit',
            'status' => 'Tersedia',
            'is_active' => true,
        ]);
        Item::create([
            'code' => 'INV-SUGGEST-002',
            'name' => 'Barang Nonaktif',
            'quantity' => 1,
            'unit' => 'Unit',
            'status' => 'Tersedia',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('items.index', ['search' => 'INV-SUGGEST-001']))
            ->assertOk()
            ->assertSee('id="itemSearchMenu"', false)
            ->assertSee('data-value="INV-SUGGEST-001"', false)
            ->assertSee('data-search="Alat Demo INV-SUGGEST-001 Canon"', false)
            ->assertDontSee('<datalist id="itemSearchSuggestions">', false)
            ->assertDontSee('data-value="INV-SUGGEST-002"', false);
    }

    public function test_item_table_shows_each_item_brand(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Item::create([
            'code' => 'INV-BRAND-001',
            'name' => 'Kursi Baca',
            'brand' => 'Olympic',
            'quantity' => 1,
            'unit' => 'Unit',
            'status' => 'Tersedia',
            'is_active' => true,
        ]);
        Item::create([
            'code' => 'INV-BRAND-002',
            'name' => 'Meja Baca',
            'brand' => 'IKEA',
            'quantity' => 1,
            'unit' => 'Unit',
            'status' => 'Tersedia',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('items.index'))
            ->assertOk()
            ->assertSee('<th>Merek</th>', false)
            ->assertSee('<td>Olympic</td>', false)
            ->assertSee('<td>IKEA</td>', false);

        $this->get(route('items.index', ['search' => 'Olympic']))
            ->assertOk()
            ->assertSee('<td>Kursi Baca</td>', false)
            ->assertSee('<td>Olympic</td>', false)
            ->assertDontSee('<td>Meja Baca</td>', false)
            ->assertDontSee('<td>IKEA</td>', false);

        $this->get(route('items.index', ['search' => 'Kursi Baca']))
            ->assertOk()
            ->assertSee('<td>INV-BRAND-001</td>', false);
    }

    public function test_item_table_uses_distinct_badge_classes_for_each_status(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        foreach ([
            ['AVAILABLE', 'Tersedia', 'bg-success'],
            ['BORROWED', 'Dipinjam', 'bg-warning text-dark'],
            ['LOST', 'Hilang', 'bg-danger'],
        ] as [$code, $status, $badgeClass]) {
            Item::create([
                'code' => 'INV-STATUS-' . $code,
                'name' => 'Barang ' . $code,
                'quantity' => 1,
                'unit' => 'Unit',
                'status' => $status,
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($admin)->get(route('items.index'));
        $response->assertOk();

        foreach ([
            ['Tersedia', 'bg-success'],
            ['Dipinjam', 'bg-warning text-dark'],
            ['Hilang', 'bg-danger'],
        ] as [$status, $badgeClass]) {
            $response->assertSee('<span class="badge item-status-badge ' . $badgeClass . '">' . $status . '</span>', false);
        }
    }
}