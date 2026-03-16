<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id']; // Memperbolehkan semua kolom diisi massal kecuali id

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
