<?php

namespace App\Service;

use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleService
{
    /**
     * Get all roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRoles()
    {
        try {
            // Retrieve all roles from the database
            return Role::all();
        } catch (\Exception $e) {
            Log::error('Failed to retrieve roles: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Create a new role.
     *
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function createRole($data)
    {
        try {
            // Create a new role using the provided data
            Role::create([
                'role_name' => $data['role_name'],
                'description' => $data['description'],
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Error creating role: ' . $e->getMessage());
            throw new \Exception('Error creating role: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing role.
     *
     * @param int $id
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function updateRole($id, $data)
    {
        try {
            // Find the role by its ID
            $role = Role::findOrFail($id);

            // Update the role with the provided data
            $role->update([
                'role_name' => $data['role_name'] ?? $role->role_name,
                'description' => $data['description'] ?? $role->description,
            ]);
        } catch (ModelNotFoundException $e) {
            Log::error('Role not found: ' . $e->getMessage());
            throw new \Exception('Role not found');
        } catch (\Exception $e) {
            Log::error('Error updating role: ' . $e->getMessage());
            throw new \Exception('Error updating role: ');
        }
    }

    /**
     * Show a specific role.
     *
     * @param int $id
     * @return Role
     * @throws \Exception
     */
    public function showRole($id)
    {
        try {
            // Find the role by its ID
            return Role::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::error('Role not found: ' . $e->getMessage());
            throw new \Exception('Role not found');
        }
    }

    /**
     * Delete a role.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function destroyRole($id)
    {
        try {
            // Find the role by its ID
            $role = Role::findOrFail($id);
            $role->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            Log::error('Role not found: ' . $e->getMessage());
            throw new \Exception('Role not found: ');
        } catch (\Exception $e) {
            Log::error('Error deleting role: ' . $e->getMessage());
            throw new \Exception('Error deleting role: ' . $e->getMessage());
        }
    }
}
