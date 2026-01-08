<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository for Admin data access.
 *
 * Handles all database queries related to admin users.
 */
class AdminRepository
{
    /**
     * Find admin by ID with bank sampah relation.
     *
     * @param int $id Admin ID
     * @return Admin
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findWithBankSampah(int $id): Admin
    {
        return Admin::query()
            ->with('bankSampah:id,nama_bank_sampah,kode_bank_sampah')
            ->findOrFail($id);
    }

    /**
     * Find admin by ID.
     *
     * @param int $id Admin ID
     * @return Admin
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Admin
    {
        return Admin::query()->findOrFail($id);
    }

    /**
     * Create a new admin.
     *
     * @param array{name: string, email: string, password: string, role: string, id_bank_sampah: int} $data
     * @return Admin
     */
    public function create(array $data): Admin
    {
        return Admin::create($data);
    }

    /**
     * Update an existing admin.
     *
     * @param Admin $admin Admin instance
     * @param array<string, mixed> $data Data to update
     * @return Admin
     */
    public function update(Admin $admin, array $data): Admin
    {
        $admin->update($data);

        return $admin->fresh();
    }

    /**
     * Delete an admin.
     *
     * @param Admin $admin Admin instance
     * @return bool
     */
    public function delete(Admin $admin): bool
    {
        return (bool) $admin->delete();
    }

    /**
     * Count admins for a specific bank sampah.
     *
     * @param int $bankSampahId Bank sampah ID
     * @return int
     */
    public function countByBankSampah(int $bankSampahId): int
    {
        return Admin::query()
            ->where('id_bank_sampah', $bankSampahId)
            ->count();
    }

    /**
     * Get all admins for a bank sampah.
     *
     * @param int $bankSampahId Bank sampah ID
     * @return Collection<int, Admin>
     */
    public function getByBankSampah(int $bankSampahId): Collection
    {
        return Admin::query()
            ->where('id_bank_sampah', $bankSampahId)
            ->with('bankSampah:id,nama_bank_sampah')
            ->get();
    }
}
