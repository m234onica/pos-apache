<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuOption;
use App\Models\Order;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
                    $item->rice = $remarkArr->RICE ?? '';
                    $item->riceAdvanced = $remarkArr->RICE_ADVANCED ?? '';
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
            Validator::make($request->all(), [
                'carts' => 'required|array|min:1', // carts 必須是至少有一個項目的陣列
                'carts.*.name' => 'required|string',
                'carts.*.price' => 'required|integer|min:0',
                'carts.*.type' => 'required|string|in:BASIC,CLUB,RICE,SPICY,DRINK,ADVANCED,RICE_ADVANCED', // 假設 type 只能是 DRINK 或 FOOD
                'carts.*.quantity' => 'required|integer|min:1',
                'carts.*.options' => 'nullable|array',
                'carts.*.riceOptions' => 'nullable',
                'carts.*.riceAdvancedOptions' => 'nullable',
                'carts.*.advancedOptions' => 'nullable|array',
                'carts.*.spicyOptions' => 'nullable',
                'carts.*.drinkOptions' => 'nullable|array',
                'carts.*.totalPrice' => 'required|integer|min:1',
            ])->validate();

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
                    $totalPrice += $item['totalPrice'] * $item['quantity'];

                    foreach (['options', 'advancedOptions'] as $optionType) {
                        if (!$item[$optionType]) {
                            continue;
                        }
                        foreach ($item[$optionType] as $option) {
                            $remark[$option['type']][] = $option['name'];
                        }
                    }

                    if (isset($item['riceOptions'])) {
                        $remark['RICE'] = $item['riceOptions']['name'];
                    }

                    if (isset($item['riceAdvancedOptions'])) {
                        $remark['RICE_ADVANCED'] = $item['riceAdvancedOptions']['name'];
                    }

                    if (isset($item['spicyOptions'])) {
                        $remark['SPICY'] = $item['spicyOptions']['name'];
                    }

                    if (isset($item['drinkOptions'])) {
                        $remark['DRINK'] = $item['drinkOptions']['name'];
                    }

                    $order->items()->create([
                        'name' => $item['name'],
                        'price' => $item['totalPrice'],
                        'quantity' => $item['quantity'],
                        'total_price' => $item['totalPrice'] * $item['quantity'],
                        'remark' => json_encode($remark, JSON_UNESCAPED_UNICODE) ?? [],
                    ]);
                }

                $order->update(['price' => $totalPrice]);
            }
        } catch (ValidationException $e) {
            DB::rollBack();
            // 使用 errors() 方法來獲取所有驗證錯誤
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
                'error_detail' => $e->getMessage()
            ], 500);
        }

        DB::commit();
        $orders = Order::orderByRaw("FIELD(status, 'UNPAID', 'PROCESSING', 'COMPLETED', 'CANCELED')")->with('items')->get();

        return response()->json(['status' => 'success', 'data' => $orders]);
    }
}
