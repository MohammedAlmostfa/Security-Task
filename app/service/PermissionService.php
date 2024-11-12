<?php

namespace App\Service;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermissionService
{
    /**
     * Get all permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getAllPermissions()
    {
        try {
            // Retrieve all permissions from the database
            $data =Permission::all()->select(['permission_name', 'description']);
            return $data ;
        } catch (\Exception $e) {
            Log::error('Failed to retrieve permissions: ' . $e->getMessage());
            throw new \Exception('An error occurred on the server.');
        }
    }

    /**
     * Create a new permission.
     *
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function createPermission($data)
    {
        try {

            // Create a new permission using the provided data
            Permission::create([
                'permission_name' => $data['permission_name'],
                'description' => $data['description']?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error creating permission: ' . $e->getMessage());
            throw new \Exception('Error creating permission: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing permission.
     *
     * @param int $id
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function updatePermission($id, $data)
    {
        try {
            // Find the permission by its ID
            $permission = Permission::findOrFail($id);
            // Update the permission with the provided data
            $permission->update([
                'permission_name' => $data['permission_name'] ?? $permission->permission_name,
                'description' => $data['description'] ?? $permission->description,
            ]);
        } catch (ModelNotFoundException $e) {
            Log::error('Permission not found: ' . $e->getMessage());
            throw new \Exception('Permission not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error updating permission: ' . $e->getMessage());
            throw new \Exception('Error updating permission: ' . $e->getMessage());
        }
    }

    /**
     * Show a specific permission.
     *
     * @param int $id
     * @return Permission
     * @throws \Exception
     */
    public function showPermission($id)
    {
        try {
            // Find the permission by its ID
            return Permission::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::error('Permission not found: ' . $e->getMessage());
            throw new \Exception('permission not found');
        }
    }

    /**
     * Delete a permission.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function destroyPermission($id)
    {
        try {
            // Find the permission by its ID
            $permission = Permission::findOrFail($id);
            $permission->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            Log::error('Permission not found: ' . $e->getMessage());
            throw new \Exception('Permission not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting permission: ' . $e->getMessage());
            throw new \Exception('Error deleting permission: ' . $e->getMessage());
        }
    }

    public function addPermissionToRole($permissionId, $roleId)
    {
        try {
            // Find the permission by its ID or fail
            $permission = Permission::findOrFail($permissionId);

            // Find the role by its ID or fail
            $role = Role::findOrFail($roleId);

            // Attach the permission to the role
            $role->permissions()->attach($permissionId);

            // Return a success response or any other necessary action
            return true;

        } catch (ModelNotFoundException $e) {
            // Log the error and throw an exception if the permission or role is not found
            Log::error('Model not found: ' . $e->getMessage());
            throw new \Exception('Model not found: ');

        } catch (\Exception $e) {
            // Log any other errors and throw an exception
            Log::error('Error adding permission to role: ' . $e->getMessage());
            throw new \Exception('Error adding permission to role:');
        }
    }


}
