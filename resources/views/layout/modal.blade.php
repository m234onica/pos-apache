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
                        <button type="button" class="save-button" onclick="submitForm()">儲存</button>
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
                <div class="form-group" style="font-size:30px;">
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

    $('#cartModal').on('show.bs.modal', function(event) {
        document.getElementById('cartModal').setAttribute('data-carts', JSON.stringify(carts));

        var button = $(event.relatedTarget); // 引發 modal 的按鈕
        var carts = button.data('carts');

        var modalBody = $('#cart-modal-body');
        var total = 0;

        modalBody.empty(); // 清空 modal 中的內容

        // 檢查如果是字串，將其轉換成 JSON 格式
        if (typeof carts === 'string') {
            try {
                carts = JSON.parse(carts);
            } catch (error) {
                carts = []; // 如果解析失敗，設置為空陣列以避免錯誤
            }
        }

        // 確認 carts 是陣列
        if (Array.isArray(carts)) {
            var modalBody = $('#cart-modal-body');
            var total = 0;
            modalBody.empty(); // 清空 modal 中的內容

            carts.forEach(function(cart) {
                var item = $('<div class="cart-item"></div>');
                var name = $('<h4>' + cart.name + '</h4>');
                var price = $('<span>$' + cart.price + '</span>');

                item.append(name);
                item.append(price);
                modalBody.append(item);

                total += cart.price;
            });
        }


        var totalDiv = $('<div class="total"></div>');
        var totalLabel = $('<span class="total-label">總計:</span>');
        var totalPrice = $('<span style="color:red">$' + total + '</span>');

        totalDiv.append(totalLabel);
        totalDiv.append(totalPrice);
        modalBody.append(totalDiv);
    });
    $('#cartModal').on('hidden.bs.modal', function() {
        // 檢查購物車狀態是否正常
    });

    function submitForm() {
        var id = $('#menu-id').val();
        if (!id) {
            $.ajax({
                url: '/menu',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: $('#meal-name').val(),
                    price: $('#price').val(),
                    status: $('#status').prop('checked') ? 1 : 0
                },
                success: function(response) {
                    alert('儲存成功!');
                    $('#editPage').modal('hide');
                    location.reload(); // 重新加載頁面以顯示更新後的資料
                },
                error: function(xhr) {
                    console.log(xhr.status);

                    if (xhr.status === 422) {
                        alert('請填寫所有欄位!');
                    } else {
                        alert('儲存失敗，請重試!');
                    }
                }
            });
        } else {
            $.ajax({
                url: '/menu/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: $('#meal-name').val(),
                    price: $('#price').val(),
                    status: $('#status').prop('checked') ? 1 : 0
                },
                success: function(response) {
                    alert('儲存成功!');
                    $('#editPage').modal('hide');
                    location.reload(); // 重新加載頁面以顯示更新後的資料
                },
                error: function(xhr) {

                    if (xhr.status === 422) {
                        alert('請填寫並確認所有欄位是否有誤!');
                    } else {
                        alert('儲存失敗，請重試!');
                    }
                }
            });
        }
    }

    function createOrder() {
        $.ajax({
            url: '/order',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                carts: carts
            },
            success: function(response) {
                alert('訂單成立!');
                $('#cartModal').modal('hide');
                window.location.href = '/orders'; // 重新加載頁面以顯示更新後的資料
            },
            error: function(xhr) {

                if (xhr.status === 422) {
                    alert('請確認購物車是否有誤!');
                } else {
                    alert('訂單成立失敗，請重試!');
                }
            }
        });
    }
</script>
