<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function getuser()
    {
        return $this->belongsTo(User::class);
    }

    protected $table = 'spaces';

    protected $fillable = [
        'space_name',
    ];
}
