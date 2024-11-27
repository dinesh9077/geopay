<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDirector extends Model
{
    use HasFactory;
	protected $fillable = [
		'company_details_id',
		'name',
		'created_at',
		'updated_at',
	];
}
