<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as PermissionModel;

class Permissions extends PermissionModel
{
    use HasFactory;

    protected $table = 'permissions';

    protected $fillable = ['name', 'guard_name'];
}
