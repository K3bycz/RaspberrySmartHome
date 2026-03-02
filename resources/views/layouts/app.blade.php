<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Raspberry SmartHome
    </title>
        @yield('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"></head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <header class="col-md-12">
        <h1> Raspberry SmartHome <img src="{{ asset('images/raspberry.png') }}" style="width:22px; height:25px;" alt=""></h1>
    </header>
    <button id="menuToggle" class="btn btn-custom d-md-none">
        ☰ Menu
    </button>
    <main>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="wave">
        <path fill="#C31C4A" fill-opacity="1.5" d="M0,256L34.3,250.7C68.6,245,137,235,206,240C274.3,245,343,267,411,240C480,213,549,139,617,133.3C685.7,128,754,192,823,218.7C891.4,245,960,235,1029,197.3C1097.1,160,1166,96,1234,74.7C1302.9,53,1371,75,1406,85.3L1440,96L1440,0L1405.7,0C1371.4,0,1303,0,1234,0C1165.7,0,1097,0,1029,0C960,0,891,0,823,0C754.3,0,686,0,617,0C548.6,0,480,0,411,0C342.9,0,274,0,206,0C137.1,0,69,0,34,0L0,0Z"></path>
    </svg>
        <div class="row" >
            <div id="sidebarMenu" class="col-12 col-md-2 menu" style="flex: 1 0 10%;">
                <nav >
                    @include('partials.menu')
                </nav>
            </div>
            <div class="col-12 col-md-10 content" style="flex: 1 0 90%;">
                @yield('content')
            </div>
            <div id="overlay"></div>
        </div>
    </main>
    <footer>
        @include('partials.footer')
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script></body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</html>
@yield('scripts')