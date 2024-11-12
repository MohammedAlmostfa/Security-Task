<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            "user.index",
            "user.store",
            "user.show",
            "user.update",
            "user.destroy",
  "user.tasks",
            "task.update.status",
            "task.return",
            'task.index',
            "task.store",
            "task.show",
            "task.update",
            "task.destroy",

            "comment.index",
            "comment.store",
            "comment.show",
            "comment.update",
            "comment.destroy",
"comment.return",
            "permission.index",
            "permission.store",
            "permission.show",
            "permission.update",
            "permission.destroy",

            "assignTask",
            "reassignTask",
            "connectTask",
            "downloadAttachment",
            "generateDailyReport",

            "role.index",
            "role.store",
            "role.show",
            "role.update",
            "role.destroy",
            "addPermissionToRole",


            "attachment.index",
            "attachment.store",
            "attachment.show",
            "attachment.update",
            "attachment.destroy",
        ];

        foreach ($permissions as $permission) {
            Permission::create(['permission_name' => $permission]);
        }
    }
}
