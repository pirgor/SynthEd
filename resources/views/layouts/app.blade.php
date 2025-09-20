<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SynthEd') }}</title>
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Custom Styles -->
    <!-- <link href="{{ asset('css/custom.css') }}" rel="stylesheet"> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap 5 -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body>
    <div id="app" class="d-flex">
        @auth
            <!-- Sidebar -->
            <nav class="d-flex flex-column flex-shrink-0 p-3 shadow"
                style="width: 250px; min-height: 100vh; background-color: #224D3D;">
                <a href="{{ url('/') }}"
                    class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
                    <i class="bi bi-book fs-2 me-2 text-white"></i>
                    <span class="fs-4 fw-bold text-white">{{ config('app.name', 'SynthEd') }}</span>
                </a>
                <hr class="text-white">

                <!-- Top menu -->
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a class="nav-link sidebar-link">
                            <i class="bi bi-person-circle me-2"></i> Profile
                        </a>
                    </li>
                    <li>
                        <a class="nav-link sidebar-link">
                            <i class="bi bi-bell me-2"></i> Notifications
                        </a>
                    </li>
                    <li>
                        <a class="nav-link sidebar-link">
                            <i class="bi bi-journal-text me-2"></i> Course Content
                        </a>
                    </li>
                    <li>
                        <a class="nav-link sidebar-link">
                            <i class="bi bi-clipboard-check me-2"></i> My Grades
                        </a>
                    </li>
                </ul>

                <!-- Bottom menu -->
                <ul class="nav nav-pills flex-column mt-auto">
                    <li>
                        <a class="nav-link sidebar-link">
                            <i class="bi bi-gear me-2"></i> Settings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('logout') }}" class="nav-link sidebar-link text-danger"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                        </a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </ul>
            </nav>
        @endauth

        <!-- Main Content -->
        <div class="flex-grow-1">
            <main class="p-4">
                @yield('content')

                @auth
                    @include('chatbot.chatbot')
                @endauth
            </main>
        </div>
    </div>
</body>
</html>
