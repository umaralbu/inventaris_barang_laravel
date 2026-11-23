<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemBrandPhotoReuseTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_view_receives_brand_photos_map(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Item::create([
            'code' => 'INV-0001',
            'name' => 'Mouse Wireless',
            'brand' => 'Logitech',
            'quantity' => 1,
            'status' => 'Tersedia',
            'photo' => 'items/logitech-mouse.jpg',
            'is_active' => true,
        ]);

        Item::create([
            'code' => 'INV-0002',
            'name' => 'Keyboard Mekanik',
            'brand' => 'Keychron',
            'quantity' => 1,
            'status' => 'Tersedia',
            'photo' => null,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('items.create'));

        $response->assertOk()
            ->assertViewHas('brandPhotos', function ($photos) {
                return isset($photos['Logitech']) &&
                    $photos['Logitech']['path'] === 'items/logitech-mouse.jpg';
            })
            ->assertViewHas('brandsByItemName', function ($grouped) {
                return isset($grouped['Mouse Wireless']) &&
                    in_array('Logitech', $grouped['Mouse Wireless'], true);
            });
    }

    public function test_new_item_reuses_existing_photo_if_same_brand_and_no_new_photo_uploaded(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Item::create([
            'code' => 'INV-0001',
            'name' => 'Mouse M170',
            'brand' => 'Logitech',
            'quantity' => 1,
            'status' => 'Tersedia',
            'photo' => 'items/logitech-reference.jpg',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('items.store'), [
            'name' => 'Keyboard K380',
            'brand' => 'Logitech',
            'quantity' => 1,
            'unit' => 'unit',
            'condition' => 'Baik',
            'status' => 'Tersedia',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('items.index'));

        $newItem = Item::where('name', 'Keyboard K380')->first();
        $this->assertNotNull($newItem);
        $this->assertSame('Logitech', $newItem->brand);
        $this->assertSame('items/logitech-reference.jpg', $newItem->photo);
    }

    public function test_new_item_can_override_existing_brand_photo_with_new_uploaded_photo(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Item::create([
            'code' => 'INV-0001',
            'name' => 'Mouse M170',
            'brand' => 'Logitech',
            'quantity' => 1,
            'status' => 'Tersedia',
            'photo' => 'items/logitech-old.jpg',
            'is_active' => true,
        ]);

        $file = UploadedFile::fake()->image('custom-photo.jpg');

        $response = $this->actingAs($admin)->post(route('items.store'), [
            'name' => 'Webcam C922',
            'brand' => 'Logitech',
            'quantity' => 1,
            'unit' => 'unit',
            'condition' => 'Baik',
            'status' => 'Tersedia',
            'photo' => $file,
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('items.index'));

        $newItem = Item::where('name', 'Webcam C922')->first();
        $this->assertNotNull($newItem);
        $this->assertNotSame('items/logitech-old.jpg', $newItem->photo);
        $this->assertTrue(str_starts_with($newItem->photo, 'items/'));
        Storage::disk('public')->assertExists($newItem->photo);
    }

    public function test_multiple_units_inherit_brand_photo(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Item::create([
            'code' => 'INV-0001',
            'name' => 'Monitor 24 Inch',
            'brand' => 'Dell',
            'quantity' => 1,
            'status' => 'Tersedia',
            'photo' => 'items/dell-display.jpg',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('items.store'), [
            'name' => 'Monitor 27 Inch',
            'brand' => 'Dell',
            'quantity' => 3,
            'unit' => 'unit',
            'condition' => 'Baik',
            'status' => 'Tersedia',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect(route('items.index'));

        $items = Item::where('name', 'Monitor 27 Inch')->get();
        $this->assertCount(3, $items);
        foreach ($items as $item) {
            $this->assertSame('items/dell-display.jpg', $item->photo);
        }
    }

    public function test_generate_codes_produces_unique_sequential_codes(): void
    {
        Item::create([
            'code' => 'INV-0001',
            'name' => 'Existing 1',
            'quantity' => 1,
            'status' => 'Tersedia',
        ]);

        $codes = Item::generateCodes(3);
        $this->assertCount(3, $codes);
        $this->assertEquals(['INV-0002', 'INV-0003', 'INV-0004'], $codes);
        $this->assertCount(3, array_unique($codes));
    }
}

