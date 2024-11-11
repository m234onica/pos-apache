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
        <button id="menu-{{ $menu->id }}" class="menu-item"
            onclick="addOrder({{ $menu->id }})">
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
            updateCartData(); // 更新 Modal 內容
        });

        $('#editMenuPage').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // 引發 modal 的按鈕
            var id = button.data('id');
            var name = button.data('name');
            var price = button.data('price');
            var status = button.data('status');

            // 填充 modal 中的表單
            $('#menu-id').val(id);
            $('#meal-name').val(name);
            $('#price').val(price);
            $('#status').prop('checked', status == 1);
        });
    });


    function goBack() {
        window.history.back();
    }

    function addOrder(menuId) {
        const menuName = document.querySelector(`#menu-${menuId} .item-details h1`).innerText;
        const menuPrice = parseFloat(document.querySelector(`#menu-${menuId} .item-details span`).innerText.replace('$', ''));

        // 檢查商品是否已在購物車中
        const existingItem = carts.find(item => item.id === menuId);

        if (existingItem) {
            // 如果已存在，增加數量
            existingItem.quantity++;
        } else {
            // 如果不存在，將新商品加入購物車
            carts.push({
                id: menuId,
                name: menuName,
                price: menuPrice,
                quantity: 1
            });
        }

        // 更新 cartModal 的 data-carts 屬性
        document.getElementById('cartButton').setAttribute('data-carts', JSON.stringify(carts));
    }

    function updateCartData() {
        const cartModalBody = document.getElementById('cart-modal-body');
        const totalAmountElement = document.getElementById('totalAmount');

        // 直接使用 cart 陣列
        const cartItems = carts;

        cartModalBody.innerHTML = ''; // 清空現有的顯示
        let totalAmount = 0; // 初始化總金額

        if (!cartItems.length) {
            cartModalBody.innerHTML = '<p>購物車是空的。</p>';
            totalAmountElement.textContent = '$0'; // 更新總金額顯示
            return;
        }


        cartItems.forEach(item => {
            const itemTag = document.createElement('p');
            itemTag.textContent = `${item.name} - $${item.price} x ${item.quantity} `;

            // 創建刪除符號
            const deleteButton = document.createElement('span');
            deleteButton.innerHTML = '<span class="material-symbols-outlined icon" style="color:red; font-size:30px">delete</span>'; // 使用刪除符號
            deleteButton.style.cursor = 'pointer'; // 鼠標樣式
            deleteButton.style.marginLeft = '10px'; // 在符號和文本之間留一些空間
            deleteButton.onclick = () => removeItem(item.id); // 綁定刪除函數

            itemTag.appendChild(deleteButton); // 將刪除符號添加到項目後面
            cartModalBody.appendChild(itemTag);

            totalAmount += item.price * item.quantity; // 累加總金額
        });

        // 更新總金額顯示
        totalAmountElement.textContent = `$${totalAmount.toFixed(0)}`;

    }

    function removeItem(menuId) {
        // 從購物車中移除項目
        carts = carts.filter(item => item.id !== menuId);

        // 更新 data-carts
        document.getElementById('cartButton').setAttribute('data-carts', JSON.stringify(carts));

        // 更新購物車顯示
        updateCartData();
    }
</script>
@endsection
