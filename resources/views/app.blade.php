<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <!-- libraries CSS -->
    <link rel="stylesheet" href="{{ asset('assets/icon/flaticon_restics.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/splide/splide.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/slim-select/slimselect.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/animate-wow/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/flatpickr/flatpickr.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/css/flag-icons.min.css"/>

    <!-- custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @vite('resources/js/app.js')
    @inertiaHead
    @routes

</head>

<body class="font-Montserrat bg-white text-slate-900 dark:bg-slate-700 dark:text-white">
    <div class="preloader" id="preloader">
        <div class="loader"></div>
    </div>

    @inertia

        <!-- libraries JS -->
        <script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/splide/splide.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/splide/splide-extension-auto-scroll.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/slim-select/slimselect.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/animate-wow/wow.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/splittype/index.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/mixitup/mixitup.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/fslightbox/fslightbox.js') }}"></script>
        <script src="{{ asset('assets/vendor/flatpickr/flatpickr.js') }}"></script>
    
        <!-- custom JS -->
        <script src="{{ asset('assets/js/main.js') }}"></script>
        <script src="{{ asset('assets/js/tab.js') }}"></script>
        <script src="{{ asset('assets/js/countdown.js') }}"></script>
</body>

</html>
