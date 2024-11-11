<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuOption;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderByRaw("FIELD(status, 'UNPAID', 'PROCESSING', 'COMPLETED', 'CANCELED')")->with('items')->get();
        foreach ($orders as $order) {
            $order->items->map(function ($item) {
                $remarkArr = json_decode($item->remark);
                if (isset($remarkArr)) {
                    $item->set = implode(', ', $remarkArr);
                } else {
                    $item->set = '無';
                }
                return $item;
            });
        }


        return view('order.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);

        return view('order.show', compact('order'));
    }

    public function create()
    {
        $menus = Menu::where('status', true)->with('options')->orderBy('id', 'asc')->get();
        $menuOptions = MenuOption::where('status', true)->get();
        $menuOptions->map(function ($menuOption) {
            $menuOption->type = json_decode($menuOption->type);
            return $menuOption;
        });
        return view('order.create', compact('menus', 'menuOptions'));
    }

    public function store(Request $request, $id = null)
    {
        try {
            DB::beginTransaction();
            if ($id) {
                $order = Order::where('id', $id)->update(['status' => $request->status]);
            } else {
                $order = Order::create([
                    'order_no' => 'ORD-' . date('YmdHis'),
                    'price' => 0,
                    'status' => 'UNPAID',
                ]);

                $totalPrice = 0;
                foreach ($request->input('carts') as $item) {
                    $remark = [];
                    $totalPrice += $item['price'];

                    if (isset($item['spicyOptions'])) {
                        $remark = array_merge(array_column($item['options'], 'name'), [$item['spicyOptions']['name']]);
                    }

                    if (isset($item['drinkOptions'])) {
                        $remark = array_merge(array_column($item['options'], 'name'), [$item['drinkOptions']['name']]);
                    }

                    $order->items()->create([
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => 1,
                        'total_price' => $item['price'],
                        'remark' => json_encode($remark, JSON_UNESCAPED_UNICODE) ?? '{}',
                    ]);
                }

                $order->update(['price' => $totalPrice]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

        DB::commit();
        $orders = Order::orderByRaw("FIELD(status, 'UNPAID', 'PROCESSING', 'COMPLETED', 'CANCELED')")->with('items')->get();

        return response()->json(['status' => 'success', 'data' => $orders]);
    }
}
