<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemUnitCodesTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_unit_is_saved_with_its_own_code(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
        $this->post(route('items.store'), $this->payload())
            ->assertSessionHasNoErrors()->assertRedirect(route('items.index'));

        $this->assertDatabaseCount('items', 3);
        foreach (['SAPU-01', 'SAPU-02', 'SAPU-03'] as $code) {
            $this->assertDatabaseHas('items', ['code' => $code, 'name' => 'Sapu', 'quantity' => 1]);
        }
    }

    public function test_duplicate_existing_or_missing_codes_do_not_save_any_units(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
        $payload = $this->payload();
        $payload['codes'] = ['SAPU-01', 'SAPU-01', 'SAPU-03'];
        $this->post(route('items.store'), $payload)->assertSessionHasErrors('codes.0');
        $this->assertDatabaseCount('items', 0);

        $payload['codes'] = ['SAPU-01'];
        $this->post(route('items.store'), $payload)->assertSessionHasErrors('codes');
        $this->assertDatabaseCount('items', 0);

        Item::create(['code' => 'SAPU-01', 'name' => 'Sapu lama', 'quantity' => 1, 'status' => 'Tersedia']);
        $this->post(route('items.store'), $this->payload())->assertSessionHasErrors('codes.0');
        $this->assertDatabaseCount('items', 1);
    }

    private function payload(): array
    {
        return [
            'name' => 'Sapu',
            'quantity' => 3,
            'codes' => ['SAPU-01', 'SAPU-02', 'SAPU-03'],
            'unit' => 'pcs',
            'condition' => 'Baik',
            'status' => 'Tersedia',
        ];
    }
}
