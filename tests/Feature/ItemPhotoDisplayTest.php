<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemPhotoDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_detail_displays_photo_from_public_storage_without_storage_link(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $photo = UploadedFile::fake()->image('item.jpg');
        $photoPath = $photo->store('items', 'public');
        $item = Item::create([
            'code' => 'INV-0001',
            'name' => 'Laptop',
            'quantity' => 1,
            'status' => 'Tersedia',
            'photo' => $photoPath,
        ]);

        $detailResponse = $this->actingAs($admin)->get(route('items.show', $item));

        $detailResponse->assertOk()
            ->assertSee(route('items.photo', $item), false)
            ->assertSee('alt="Foto Laptop"', false);

        $this->get(route('items.photo', $item))
            ->assertOk()
            ->assertHeader('content-type', 'image/jpeg');
    }
}
