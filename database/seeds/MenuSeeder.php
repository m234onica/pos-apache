<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createMenu();
        $this->createMenuOptions();

        // 建立關聯
        DB::table('menu_option_refs')->truncate();

        $menuOptions = DB::table('menu_options')->get();
        $menus = DB::table('menu')->get();

        foreach ($menus as $menu) {
            if ($menu->type == 'DRINK') {
                DB::table('menu_option_refs')->insert([
                    'menu_id' => $menu->id,
                    'menu_option_id' => $menuOptions->where('name', '小杯')->first()->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                $menuOptions->map(function ($menuOption) use ($menu) {
                    $menuOptionType = $menuOption->type;
                    // 避免混鬆選項
                    if ($menuOption->name == '混鬆') {
                        return; // 直接跳過，不與任何組合
                    }

                    if ($menu->type == 'BASIC' && $menuOption->type == 'BASIC') {
                        // 對於素食菜單，只允許與素鬆搭配
                        if ($menu->name == '素食' && $menuOption->name == '素鬆') {
                            DB::table('menu_option_refs')->insert([
                                'menu_id' => $menu->id,
                                'menu_option_id' => $menuOption->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]);
                        }

                        // 對於非素食菜單，只允許與肉鬆搭配
                        if ($menu->name != '素食' && $menuOption->name == '肉鬆') {
                            DB::table('menu_option_refs')->insert([
                                'menu_id' => $menu->id,
                                'menu_option_id' => $menuOption->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]);
                        }

                        // 其他 BASIC 選項（不包括素鬆或肉鬆）需要繼續插入
                        if ($menuOption->name != '素鬆' && $menuOption->name != '肉鬆') {
                            DB::table('menu_option_refs')->insert([
                                'menu_id' => $menu->id,
                                'menu_option_id' => $menuOption->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ]);
                        }
                    }

                    if ($menu->type == 'CLUB' && ($menuOptionType == 'BASIC' || $menuOptionType == 'CLUB') && $menuOption->name != '素鬆') {
                        DB::table('menu_option_refs')->insert([
                            'menu_id' => $menu->id,
                            'menu_option_id' => $menuOption->id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                });
            }
        }
    }

    private function createMenu()
    {
        $now = Carbon::now();

        $menus = [
            ['name' => '素食', 'price' => 45, 'type' => 'BASIC'],
            ['name' => '傳統肉鬆', 'price' => 45, 'type' => 'BASIC'],
            ['name' => '玉米', 'price' => 55, 'type' => 'BASIC'],
            ['name' => '蔥蛋', 'price' => 55, 'type' => 'BASIC'],
            ['name' => '滷蛋', 'price' => 55, 'type' => 'BASIC'],
            ['name' => '鮪魚', 'price' => 60, 'type' => 'BASIC'],
            ['name' => '鮪魚總匯', 'price' => 75, 'type' => 'CLUB'],
            ['name' => '豆漿', 'price' => 25, 'type' => 'DRINK'],
            ['name' => '清漿', 'price' => 25, 'type' => 'DRINK'],
            ['name' => '紅茶', 'price' => 20, 'type' => 'DRINK'],
        ];

        foreach ($menus as $menu) {
            DB::table('menu')->updateOrInsert(
                [
                    'name' => $menu['name']
                ],
                [
                    'price' => $menu['price'],
                    'type' => $menu['type'],
                    'status' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    private function createMenuOptions()
    {
        $now = Carbon::now();

        $menuOptions = [
            ['name' => '紫米', 'price' => 5, 'type' => 'RICE'],
            ['name' => '混米', 'price' => 5, 'type' => 'RICE'],

            ['name' => '加飯', 'price' => 0, 'type' => 'RICE_ADVANCED'],
            ['name' => '少飯', 'price' => 0, 'type' => 'RICE_ADVANCED'],
            ['name' => '1/3 飯', 'price' => 0, 'type' => 'RICE_ADVANCED'],

            ['name' => '蘿蔔', 'price' => 0, 'type' => 'BASIC'],
            ['name' => '酸菜', 'price' => 0, 'type' => 'BASIC'],
            ['name' => '油條', 'price' => 0, 'type' => 'BASIC'],
            ['name' => '肉鬆', 'price' => 0, 'type' => 'BASIC'],
            ['name' => '素鬆', 'price' => 0, 'type' => 'BASIC'],
            ['name' => '混鬆', 'price' => 0, 'type' => 'BASIC'],

            ['name' => '蔥蛋', 'price' => 10, 'type' => 'CLUB'],
            ['name' => '滷蛋', 'price' => 10, 'type' => 'CLUB'],
            ['name' => '玉米', 'price' => 10, 'type' => 'CLUB'],

            ['name' => '起司', 'price' => 10, 'type' => 'ADD'],
            ['name' => '滷蛋', 'price' => 10, 'type' => 'ADD'],
            ['name' => '蔥蛋', 'price' => 10, 'type' => 'ADD'],
            ['name' => '油條', 'price' => 10, 'type' => 'ADD'],
            ['name' => '滷蛋不要蛋黃', 'price' => 10, 'type' => 'ADD'],
            ['name' => '鮪魚', 'price' => 15, 'type' => 'ADD'],

            ['name' => '蘿蔔加量', 'price' => 0, 'type' => 'ADVANCED'],
            ['name' => '蘿蔔減量', 'price' => 0, 'type' => 'ADVANCED'],
            ['name' => '酸菜加量', 'price' => 0, 'type' => 'ADVANCED'],
            ['name' => '酸菜減量', 'price' => 0, 'type' => 'ADVANCED'],
            ['name' => '肉鬆加量', 'price' => 0, 'type' => 'ADVANCED'],
            ['name' => '肉鬆減量', 'price' => 0, 'type' => 'ADVANCED'],
            ['name' => '油條少', 'price' => 0, 'type' => 'ADVANCED'],

            ['name' => '小辣', 'price' => 0, 'type' => 'SPICY'],
            ['name' => '中辣', 'price' => 0, 'type' => 'SPICY'],
            ['name' => '大辣', 'price' => 0, 'type' => 'SPICY'],

            ['name' => '小杯', 'price' => 0, 'type' => 'SIZE'],
            ['name' => '大杯', 'price' => 5, 'type' => 'SIZE'],
            ['name' => '冰', 'price' => 0, 'type' => 'HEAT'],
            ['name' => '熱', 'price' => 0, 'type' => 'HEAT'],
        ];

        foreach ($menuOptions as $menuOption) {
            DB::table('menu_options')->updateOrInsert(
                [
                    'name' => $menuOption['name'],
                    'type' => $menuOption['type'],
                ],
                [
                    'price' => $menuOption['price'],
                    'type' => $menuOption['type'],
                    'status' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
