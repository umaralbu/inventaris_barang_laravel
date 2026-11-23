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

    public function test_multiple_item_rows_save_each_item_with_its_own_codes_and_details(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
        $this->post(route('items.store'), [
            'items' => [
                [
                    'name' => 'Sapu',
                    'brand' => 'CleanCo',
                    'quantity' => 2,
                    'codes' => ['MULTI-SAPU-01', 'MULTI-SAPU-02'],
                    'unit' => 'pcs',
                    'condition' => 'Baik',
                    'status' => 'Tersedia',
                    'specification' => 'Gagang panjang',
                ],
                [
                    'name' => 'Ember',
                    'brand' => 'Plastik Jaya',
                    'quantity' => 1,
                    'codes' => ['MULTI-EMBER-01'],
                    'unit' => 'unit',
                    'condition' => 'Baik',
                    'status' => 'Tersedia',
                    'description' => 'Untuk kebersihan',
                ],
            ],
        ])->assertSessionHasNoErrors()->assertRedirect(route('items.index'));

        $this->assertDatabaseCount('items', 3);
        $this->assertDatabaseHas('items', [
            'code' => 'MULTI-SAPU-02',
            'name' => 'Sapu',
            'brand' => 'CleanCo',
            'quantity' => 1,
            'specification' => 'Gagang panjang',
        ]);
        $this->assertDatabaseHas('items', [
            'code' => 'MULTI-EMBER-01',
            'name' => 'Ember',
            'brand' => 'Plastik Jaya',
            'quantity' => 1,
            'description' => 'Untuk kebersihan',
        ]);
    }

    public function test_multiple_item_rows_reject_duplicate_codes_without_saving_any_items(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
        $this->post(route('items.store'), [
            'items' => [
                ['name' => 'Sapu', 'quantity' => 1, 'codes' => ['DUP-CODE'], 'unit' => 'pcs', 'condition' => 'Baik', 'status' => 'Tersedia'],
                ['name' => 'Ember', 'quantity' => 1, 'codes' => ['dup-code'], 'unit' => 'pcs', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ],
        ])->assertSessionHasErrors('items.1.codes.0');

        $this->assertDatabaseCount('items', 0);
    }

    public function test_multiple_item_rows_require_one_inventory_code_per_unit(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
        $this->post(route('items.store'), [
            'items' => [
                ['name' => 'Sapu', 'quantity' => 2, 'codes' => ['ONLY-ONE'], 'unit' => 'pcs', 'condition' => 'Baik', 'status' => 'Tersedia'],
            ],
        ])->assertSessionHasErrors('items.0.codes');

        $this->assertDatabaseCount('items', 0);
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
