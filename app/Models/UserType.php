<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class UserType extends Model
{

    protected $fillable = ['type', 'status'];
    use HasFactory;

    public function newUniqueId()
    {
        return (string) Uuid::uuid4();
    }
}
