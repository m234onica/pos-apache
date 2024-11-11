<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('status', 'desc')->orderBy('id', 'asc')->get();

        return view('menu.index', compact('menus'));
    }

    public function show($id)
    {
        $menu = Menu::find($id);

        return view('menu.show', compact('menu'));
    }

    public function store(Request $request, $id = null)
    {
        Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|integer|min:1',
            'status' => 'required|boolean',
        ])->validate();

        if (!$id) {
            Menu::create($request->all());
        } else {
            Menu::find($id)->update($request->all());
        }
        return redirect()->route('menu.index');
    }
}
