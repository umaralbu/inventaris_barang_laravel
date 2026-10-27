<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowStaffPickerTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_picker_lists_active_staff_and_restores_id_after_validation_failure(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $staff = Staff::create(['name' => 'Siti Aminah', 'is_active' => true]);
        Staff::create(['name' => 'Staf Nonaktif', 'is_active' => false]);

        $this->actingAs($user)->get(route('transactions.borrow'))
            ->assertOk()
            ->assertSee('id="borrowStaffPicker"', false)
            ->assertSee('data-label="Siti Aminah"', false)
            ->assertDontSee('Staf Nonaktif');

        $this->from(route('transactions.borrow'))->post(route('transactions.borrow'), ['staff_id' => $staff->id])
            ->assertRedirect(route('transactions.borrow'))
            ->assertSessionHasErrors('item_id')
            ->assertSessionHasInput('staff_id', $staff->id);

        $this->get(route('transactions.borrow'))->assertOk()
            ->assertSee('value="' . $staff->id . '" selected>Siti Aminah', false);

        $this->post(route('transactions.borrow'), ['staff_id' => 'Siti Aminah'])
            ->assertSessionHasErrors('staff_id');
    }
}
