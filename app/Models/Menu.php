<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $fillable = ['name', 'price', 'status'];

    public function options()
    {
        return $this->belongsToMany(MenuOption::class, 'menu_option_refs', 'menu_id', 'menu_option_id');
    }

}
