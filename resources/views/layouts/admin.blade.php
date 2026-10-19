<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    
    <title>Diagnosis Project - @yield('title')</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Vite CSS -->
    @vite(['resources/css/app.css'])

    <!-- SQA-io Theme -->
    <link href="{{ asset('css/sqa-theme.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>

<body id="page-top"
    @auth
        data-authenticated="1"
        @if(session('enter_fullscreen')) data-enter-fullscreen="1" @endif
    @endauth
>
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <div class="sqa-content-header">
                    @include('layouts.partials.topbar')
                    @stack('page-header')
                </div>

                <div class="container-fluid sqa-page-body">
                    @yield('content')
                </div>
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('layouts.partials.footer')
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Vite JS -->
    @vite(['resources/js/app.js'])
    
    @stack('scripts')
    <script src="{{ asset('js/fullscreen.js') }}"></script>
</body>
</html>