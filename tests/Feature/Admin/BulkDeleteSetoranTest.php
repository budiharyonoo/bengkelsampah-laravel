<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Point;
use App\Models\Setoran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkDeleteSetoranTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'id_bank_sampah' => null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function makeSetoranData(array $overrides = []): array
    {
        return array_merge([
            'user_id' => 1,
            'user_name' => 'User One',
            'user_identifier' => 'USR001',
            'bank_sampah_id' => 1,
            'bank_sampah_name' => 'BS Test',
            'bank_sampah_code' => 'BST001',
            'bank_sampah_address' => 'Jl. Test',
            'bank_sampah_phone' => '081234567890',
            'address_id' => 1,
            'address_name' => 'Rumah',
            'address_phone' => '081234567890',
            'address_full_address' => 'Jl. Test No. 1',
            'address_is_default' => true,
            'tipe_setor' => 'jual',
            'status' => 'selesai',
            'items_json' => '[]',
            'estimasi_total' => 10000,
            'aktual_total' => 10000,
            'tipe_layanan' => 'tempat',
        ], $overrides);
    }

    public function test_bulk_delete_setoran_with_related_points(): void
    {
        $setoran1 = Setoran::create($this->makeSetoranData());
        $setoran2 = Setoran::create($this->makeSetoranData(['status' => 'konfirmasi', 'aktual_total' => 0]));

        Point::create([
            'user_id' => 1,
            'user_name' => 'User One',
            'user_identifier' => 'USR001',
            'type' => 'setor',
            'tanggal' => now(),
            'jumlah_point' => 100,
            'xp' => 50,
            'setoran_id' => $setoran1->id,
        ]);

        Point::create([
            'user_id' => 1,
            'user_name' => 'User One',
            'user_identifier' => 'USR001',
            'type' => 'setor',
            'tanggal' => now(),
            'jumlah_point' => 50,
            'xp' => 25,
            'setoran_id' => $setoran2->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->postJson(route('dashboard.setoran.bulk-destroy'), [
                'ids' => [$setoran1->id, $setoran2->id],
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('setorans', ['id' => $setoran1->id]);
        $this->assertDatabaseMissing('setorans', ['id' => $setoran2->id]);
        $this->assertDatabaseMissing('points', ['setoran_id' => $setoran1->id]);
        $this->assertDatabaseMissing('points', ['setoran_id' => $setoran2->id]);
    }

    public function test_bulk_delete_validation_error_empty_ids(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->postJson(route('dashboard.setoran.bulk-destroy'), [
                'ids' => [],
            ]);

        $response->assertUnprocessable()
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure(['data' => ['ids']]);
    }

    public function test_bulk_delete_validation_error_nonexistent_id(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->postJson(route('dashboard.setoran.bulk-destroy'), [
                'ids' => [99999],
            ]);

        $response->assertUnprocessable()
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure(['data' => ['ids.0']]);
    }

    public function test_bulk_delete_unauthenticated_request_rejected(): void
    {
        $response = $this->postJson(route('dashboard.setoran.bulk-destroy'), [
            'ids' => [1],
        ]);

        $response->assertRedirect(route('admin.login'));
    }
}
