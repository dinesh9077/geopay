<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookRegister extends Model
{
    use HasFactory;
	protected $fillable = [
		'id', 'user_id', 'url', 'secret', 'status', 'created_at', 'updated_at'
	];
}
