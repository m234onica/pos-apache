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
                    <input type="hidden" id="type">
                    <input type="hidden" id="price">
                    <div id="riceOptions" class=" form-group">
                        <label class="label" for="riceOptions">配飯：</label>
                        <div class="checkbox-grid" id="rice-options-container">
                            <!-- 選項會動態填充 -->
                        </div>
                    </div>
                    <div id="riceAdvancedOptions" class="form-group">
                        <label class="label" for="riceAdvancedOptions">配飯備注：</label>
                        <div class="checkbox-grid" id="rice-advanced-options-container">
                            <!-- 選項會動態填充 -->
                        </div>
                    </div>
                    <div id="menuOptions" class="form-group">
                        <label class="label" for="menuOptions">配菜：</label>
                        <div class="checkbox-grid" id="options-container">
                            <!-- 選項會動態填充 -->
                        </div>
                    </div>
                    <div id="advancedOptions" class="form-group">
                        <label class="label" for="advancedOptions">配菜備注：</label>
                        <div class="checkbox-grid" id="advanced-options-container">
                            <!-- 選項會動態填充 -->
                        </div>
                    </div>
                    <div id="spicyOptions" class=" form-group">
                        <label class="label" for="spicyOptions">辣度：</label>
                        <div class="checkbox-grid" id="spicy-options-container">
                            <!-- 選項會動態填充 -->
                        </div>
                    </div>
                    <div id="drinkOptions" class=" form-group">
                        <label class="label" for="drinkOptions">尺寸：</label>
                        <div class="checkbox-grid" id="drink-options-container">
                            <!-- 選項會動態填充 -->
                        </div>
                    </div>
                    <!-- 數量：數字選擇器 -->
                    <div id="quantity" class=" form-group">
                        <label class="label" for="quantity">數量：</label>
                        <div class="quantity-container">
                            <button type="button" class="quantity-btn" onclick="decrement()">−</button>
                            <input type="number" id="quantityInput" name="quantity" value="1" min="1" readonly>
                            <button type="button" class="quantity-btn" onclick="increment()">+</button>
                        </div>
                    </div>
                    <div class="form-group" style="text-align: right;">
                        <span id="totalPrice" style="font-weight: bold;color:green;margin-right:24px;font-size:40px;">$0</span>
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
                    <span>總共：<span id="totalAmount" style="color: green;font-weight: 600;">$0</span></span>
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
        let cartKeyCounter = 0; // 用於生成唯一 key 的計數器

        // 初始化事件監聽器
        initModalEvents();
        initMenuOptionsEvents();

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
                        Swal.fire({
                            title: '儲存成功!',
                            confirmButtonText:'<p style="font-size: 28px;">確認</p>',
                        });
                        $('#editMenuPage').modal('hide');
                        location.reload();
                    } else if (response.status === 422) {
                        Swal.fire({
                            title: '請填寫所有欄位!',
                            confirmButtonText:'<p style="font-size: 28px;">確認</p>',
                        });
                    } else {
                        throw new Error('儲存失敗');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        text: error.message || '儲存失敗，請重試!',
                        confirmButtonText:'<p style="font-size: 28px;">確認</p>',
                        icon: 'error',
                    });
                });
        }

        // 當 cartModal 顯示時更新購物車內容
        $('#cartModal').on('show.bs.modal', function() {
            $(this).removeAttr('aria-hidden');
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
                    type: button.getAttribute('data-menu-type'),
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
                        type: this.getAttribute("data-menu-type"),
                        quantity: this.getAttribute("data-quantity"),
                        drinkOptions: JSON.parse(this.getAttribute("data-menu-drink-options") || '[]'),
                        riceOptions: JSON.parse(this.getAttribute("data-menu-rice-options") || '[]'),
                        riceAdvancedOptions: JSON.parse(this.getAttribute("data-menu-rice-advanced-options") || '[]'),
                        advancedOptions: JSON.parse(this.getAttribute("data-menu-advanced-options") || '[]'),
                        options: JSON.parse(this.getAttribute("data-menu-default-options") || '[]'),
                        allOptions: JSON.parse(this.getAttribute("data-menu-all-options") || '[]'),
                        spicyOptions: JSON.parse(this.getAttribute("data-menu-spicy-options") || '[]')
                    };
                    populateOrderForm(menuData);
                    updateTotalPrice();
                });
            });
        }

        function populateOrderForm({
            id,
            name,
            price,
            type,
            quantity,
            options,
            allOptions,
            spicyOptions,
            drinkOptions,
            riceOptions,
            riceAdvancedOptions,
            advancedOptions
        }) {
            document.getElementById('menu-id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('price').value = price;
            document.getElementById('type').value = type;
            document.getElementById('quantityInput').value = quantity;

            // 將數據設置到對應的 DOM 元素上，這樣在 addCart 中可以正確讀取
            document.getElementById("options-container").setAttribute("data-menu-all-options", JSON.stringify(allOptions));
            document.getElementById("drink-options-container").setAttribute("data-menu-drink-options", JSON.stringify(drinkOptions));
            document.getElementById("spicy-options-container").setAttribute("data-menu-spicy-options", JSON.stringify(spicyOptions));
            document.getElementById("rice-options-container").setAttribute("data-menu-rice-options", JSON.stringify(riceOptions));
            document.getElementById("rice-advanced-options-container").setAttribute("data-menu-rice-advanced-options", JSON.stringify(riceAdvancedOptions));
            document.getElementById("advanced-options-container").setAttribute("data-menu-advanced-options", JSON.stringify(advancedOptions));

            // 將 options 轉換為一個 Set，方便查找
            const selectedOptions = new Set(options.map(opt => opt.id));

            if (type === 'DRINK') {
                document.getElementById("menuOptions").style.display = "none";
                document.getElementById("spicyOptions").style.display = "none";
                document.getElementById("drinkOptions").style.display = "block";
                document.getElementById("riceOptions").style.display = "none";
                document.getElementById("riceAdvancedOptions").style.display = "none";
                document.getElementById("advancedOptions").style.display = "none";

                // 填充飲料選項
                const drinkOptionArray = Array.isArray(drinkOptions) ? drinkOptions : Object.values(drinkOptions);
                const drinkOptionsContainer = document.getElementById("drink-options-container");
                populateOptions(drinkOptionArray, selectedOptions, drinkOptionsContainer, true);
                return;

            } else {
                document.getElementById("menuOptions").style.display = "block";
                document.getElementById("spicyOptions").style.display = "block";
                document.getElementById("drinkOptions").style.display = "none";
                document.getElementById("riceOptions").style.display = "block";
                document.getElementById("riceAdvancedOptions").style.display = "block";
                document.getElementById("advancedOptions").style.display = "block";
            }

            // 確保 allOptions 和 spicyOptions 是數組
            const optionsArray = Array.isArray(allOptions) ? allOptions : Object.values(allOptions);
            const advancedOptionsArray = Array.isArray(advancedOptions) ? advancedOptions : Object.values(advancedOptions);
            const riceOptionsArray = Array.isArray(riceOptions) ? riceOptions : Object.values(riceOptions);
            const riceAdvancedOptionsArray = Array.isArray(riceAdvancedOptions) ? riceAdvancedOptions : Object.values(riceAdvancedOptions);
            const spicyOptionsArray = Array.isArray(spicyOptions) ? spicyOptions : Object.values(spicyOptions);

            // 填充配飯選項
            const riceOptionsContainer = document.getElementById("rice-options-container");
            populateOptions(riceOptionsArray, selectedOptions, riceOptionsContainer, true);

            // 填充配飯備注選項
            const riceAdvancedOptionsContainer = document.getElementById("rice-advanced-options-container");
            populateOptions(riceAdvancedOptionsArray, selectedOptions, riceAdvancedOptionsContainer, true);

            // 填充配菜選項
            const optionsContainer = document.getElementById("options-container");
            populateOptions(optionsArray, selectedOptions, optionsContainer);

            // 填充配菜備注選項
            const advancedOptionsContainer = document.getElementById("advanced-options-container");
            populateOptions(advancedOptionsArray, selectedOptions, advancedOptionsContainer);

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
                checkbox.setAttribute("data-price", option.price); // 將價格設置到數據屬性

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
                        // 更新總價，移到這裡確保每次都會更新
                        updateTotalPrice();
                    });
                } else {
                    // 多選時的更新
                    checkbox.addEventListener("change", () => updateTotalPrice());
                }

                const label = document.createElement("label");
                label.htmlFor = `option-${option.id}`;
                label.innerText = option.name + ` (+$${option.price})`;

                checkboxItem.appendChild(checkbox);
                checkboxItem.appendChild(label);
                container.appendChild(checkboxItem);
            });
        }

        // 計算並更新總價
        function updateTotalPrice() {
            const itemPrice = parseFloat(document.getElementById('price').value) || 0;
            const quantity = parseInt(document.getElementById('quantityInput').value) || 1;
            let optionsNodeList = [];

            // 判斷 #options-container 是否顯示，否則使用 #drink-options-container
            if (menuOptionsModal.querySelector('#options-container').offsetParent !== null) {
                // 使用 Array.from 將 NodeList 轉換為 Array，再合併其他選項
                optionsNodeList = Array.from(menuOptionsModal.querySelectorAll('#options-container input[type="checkbox"]:checked'))
                    .concat(Array.from(menuOptionsModal.querySelectorAll('#rice-options-container input[type="checkbox"]:checked')))
                    .concat(Array.from(menuOptionsModal.querySelectorAll('#advanced-options-container input[type="checkbox"]:checked')));

            } else if (menuOptionsModal.querySelector('#drink-options-container').offsetParent !== null) {
                optionsNodeList = menuOptionsModal.querySelectorAll('#drink-options-container input[type="checkbox"]:checked');
            }

            // 確保 optionsNodeList 為陣列，避免單一元素時無法進行 reduce
            const options = Array.from(optionsNodeList);

            const optionsTotal = options.reduce((total, option) => {
                const optionPrice = parseFloat(option.getAttribute('data-price')) || 0;
                return total + optionPrice;
            }, 0);

            const total = (itemPrice + optionsTotal) * quantity;
            document.getElementById('totalPrice').innerText = `$${total.toFixed(0)}`;
        }

        function increment(container) {
            const input = document.getElementById('quantityInput');
            input.value = parseInt(input.value) + 1;
            updateTotalPrice();
        }

        function decrement(container) {
            const input = document.getElementById('quantityInput');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
            updateTotalPrice();
        }

        // 加入購物車
        function addCart() {
            const name = document.getElementById('name').value;
            const menuId = document.getElementById('menu-id').value;
            const price = parseFloat(document.getElementById('price').value);
            const type = document.getElementById('type').value;
            const quantity = parseInt(document.getElementById('quantityInput').value);

            // 將選項陣列轉換為以 id 為鍵的對象
            function mapOptionsById(optionsArray) {
                return optionsArray.reduce((acc, option) => {
                    acc[option.id] = option;
                    return acc;
                }, {});
            }

            // 獲取選中的選項，返回包含選項 id, name 和 price 的對象
            function getSelectedOptions(containerId, optionsMapObject) {
                return Array.from(document.querySelectorAll(`#${containerId} input[type="checkbox"]:checked`))
                    .map(option => {
                        const optionId = option.value;
                        const optionData = optionsMapObject[optionId] || {
                            name: "未知",
                            price: 0
                        };
                        return {
                            id: optionId,
                            type: optionData.type,
                            name: optionData.name,
                            price: optionData.price
                        };
                    });
            }

            // 解析 JSON 並轉換為對象
            // 配菜
            const optionMapArray = Array.isArray(JSON.parse(document.getElementById("options-container").getAttribute("data-menu-all-options") || '[]')) ?
                JSON.parse(document.getElementById("options-container").getAttribute("data-menu-all-options") || '[]') :
                Object.values(JSON.parse(document.getElementById("options-container").getAttribute("data-menu-all-options") || '[]'));

            // 配飯
            const riceOptionMapArray = Array.isArray(JSON.parse(document.getElementById("rice-options-container").getAttribute("data-menu-rice-options") || '[]')) ?
                JSON.parse(document.getElementById("rice-options-container").getAttribute("data-menu-rice-options") || '[]') :
                Object.values(JSON.parse(document.getElementById("rice-options-container").getAttribute("data-menu-rice-options") || '[]'));

            // 配飯備注
            const riceAdvancedOptionMapArray = Array.isArray(JSON.parse(document.getElementById("rice-advanced-options-container").getAttribute("data-menu-rice-advanced-options") || '[]')) ?
                JSON.parse(document.getElementById("rice-advanced-options-container").getAttribute("data-menu-rice-advanced-options") || '[]') :
                Object.values(JSON.parse(document.getElementById("rice-advanced-options-container").getAttribute("data-menu-rice-advanced-options") || '[]'));

            // 配菜備注
            const advancedOptionMapArray = Array.isArray(JSON.parse(document.getElementById("advanced-options-container").getAttribute("data-menu-advanced-options") || '[]')) ?
                JSON.parse(document.getElementById("advanced-options-container").getAttribute("data-menu-advanced-options") || '[]') :
                Object.values(JSON.parse(document.getElementById("advanced-options-container").getAttribute("data-menu-advanced-options") || '[]'));

            const drinkOptionMapArray = Array.isArray(JSON.parse(document.getElementById("drink-options-container").getAttribute("data-menu-drink-options") || '[]')) ?
                JSON.parse(document.getElementById("drink-options-container").getAttribute("data-menu-drink-options") || '[]') :
                Object.values(JSON.parse(document.getElementById("drink-options-container").getAttribute("data-menu-drink-options") || '[]'));

            const spicyOptionMapArray = Array.isArray(JSON.parse(document.getElementById("spicy-options-container").getAttribute("data-menu-spicy-options") || '[]')) ?
                JSON.parse(document.getElementById("spicy-options-container").getAttribute("data-menu-spicy-options") || '[]') :
                Object.values(JSON.parse(document.getElementById("spicy-options-container").getAttribute("data-menu-spicy-options") || '[]'));

            // 生成以 id 為鍵的對象
            const optionMapObject = mapOptionsById(optionMapArray);
            const riceOptionMapObject = mapOptionsById(riceOptionMapArray);
            const riceAdvancedOptionMapObject = mapOptionsById(riceAdvancedOptionMapArray);
            const advancedOptionMapObject = mapOptionsById(advancedOptionMapArray);
            const drinkOptionMapObject = mapOptionsById(drinkOptionMapArray);
            const spicyOptionMapObject = mapOptionsById(spicyOptionMapArray);

            let selectedOptions = [];
            let selectedAdvancedOption = [];
            let selectedRiceOption = null;
            let selectedRiceAdvancedOption = null;
            let selectedSpicyOption = null;
            let selectedDrinkOption = null;

            if (type !== 'DRINK') {
                // 獲取配飯選項
                const selectedRiceOptionElement = document.querySelector('#rice-options-container input[type="checkbox"]:checked');
                selectedRiceOption = selectedRiceOptionElement ? {
                    id: selectedRiceOptionElement.value,
                    name: riceOptionMapObject[selectedRiceOptionElement.value]?.name || "未知",
                    price: riceOptionMapObject[selectedRiceOptionElement.value]?.price || 0
                } : null;

                // 獲取配飯備注選項
                const selectedRiceAdvancedOptionElement = document.querySelector('#rice-advanced-options-container input[type="checkbox"]:checked');
                selectedRiceAdvancedOption = selectedRiceAdvancedOptionElement ? {
                    id: selectedRiceAdvancedOptionElement.value,
                    name: riceAdvancedOptionMapObject[selectedRiceAdvancedOptionElement.value]?.name || "未知",
                    price: riceAdvancedOptionMapObject[selectedRiceAdvancedOptionElement.value]?.price || 0
                } : null;

                // 獲取配菜選項
                selectedOptions = getSelectedOptions('options-container', optionMapObject);

                // 獲取配菜備注選項
                selectedAdvancedOption = getSelectedOptions('advanced-options-container', advancedOptionMapObject);

                // 獲取辣度選項
                const selectedSpicyOptionElement = document.querySelector('#spicy-options-container input[type="checkbox"]:checked');
                selectedSpicyOption = selectedSpicyOptionElement ? {
                    id: selectedSpicyOptionElement.value,
                    name: spicyOptionMapObject[selectedSpicyOptionElement.value]?.name || "未知",
                    price: spicyOptionMapObject[selectedSpicyOptionElement.value]?.price || 0
                } : null;
            } else {
                // 獲取飲品尺寸選項
                const selectedDrinkOptionElement = document.querySelector('#drink-options-container input[type="checkbox"]:checked');
                selectedDrinkOption = selectedDrinkOptionElement ? {
                    id: selectedDrinkOptionElement.value,
                    name: drinkOptionMapObject[selectedDrinkOptionElement.value]?.name || "未知",
                    price: drinkOptionMapObject[selectedDrinkOptionElement.value]?.price || 0
                } : null;
            }

            // 構建要加入購物車的項目
            const cartItem = {
                key: cartKeyCounter++, // 用於唯一識別購物車項目
                menuId,
                name,
                price,
                type,
                quantity,
                options: selectedOptions,
                riceOptions: selectedRiceOption,
                riceAdvancedOptions: selectedRiceAdvancedOption,
                advancedOptions: selectedAdvancedOption,
                spicyOptions: selectedSpicyOption,
                drinkOptions: selectedDrinkOption
            };

            // 將項目添加到購物車陣列中
            carts.push(cartItem);

            // 顯示通知或更新購物車視圖
            Swal.fire({
                title: '已加入購物車！',
                confirmButtonText:'<p style="font-size: 28px;">確認</p>',
            });
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
                const itemDiv = document.createElement('li');
                itemDiv.classList.add('cart-item');
                itemDiv.style = "margin-bottom: 36px;";

                // 計算主商品及其選項的總價格
                let totalPrice = cartItem.price + (cartItem.options || []).reduce((acc, option) => acc + option.price, 0);
                totalPrice += (cartItem.spicyOptions?.price || 0) + (cartItem.drinkOptions?.price || 0);
                totalPrice += (cartItem.riceOptions?.price || 0) + (cartItem.riceAdvancedOptions?.price || 0);
                totalPrice += (cartItem.advancedOptions || []).reduce((acc, option) => acc + option.price, 0);

                // 將計算後的價格存儲到 cartItem 的 computedPrice 屬性（不覆蓋原始 price）
                cartItem.totalPrice = totalPrice;

                // 顯示主商品的名稱和總價格
                itemDiv.innerHTML = `<span style="margin-right: 24px;font-weight: 700;">${cartItem.name}</span>
                    <span style="margin-right: 24px;font-weight: 700;">$${totalPrice} x ${cartItem.quantity || 1}</span>`;

                // 刪除按鈕
                const deleteButton = document.createElement('span');
                deleteButton.className = "material-symbols-outlined icon";
                deleteButton.style = "color:red; font-size:36px; cursor:pointer;";
                deleteButton.innerText = 'delete';
                deleteButton.addEventListener('click', () => removeCartItem(cartItem.key));
                itemDiv.appendChild(deleteButton);

                modalBody.appendChild(itemDiv);

                // 顯示選項內容和價格
                const optionsList = document.createElement('div');
                optionsList.classList.add('options-list');

                // 顯示各個選項的名稱和價格
                (cartItem.options || []).forEach((option, index) => {
                    const optionItem = document.createElement('span');
                    optionItem.style = "margin-right: 12px;";
                    optionItem.innerText = `${option.name} (+$${option.price}) `;
                    optionsList.appendChild(optionItem);
                });
                itemDiv.appendChild(optionsList);

                // 顯示配菜備注選項
                if (cartItem.advancedOptions) {
                    const optionsList = document.createElement('div');
                    optionsList.classList.add('options-list');

                    // 顯示各個選項的名稱和價格
                    (cartItem.advancedOptions || []).forEach((option, index) => {
                        const advanceOptionItem = document.createElement('span');
                        advanceOptionItem.style = "margin-right: 12px;";
                        advanceOptionItem.innerText = `${option.name} (+$${option.price}) `;
                        optionsList.appendChild(advanceOptionItem);
                    });

                    itemDiv.appendChild(optionsList);
                }

                // 顯示辣度選項
                if (cartItem.spicyOptions) {
                    const spicyOptionItem = document.createElement('p');
                    spicyOptionItem.classList.add('options-list');
                    spicyOptionItem.innerText = `${cartItem.spicyOptions.name} (+$${cartItem.spicyOptions.price})`;
                    itemDiv.appendChild(spicyOptionItem);
                }

                // 顯示配飯選項
                if (cartItem.riceOptions) {
                    const riceOptionItem = document.createElement('p');
                    riceOptionItem.classList.add('options-list');
                    riceOptionItem.innerText = `${cartItem.riceOptions.name} (+$${cartItem.riceOptions.price})`;
                    itemDiv.appendChild(riceOptionItem);
                }

                // 顯示配飯備注選項
                if (cartItem.riceAdvancedOptions) {
                    const riceAdvancedOptionItem = document.createElement('p');
                    riceAdvancedOptionItem.classList.add('options-list');
                    riceAdvancedOptionItem.innerText = `${cartItem.riceAdvancedOptions.name} (+$${cartItem.riceAdvancedOptions.price})`;
                    itemDiv.appendChild(riceAdvancedOptionItem);
                }

                // 顯示尺寸選項
                if (cartItem.drinkOptions) {
                    const drinkOptionItem = document.createElement('p');
                    drinkOptionItem.classList.add('options-list');
                    drinkOptionItem.innerText = `${cartItem.drinkOptions.name} (+$${cartItem.drinkOptions.price})`;
                    itemDiv.appendChild(drinkOptionItem);
                }

                // 計算總金額
                total += totalPrice * (cartItem.quantity || 1);
            });

            // 更新總金額顯示
            totalAmountElement.textContent = `$${total.toFixed(0)}`;

        }

        // 刪除購物車項目
        function removeCartItem(key) {
            carts = carts.filter(item => item.key !== key);
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
                        Swal.fire({
                            title: '訂單成立!',
                            confirmButtonText:'<p style="font-size: 28px;">確認</p>',
                        }).then(() => {
                            $('#cartModal').modal('hide');
                            window.location.href = '/orders';
                        });
                    } else if (response.status === 422) {
                        Swal.fire({
                            title: '請確認購物車是否有誤!',
                            confirmButtonText:'<p style="font-size: 28px;">確認</p>',
                        });
                    } else {
                        throw new Error('訂單成立失敗');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        confirmButtonText:'<p style="font-size: 28px;">確認</p>',
                        title: error.message || '訂單成立失敗，請重試!',
                    });
                });
        }

        // 外部調用的函數，掛載到 window 物件上
        window.submitMenuForm = submitMenuForm;
        window.increment = increment;
        window.decrement = decrement;
        window.createOrder = createOrder;
        window.addCart = addCart;

    });
</script>
