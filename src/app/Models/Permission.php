<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Shanmuga\LaravelEntrust\Models\EntrustPermission;

class Permission extends EntrustPermission
{
    use HasFactory;

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];
}
