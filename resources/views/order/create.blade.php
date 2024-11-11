@extends('layout/container')

@section('content')
<div class="menu-list">
    <button
        id="goBack"
        class="back-button"
        onclick="goBack()">回上一頁
    </button>


    <div class="menu-grid">
        @if (!$menus->count())
        <div class="empty">
            <p>目前尚未有任何菜單!</p>
        </div>
        @endif

        @foreach ($menus as $menu)
        @php
        $spicyOptions = collect([]);
        $drinkOptions = collect([]);
        $canAddMenuOptions = collect([]);

        if ($menu->type === 'DRINK') {
            // 只保留類型為 DRINK 的選項
            $drinkOptions = $menuOptions->filter(function ($option) {
                return $option->type === ['DRINK'];
            });
        } elseif (in_array($menu->type, ['BASIC', 'CLUB'])) {
            // 保留非 DRINK 和非 SPICY 的選項
            $canAddMenuOptions = $menuOptions->filter(function ($option) {
                return !in_array('DRINK', $option->type) && !in_array('SPICY', $option->type);
            })->map(function ($option) use ($menu) {
                // 設定價格：BASIC 類型的選項且選項包含 BASIC，或 CLUB 類型的選項價格為 0，否則為 5
    if (($menu->type === 'BASIC' && in_array('BASIC', $option->type)) || $menu->type === 'CLUB') {
        $option->price = 0;
    } else {
        $option->price = 5;
    }
    return $option;
            });
        }

        // 只保留類型為 SPICY 的選項
        $spicyOptions = $menuOptions->filter(function ($option) {
            return in_array('SPICY', $option->type);
        });
        @endphp
        <button id="menu-{{ $menu->id }}" class="menu-item"
            data-toggle="modal"
            data-target="#menuOptionsModal"
            data-id="{{ $menu->id }}"
            data-name="{{ $menu->name }}"
            data-price="{{ $menu->price }}"
            data-menu-type="{{ $menu->type }}"
            data-menu-default-options="{{ $menu->options }}"
            data-menu-all-options="{{ $canAddMenuOptions }}"
            data-menu-drink-options="{{ $drinkOptions }}"
            data-menu-spicy-options="{{ $spicyOptions }}">
            <div class="item-details">
                <h1>{{ $menu->name }}</h1>
                <span>${{ $menu->price }}</span>
            </div>
        </button>
        @endforeach
    </div>

    <button
        id="cartButton"
        class="add-btn"
        data-toggle="modal"
        data-target="#cartModal"
        data-carts="">
        顯示清單
    </button>

    {{-- 引入 modal --}}
    @include('layout.modal')
</div>

<script type="text/javascript">
    // 獲取 cart 的初始狀態，並進行檢查
    let carts = [];

    // 確保你有這個代碼在 DOM Ready 的地方
    $(document).ready(function() {
        // 當 Modal 顯示時更新數據
        $('#cartModal').on('show.bs.modal', function() {
            // 這裡直接從 cart 數組獲取最新的購物車數據
            const cartData = JSON.stringify(carts);
            // 更新 data-carts 屬性
            $(this).data('carts', cartData); // 更新 jQuery 的內部數據緩存
            $(this).attr('data-carts', cartData); // 更新 DOM 屬性
        });
    });


    function goBack() {
        window.history.back();
    }
</script>
@endsection
