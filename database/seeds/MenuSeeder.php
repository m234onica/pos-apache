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
                    $menuOptionType = json_decode($menuOption->type);
                    if (in_array($menu->type, $menuOptionType)) {
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
            ['name' => '紫米', 'price' => 5, 'type' => ['RICE']],
            ['name' => '蘿蔔', 'price' => 0, 'type' => ['BASIC', 'CLUB']],
            ['name' => '酸菜', 'price' => 0, 'type' => ['BASIC', 'CLUB']],
            ['name' => '油條', 'price' => 0, 'type' => ['BASIC', 'CLUB']],
            ['name' => '肉鬆', 'price' => 0, 'type' => ['BASIC', 'CLUB']],
            ['name' => '蔥蛋', 'price' => 0, 'type' => ['CLUB']],
            ['name' => '滷蛋', 'price' => 0, 'type' => ['CLUB']],
            ['name' => '玉米', 'price' => 0, 'type' => ['CLUB']],
            ['name' => '鮪魚', 'price' => 0, 'type' => ['CLUB']],
            ['name' => '小辣', 'price' => 0, 'type' => ['SPICY']],
            ['name' => '中辣', 'price' => 0, 'type' => ['SPICY']],
            ['name' => '大辣', 'price' => 0, 'type' => ['SPICY']],
            ['name' => '小杯', 'price' => 0, 'type' => ['DRINK']],
            ['name' => '大杯', 'price' => 5, 'type' => ['DRINK']],
        ];

        foreach ($menuOptions as $menuOption) {
            DB::table('menu_options')->updateOrInsert(
                [
                    'name' => $menuOption['name']
                ],
                [
                    'price' => $menuOption['price'],
                    'type' => json_encode($menuOption['type']),
                    'status' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
