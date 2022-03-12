<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::beginTransaction();
        $super_admin = new Role();
        $super_admin->name         = 'super-admin';
        $super_admin->display_name = 'Super Admin'; // optional
        $super_admin->description  = 'Highest Level'; // optional
        $super_admin->save();
        
        $regular_user = new Role();
        $regular_user->name         = 'user';
        $regular_user->display_name = 'User'; // optional
        $regular_user->description  = 'User'; // optional
        $regular_user->save();
        
        /**Attach super admin for user 1 */
        $user = User::find(1);
        $user->attachRole($super_admin);

        /**Attach user for all user exclude super admin */
        $users = User::where('id', '>', 1)->get();
        foreach ($users as $user) {
            $user->attachRole($regular_user);
        }

        $superAdminPermissionArray = [];
        $regularPermissionArray = [];

        /**Attach Permission for Super Admin */
        $api = [
            'api-users' => 'User API',
        ];

        foreach ($api as $key => $value) {
            /**Create Permission to INDEX */
            $index = Permission::create(['name' => $key.'-index', 'display_name' => $value.' Index']);

            /**Create Permission to STORE */
            $store = Permission::create(['name' => $key.'-store', 'display_name' => $value.' Store']);
            
            /**Create Permission to UPDATE */
            $update = Permission::create(['name' => $key.'-update', 'display_name' => $value.' Update']);
            
            /**Create Permission to SHOW */
            $show = Permission::create(['name' => $key.'-show', 'display_name' => $value.' Show']);
            
            /**Create Permission to DESTROY */
            $destroy = Permission::create(['name' => $key.'-destroy', 'display_name' => $value.' Destroy']);

            /**Attach Permission to Super Admin */
            $superAdminPermissionArray[] = $index->id;
            $superAdminPermissionArray[] = $store->id;
            $superAdminPermissionArray[] = $update->id;
            $superAdminPermissionArray[] = $show->id;
            $superAdminPermissionArray[] = $destroy->id;
        }

        /**Attach Permission for Super Admin, View for User */
        $api = [
            'api-gifts' => 'Gifts API',
        ];
        foreach ($api as $key => $value) {
            /**Create Permission to INDEX */
            $index = Permission::create(['name' => $key.'-index', 'display_name' => $value.' Index']);

            /**Create Permission to STORE */
            $store = Permission::create(['name' => $key.'-store', 'display_name' => $value.' Store']);
            
            /**Create Permission to UPDATE */
            $update = Permission::create(['name' => $key.'-update', 'display_name' => $value.' Update']);
            
            /**Create Permission to SHOW */
            $show = Permission::create(['name' => $key.'-show', 'display_name' => $value.' Show']);
            
            /**Create Permission to DESTROY */
            $destroy = Permission::create(['name' => $key.'-destroy', 'display_name' => $value.' Destroy']);

            /**Attach Permission to Super Admin */
            $superAdminPermissionArray[] = $index->id;
            $superAdminPermissionArray[] = $store->id;
            $superAdminPermissionArray[] = $update->id;
            $superAdminPermissionArray[] = $show->id;
            $superAdminPermissionArray[] = $destroy->id;

            /**Attach Permission to User */
            $regularPermissionArray[] = $index->id;
            $regularPermissionArray[] = $show->id;
        }

        /**Attach Permission to User */
        $api = [
            'api-gifts-redeem' => 'Gifts API Redeem',
            'api-gifts-rating' => 'Gifts API Rating',
            'api-gifts-like' => 'Gifts API Like',
        ];
        foreach ($api as $key => $value) {
            $permission = Permission::create(['name' => $key, 'display_name' => $value]);
            $regularPermissionArray[] = $permission->id;
        }

        /**Attach Permissions to Super Admin */
        $super_admin->permissions()->sync($superAdminPermissionArray);
            
        /**Attach Permissions to User */
        $regular_user->permissions()->sync($regularPermissionArray);

        \DB::commit();
    }
}
