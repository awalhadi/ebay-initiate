<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials._head')
</head>
<body>
<div id="wrapper">
    @include('layouts.partials._header')
    @include('layouts.partials._sidebar')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid" id="app">
                @include('flash::message')
                @yield('content')
            </div>
        </div>
    </div>
    @include('layouts.partials._footer')
    @include('layouts.partials._footer-script')
</div>
</body>
</html>
