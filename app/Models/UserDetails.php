<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'user_type', 'address', 'profile_picture_url', 'current_school', 'previous_school', 'parents_details', 'assigned_teacher_id', 'status'];
}
