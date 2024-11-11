@extends('layout/container')

@section('content')
<div class="menu-list">
    <button
        id="addMenu"
        class="add-btn"
        data-toggle="modal"
        data-target="#editMenuPage">+ 新增菜單</button>

        <div class="menu-grid">
        @if (!$menus->count())
        <div class="empty">
            <p>目前尚未有任何菜單!</p>
        </div>
        @endif
        @foreach ($menus as $menu)
        <button class="menu-item"
            data-toggle="modal"
            data-target="#editMenuPage"
            data-id="{{ $menu->id }}"
            data-name="{{ $menu->name }}"
            data-price="{{ $menu->price }}"
            data-status="{{ $menu->status ? 1 : 0 }}">
            <div class="item-details">
                <h1>{{ $menu->name }}</h1>
                <span>${{ $menu->price }}</span>
                <span style="color: {{$menu->status ? 'green' : 'grey'}}">{{ $menu->status ? '供應中' : '已售完' }}</span>
            </div>
        </button>
        @endforeach
    </div>
</div>

{{-- 引入 modal --}}
@include('layout.modal')

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
</script>
@endsection
