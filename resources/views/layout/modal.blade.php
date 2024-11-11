<!-- menu edit modal -->
<div class="modal fade edit-page" id="editMenuPage" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">編輯餐點</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="edit-menu-form">
                    @csrf
                    <input type="hidden" id="menu-id">
                    <div class="form-group">
                        <label for="meal-name">餐點名稱：</label>
                        <input type="text" id="meal-name" name="meal_name">
                    </div>

                    <div class="form-group">
                        <label for="price">金額：</label>
                        <input type="text" id="price" name="price">
                    </div>

                    <div class="form-group">
                        <label>餐點狀態：</label>
                        <label class="switch">
                            <input type="checkbox" id="status" name="status">
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <div class="form-group" style="text-align: right;">
                        <button type="button" class="save-button" onclick="submitMenuForm()">儲存</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 點餐 -->
<div class="modal fade edit-page" id="menuOptionsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">客制選項</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="edit-menu-form">
                    @csrf
                    <input type="hidden" id="menu-id">
                    <input type="hidden" id="name">
                    <div id="menuOptions" class="form-group">
                        <label for="menuOptions">預設菜色：</label>
                        <div class="checkbox-grid" id="options-container">
                            <!-- 選項會動態填充 -->
                        </div>
                    </div>
                    <div id="spicyOptions" class=" form-group">
                        <label for="spicyOptions">辣度：</label>
                        <div class="checkbox-grid" id="spicy-options-container">
                            <!-- 選項會動態填充 -->
                        </div>
                    </div>
                    <div id="drinkOptions" class=" form-group">
                        <label for="drinkOptions">尺寸：</label>
                        <div class="checkbox-grid" id="drink-options-container">
                            <!-- 選項會動態填充 -->
                        </div>
                    </div>
                    <div class="form-group" style="text-align: right;">
                        <button type="button" class="save-button" onclick="addCart()">加入購物車</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- order edit page's cart modal -->
<div class="modal fade" id="cartModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">購物車</h4>
            </div>
            <div class="modal-body" id="cart-modal-body">
            </div>
            <hr style="margin:0px">
            <div class="total-price-and-submit">
                <div class="form-group" style="font-size:36px;">
                    <span>總共：<span id="totalAmount" style="color: green;">$0</span></span>
                </div>

                <div class="form-group">
                    <button type="button" class="submit-button" onclick="createOrder()">成立訂單</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        // 全局變量
        let carts = [];

        // 初始化事件監聽器
        initModalEvents();
        initMenuOptionsEvents();

        // 當 cartModal 顯示時更新購物車內容
        $('#cartModal').on('show.bs.modal', function() {
            updateCartModalContent();
        });

        function initModalEvents() {
            // 編輯餐點模態框顯示時，填充表單資料
            document.getElementById('editMenuPage').addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const menuData = {
                    id: button.getAttribute('data-id'),
                    name: button.getAttribute('data-name'),
                    price: button.getAttribute('data-price'),
                    status: button.getAttribute('data-status') === '1'
                };
                populateEditForm(menuData);
            });
        }

        function initMenuOptionsEvents() {
            document.querySelectorAll(".menu-item").forEach(button => {
                button.addEventListener("click", function() {
                    const menuData = {
                        id: this.id.split("-")[1],
                        name: this.getAttribute("data-name"),
                        price: this.getAttribute("data-price"),
                        type: this.getAttribute("data-type"),
                        drinkOptions: JSON.parse(this.getAttribute("data-menu-drink-options") || '[]'),
                        options: JSON.parse(this.getAttribute("data-menu-default-options") || '[]'),
                        allOptions: JSON.parse(this.getAttribute("data-menu-all-options") || '[]'),
                        spicyOptions: JSON.parse(this.getAttribute("data-menu-spicy-options") || '[]')
                    };
                    populateOrderForm(menuData);
                });
            });
        }

        function populateOrderForm({
            id,
            name,
            price,
            type,
            options,
            allOptions,
            spicyOptions,
            drinkOptions
        }) {
            document.getElementById('menu-id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('price').value = price;

            // 將 options 轉換為一個 Set，方便查找
            const selectedOptions = new Set(options.map(opt => opt.id));

            if (type === 'DRINK') {
                document.getElementById("menuOptions").style.display = "none";
                document.getElementById("spicyOptions").style.display = "none";
                document.getElementById("drinkOptions").style.display = "block";

                // 填充飲料選項
                const drinkOptionArray = Array.isArray(drinkOptions) ? drinkOptions : Object.values(drinkOptions);
                const drinkOptionsContainer = document.getElementById("drink-options-container");
                populateOptions(drinkOptionArray, selectedOptions, drinkOptionsContainer, true);
                return;

            } else {
                document.getElementById("menuOptions").style.display = "block";
                document.getElementById("spicyOptions").style.display = "block";
                document.getElementById("drinkOptions").style.display = "none";
            }

            // 確保 allOptions 和 spicyOptions 是數組
            const optionsArray = Array.isArray(allOptions) ? allOptions : Object.values(allOptions);
            const spicyOptionsArray = Array.isArray(spicyOptions) ? spicyOptions : Object.values(spicyOptions);

            // 填充配菜選項
            const optionsContainer = document.getElementById("options-container");
            populateOptions(optionsArray, selectedOptions, optionsContainer);

            // 填充辣味選項，設置為單選
            const spicyOptionsContainer = document.getElementById("spicy-options-container");
            populateOptions(spicyOptionsArray, selectedOptions, spicyOptionsContainer, true);
        }

        // 填充編輯表單
        function populateOptions(optionsArray, selectedOptions, container, isSingleSelect = false) {
            container.innerHTML = ""; // 清空選項

            optionsArray.forEach(option => {
                const checkboxItem = document.createElement("div");
                checkboxItem.classList.add("checkbox-item");

                const checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.id = `option-${option.id}`;
                checkbox.name = "options[]";
                checkbox.value = option.id;

                // 如果 option.id 存在於 selectedOptions 中，設置為已選中
                if (selectedOptions.has(option.id)) {
                    checkbox.checked = true;
                }

                // 單選行為
                if (isSingleSelect) {
                    checkbox.addEventListener("change", () => {
                        if (checkbox.checked) {
                            const checkboxes = container.querySelectorAll("input[type='checkbox']");
                            checkboxes.forEach(cb => {
                                if (cb !== checkbox) {
                                    cb.checked = false;
                                }
                            });
                        }
                    });
                }

                const label = document.createElement("label");
                label.htmlFor = `option-${option.id}`;
                label.innerText = option.name;

                checkboxItem.appendChild(checkbox);
                checkboxItem.appendChild(label);
                container.appendChild(checkboxItem);
            });
        }

        // 表單提交（創建或更新菜單）
        function submitMenuForm() {
            const id = document.getElementById('menu-id').value;
            const url = id ? `/menu/${id}` : '/menu';
            const method = id ? 'POST' : 'POST';

            fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: document.getElementById('meal-name').value,
                        price: document.getElementById('price').value,
                        status: document.getElementById('status').checked ? 1 : 0
                    })
                })
                .then(response => {
                    if (response.ok) {
                        alert('儲存成功!');
                        $('#editMenuPage').modal('hide');
                        location.reload();
                    } else if (response.status === 422) {
                        alert('請填寫所有欄位!');
                    } else {
                        throw new Error('儲存失敗');
                    }
                })
                .catch(error => {
                    alert(error.message || '儲存失敗，請重試!');
                });
        }

        // 加入購物車
        function addCart() {
            const name = document.getElementById('name').value;
            const menuId = document.getElementById('menu-id').value;
            const price = document.getElementById('price').value;

            // 獲取所有選中的菜色選項
            const selectedOptions = Array.from(document.querySelectorAll('#options-container input[type="checkbox"]:checked'))
                .map(option => option.value);

            // 獲取辣度選項（單選）
            const selectedSpicyOption = document.querySelector('#spicy-options-container input[type="checkbox"]:checked');
            const spicyOption = selectedSpicyOption ? selectedSpicyOption.value : null;

            // 獲取尺寸選項（飲品專用）
            const selectedDrinkOption = document.querySelector('#drink-options-container input[type="checkbox"]:checked');
            const drinkOption = selectedDrinkOption ? selectedDrinkOption.value : null;

            // 構建要加入購物車的項目
            const cartItem = {
                menuId: menuId,
                name: name,
                price: parseFloat(price),
                options: selectedOptions,
                spicyOptions: spicyOption,
                drinkOptions: drinkOption
            };

            // 將項目添加到購物車陣列中
            carts.push(cartItem);

            // 顯示通知或更新購物車視圖
            alert('已加入購物車！');
            $('#menuOptionsModal').modal('hide');
            $('.modal-backdrop').remove(); // 強制移除遮罩
            console.log(carts); // 供調試用，查看購物車的內容
        }

        function updateCartModalContent() {
            const modalBody = document.getElementById('cart-modal-body');
            const totalAmountElement = document.getElementById('totalAmount');

            // 清空內容
            modalBody.innerHTML = '';
            let total = 0;

            if (carts.length === 0) {
                modalBody.innerHTML = '<p>購物車是空的。</p>';
                totalAmountElement.textContent = '$0';
                return;
            }

            // 添加每個購物車項目
            carts.forEach(cartItem => {
                const itemDiv = document.createElement('div');
                itemDiv.classList.add('cart-item');
                itemDiv.innerHTML = `<span style="margin-right: 24px;">${cartItem.name}</span>
                    <span style="margin-right: 24px;">$${cartItem.price} x ${cartItem.quantity || 1}</span>`;

                // 刪除按鈕
                const deleteButton = document.createElement('span');
                deleteButton.className = "material-symbols-outlined icon";
                deleteButton.style = "color:red; font-size:36px; cursor:pointer;";
                deleteButton.innerText = 'delete';
                deleteButton.addEventListener('click', () => removeCartItem(cartItem.menuId));

                itemDiv.appendChild(deleteButton);
                modalBody.appendChild(itemDiv);

                total += cartItem.price * (cartItem.quantity || 1);
            });

            // 更新總金額顯示
            totalAmountElement.textContent = `$${total.toFixed(0)}`;
        }

        // 刪除購物車項目
        function removeCartItem(menuId) {
            carts = carts.filter(item => item.menuId !== menuId);
            updateCartModalContent();
        }

        // 創建訂單
        function createOrder() {
            fetch('/order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        carts
                    })
                })
                .then(response => {
                    if (response.ok) {
                        alert('訂單成立!');
                        $('#cartModal').modal('hide');
                        window.location.href = '/orders';
                    } else if (response.status === 422) {
                        alert('請確認購物車是否有誤!');
                    } else {
                        throw new Error('訂單成立失敗');
                    }
                })
                .catch(error => {
                    alert(error.message || '訂單成立失敗，請重試!');
                });
        }

        // 外部調用的函數，掛載到 window 物件上
        window.submitMenuForm = submitMenuForm;
        window.createOrder = createOrder;
        window.addCart = addCart;

    });
</script>
