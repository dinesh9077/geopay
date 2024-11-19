<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
	protected $fillable = 
	[
		'id',
		'admin_id',
		'name',
		'status',
		'created_at',
		'updated_at'
	];  
    /**
     * Relationship with RoleGroup
     */
    public function roleGroups()
    {
        return $this->hasMany(RoleGroup::class, 'role_id');
    }
}
