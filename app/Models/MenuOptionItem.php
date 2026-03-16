<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuOptionItem extends Model
{
    protected $guarded = ['id'];

    public function menuOption()
    {
        return $this->belongsTo(MenuOption::class);
    }
}
