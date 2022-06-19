<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function masjids()
    {
        $this->hasMany(MasjidReview::class);
    }

    protected $fillable = ["id",'desc'];
}
