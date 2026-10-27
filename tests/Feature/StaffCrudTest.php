<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_staff_by_partial_name(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Staff::create(['name' => 'Budi Santoso']);
        Staff::create(['name' => 'Siti Aminah']);

        $this->actingAs($admin)
            ->get(route('staff.index', ['search' => 'Santoso']))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Aminah');

        $this->get(route('staff.index', ['search' => 'Tidak dikenal']))
            ->assertOk()
            ->assertSee('Tidak ada staf yang sesuai dengan pencarian.');

        $this->get(route('staff.index'))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('Siti Aminah');
    }

    public function test_admin_can_create_update_and_delete_staff(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        $this->post(route('staff.store'), ['name' => 'Staf Baru'])
            ->assertRedirect(route('staff.index'));

        $staff = Staff::where('name', 'Staf Baru')->firstOrFail();
        $this->assertDatabaseHas('staff', ['id' => $staff->id, 'is_active' => true]);

        $this->put(route('staff.update', $staff), [
            'name' => 'Staf Diperbarui',
            'is_active' => '0',
        ])->assertRedirect(route('staff.index'));

        $this->assertDatabaseHas('staff', [
            'id' => $staff->id,
            'name' => 'Staf Diperbarui',
            'is_active' => false,
        ]);

        $this->delete(route('staff.destroy', $staff))
            ->assertRedirect(route('staff.index'));

        $this->assertDatabaseMissing('staff', ['id' => $staff->id]);
    }

    public function test_borrow_form_only_offers_active_staff(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        Staff::create(['name' => 'Staf Aktif', 'is_active' => true]);
        Staff::create(['name' => 'Staf Nonaktif', 'is_active' => false]);

        $this->actingAs($user)
            ->get(route('transactions.borrow'))
            ->assertOk()
            ->assertSee('Staf Aktif')
            ->assertDontSee('Staf Nonaktif');
    }

    public function test_transfer_form_only_offers_active_staff(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        Staff::create(['name' => 'Staf Transfer Aktif', 'is_active' => true]);
        Staff::create(['name' => 'Staf Transfer Nonaktif', 'is_active' => false]);
        $location = Location::create(['name' => 'Gudang']);
        Item::create([
            'code' => 'INV-TRANSFER-1',
            'name' => 'Barang Transfer',
            'location_id' => $location->id,
            'quantity' => 1,
            'status' => 'Tersedia',
        ]);

        $this->actingAs($user)
            ->get(route('transactions.transfer'))
            ->assertOk()
            ->assertSee('Staf Transfer Aktif')
            ->assertDontSee('Staf Transfer Nonaktif');
    }
}
