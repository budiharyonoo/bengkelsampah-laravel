<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AdminDeletionException;
use App\Models\Admin;
use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Service for admin management business logic.
 *
 * Handles admin CRUD operations with proper business rules:
 * - Password hashing
 * - Prevention of deleting last admin
 * - Error handling and logging
 */
class AdminService
{
    public function __construct(
        private readonly AdminRepository $adminRepository
    ) {}

    /**
     * Get admin by ID with bank sampah relation.
     *
     * @param  int  $id  Admin ID
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getAdmin(int $id): Admin
    {
        return $this->adminRepository->findWithBankSampah($id);
    }

    /**
     * Create a new admin.
     *
     * @param  array{name: string, email: string, password: string, id_bank_sampah: int}  $data
     *
     * @throws \Exception
     */
    public function createAdmin(array $data): Admin
    {
        try {
            $adminData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'cabang',
                'id_bank_sampah' => $data['id_bank_sampah'],
            ];

            return $this->adminRepository->create($adminData);
        } catch (\Exception $e) {
            Log::error('Failed to create admin', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    /**
     * Update an existing admin.
     *
     * @param  int  $id  Admin ID
     * @param  array{name: string, email: string, password?: string, id_bank_sampah: int}  $data
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function updateAdmin(int $id, array $data): Admin
    {
        try {
            $admin = $this->adminRepository->findOrFail($id);

            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'id_bank_sampah' => $data['id_bank_sampah'],
            ];

            // Only update password if provided
            if (! empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            return $this->adminRepository->update($admin, $updateData);
        } catch (\Exception $e) {
            Log::error('Failed to update admin', ['admin_id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete an admin.
     *
     * Business rule: Cannot delete the last admin of a bank sampah.
     *
     * @param  int  $id  Admin ID
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws AdminDeletionException
     */
    public function deleteAdmin(int $id): bool
    {
        $admin = $this->adminRepository->findOrFail($id);

        // Check if this is the last admin for the bank sampah
        $adminCount = $this->adminRepository->countByBankSampah($admin->id_bank_sampah);

        if ($adminCount <= 1) {
            throw new AdminDeletionException(
                'Tidak dapat menghapus admin terakhir dari bank sampah ini'
            );
        }

        return $this->adminRepository->delete($admin);
    }
}
