<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class MenuOptionRef extends Model
{
    protected $table = 'menu_option_refs';
    protected $fillable = ['menu_id', 'menu_option_id'];
    public $timestamps = false;

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function menuOption()
    {
        return $this->belongsTo(MenuOption::class, 'menu_option_id');
    }
}
