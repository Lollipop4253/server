<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersAndRoles extends Model
{
    use HasFactory;

    public $table = 'RolesAndPermissions';

    protected $fillable = [
        'name',
        'user_id',
        'role_id',
        'created_by',
        'deleted_by'
    ];
}
