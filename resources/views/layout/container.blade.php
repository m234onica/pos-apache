<!DOCTYPE HTML>
<Html lang="zh-Hant-TW">

<head>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    @include('layout.css')
</head>

<body>
    <div class="nev-tab">
        <!-- <button class="{{ Request::is('menus') ? 'active' : '' }}" id="menu" onclick="window.location.href='/menus'">菜單管理</button> -->
        <div id="order">訂單管理</div>
    </div>
    <div class="container-fluid">
        <div class="tab">
            @yield('content')
        </div>
    </div>
</body>

</Html>
