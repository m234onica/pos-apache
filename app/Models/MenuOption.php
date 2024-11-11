<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuOption extends Model
{
    protected $table = 'menu_options';
    protected $fillable = ['name', 'price', 'status'];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_option_refs', 'menu_option_id', 'menu_id');
    }
}
