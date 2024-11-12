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
                    $item->basic = implode(', ', $remarkArr->BASIC ?? []);
                    $item->club = implode(', ', $remarkArr->CLUB ?? []);
                    $item->drink = $remarkArr->DRINK ?? '';
                    $item->spicy = $remarkArr->SPICY ?? '';
                } else {
                    $item->basic = '無';
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
        return view('order.create', compact('menus', 'menuOptions'));
    }

    public function store(Request $request, $id = null)
    {
        try {
            DB::beginTransaction();
            if ($id) {
                $order = Order::where('id', $id)->update(['status' => $request->status]);
            } else {
                $orderNo = Order::whereDate('created_at', today())->count() + 1;
                $order = Order::create([
                    // $orderNo 三碼需補零
                    'order_no' => 'ORD-' . date('Ymd') . '-' . str_pad($orderNo, 3, '0', STR_PAD_LEFT),
                    'price' => 0,
                    'status' => 'UNPAID',
                ]);

                $totalPrice = 0;
                foreach ($request->input('carts') as $item) {
                    $remark = [];
                    $totalPrice += $item['price'] * $item['quantity'];

                    foreach ($item['options'] as $option) {
                        $remark[$option['type']][] = $option['name'];
                    }
                    if (isset($item['spicyOptions'])) {
                        $remark['SPICY'] = $item['spicyOptions']['name'];
                    }

                    if (isset($item['drinkOptions'])) {
                        $remark['DRINK'] = $item['drinkOptions']['name'];
                    }

                    $order->items()->create([
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'total_price' => $item['price'] * $item['quantity'],
                        'remark' => json_encode($remark, JSON_UNESCAPED_UNICODE) ?? [],
                    ]);
                }

                $order->update(['price' => $totalPrice]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

        DB::commit();
        $orders = Order::orderByRaw("FIELD(status, 'UNPAID', 'PROCESSING', 'COMPLETED', 'CANCELED')")->with('items')->get();

        return response()->json(['status' => 'success', 'data' => $orders]);
    }
}
