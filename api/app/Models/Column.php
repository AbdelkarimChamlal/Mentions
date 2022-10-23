<?php

namespace App\Models;

use App\Models\Mention;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Column extends Model
{
    use HasFactory;



    public function mentions()
    {
        return $this->hasMany(Mention::class);
    }
}
