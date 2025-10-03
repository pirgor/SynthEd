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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .flex-grow-1 {
            margin-left: 250px;
            /* same as sidebar width */
        }
    </style>

</head>

<body>
    <div id="app" class="d-flex">
        @auth

            <!-- Sidebar -->
            <nav class="d-flex flex-column flex-shrink-0 p-3 shadow"
                style="width: 250px; height: 100vh; position: fixed; top: 0; left: 0; background-color: #224D3D;">

                <a href="{{ url('/') }}"
                    class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
                    <i class="bi bi-book fs-2 me-2 text-white"></i>
                    <span class="fs-4 fw-bold text-white">{{ config('app.name', 'SynthEd') }}</span>
                </a>
                <hr class="text-white">

                <!-- Top menu -->
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a class="nav-link sidebar-link" href="{{ route('auth.edit') }}">
                            <i class="bi bi-person-circle me-2"></i> Profile
                        </a>
                    </li>
                    <!-- Replace the existing notifications link with this -->
                    <li>
                        <a class="nav-link sidebar-link" href="{{ route('instructor.notifications.index') }}">
                            <i class="bi bi-bell me-2"></i> Notifications
                            @if (auth()->user()->unreadNotifications()->count() > 0)
                                <span
                                    class="badge bg-danger ms-auto">{{ auth()->user()->unreadNotifications()->count() }}</span>
                            @endif
                        </a>
                    </li>

                    @if (Auth::user()->user_role === 'student')
                        <li>
                            <a class="nav-link sidebar-link" href="{{ route('student.stud.lessons') }}">
                                <i class="bi bi-journal-text me-2"></i> Course Content
                            </a>
                        </li>
                        <li>
                            <a class="nav-link sidebar-link" href="{{ route('student.grades') }}">
                                <i class="bi bi-clipboard-check me-2"></i> My Grades
                            </a>
                        </li>
                    @elseif (Auth::user()->user_role === 'instructor')
                        <li>
                            <a href="{{ route('instructor.dashboard') }}" class="nav-link sidebar-link">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('instructor.announcements.index') }}" class="nav-link sidebar-link">
                                <i class="bi bi-megaphone me-2"></i> Announcements
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('instructor.lessons.index') }}" class="nav-link sidebar-link">
                                <i class="bi bi-file-earmark-arrow-up me-2"></i> Course Content
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('instructor.quizzes.index') }}" class="nav-link sidebar-link">
                                <i class="bi bi-clipboard-data me-2"></i> Assessment Management
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('instructor.analytics.index') }}" class="nav-link sidebar-link">
                                <i class="bi bi-bar-chart me-2"></i> Analytics
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('instructor.progress.index') }}" class="nav-link sidebar-link">
                                <i class="bi bi-graph-up me-2"></i> Student Progress
                            </a>
                        </li>
                    @endif
                </ul>

                <!-- Bottom menu -->
                <ul class="nav nav-pills flex-column mt-auto">
                    <li>
                        <a class="nav-link sidebar-link" href="{{ route('tts.settings.edit') }}">
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
                    @unless (request()->routeIs('student.quizzes.take') ||
                            request()->routeIs('student.quizzes.results') ||
                            request()->routeIs('student.quizzes.attempts'))
                        @include('chatbot.chatbot')
                    @endunless
                @endauth
            </main>
        </div>
    </div>
    @yield('scripts')

    <script>
        // Update notification count in sidebar
        function updateNotificationCount() {
            fetch('{{ route('instructor.notifications.index') }}')
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const count = doc.querySelector('.badge.bg-danger')?.textContent || 0;

                    const badge = document.querySelector('.nav-link[href*="notifications"] .badge');
                    if (badge) {
                        badge.textContent = count;
                        badge.style.display = count > 0 ? 'inline-block' : 'none';
                    }
                });
        }

        // Check every 30 seconds
        setInterval(updateNotificationCount, 30000);
    </script>

</body>

</html>
