<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Restaurant Order Management</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/feather/feather.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('src/assets/vendors/ti-icons/css/themify-icons.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/fontawesome-free-6.7.2-web/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/fontawesome-free-6.7.2-web/css/solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/vendors/fontawesome-free-6.7.2-web/css/regular.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('src/assets/vendors/mdi/css/materialdesignicons.min.css') }}"> --}}

    {{-- summer note css  --}}
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">

    {{-- select2 --}}
    <link rel="stylesheet" href="{{ asset('src/assets/css/select2.min.css') }}">

    {{-- date time flatpickr  --}}
    <link rel="stylesheet" href="{{ asset('src/assets/css/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/css/flatpickr-month-year.css') }}">

    {{-- <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script> --}}


    {{-- for dashboard css ui  --}}
    <link rel="stylesheet" href="{{ asset('src/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('src/assets/css/toastr.min.css') }}">

    <!-- Summernote CSS -->

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @yield('css')
</head>

<body>

    <div class="loader-container">
        <div class="loader-content">
            <span class="loader"></span>
        </div>
    </div>

    {{-- navbar --}}
    <div class="container-fluid page-body-wrapper" style="padding-top:85px;">
        @include('layouts.navbar')
        {{-- sidebar --}}
        @include('layouts.sidebar')
        {{-- <div class="main-panel"> --}}
        <div class="container-fluid main-content">
            <div class="row">
                <div class="col-12">
                    @yield('content')

                </div>
            </div>
        </div>

        @include('layouts.bs-toast-message')
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        {{-- <footer class="footer">
                <div class="d-sm-flex justify-content-center justify-content-sm-between">
                    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2025.
                        <a href="#" target="_blank">AUSphere</a>. All rights reserved.</span>
                    <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with
                        <i class="ti-heart text-danger ms-1"></i></span>
                </div>
            </footer> --}}
        <!-- partial -->
        {{-- </div> --}}
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
    </div>

    {{-- vendor.bundle.base.js is jquery and bootstrap file bundle --}}
    <script src="{{ asset('src/assets/vendors/js/vendor.bundle.base.js') }}"></script>

    <script src="{{ asset('src/assets/js/template.js') }}"></script>
    <script src="{{ asset('src/assets/js/off-canvas.js') }}"></script>
    {{-- toastr js  --}}
    {{-- sweetalert2 --}}
    <script src="{{ asset('src/assets/js/sweetalert2@11.js') }}"></script>

    <script src="{{ asset('src/assets/js/toastr.min.js') }}"></script>

    <!-- Summernote Bootstrap 5 compatible version by community-->

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

    {{-- select 2 --}}
    <script src="{{ asset('src/assets/js/select2.min.js') }}"></script>

    {{-- date time flatpickr  --}}
    <script src="{{ asset('src/assets/js/flatpickr.js') }}"></script>
    <script src="{{ asset('src/assets/js/flatpickr-month-year.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.3/pusher.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- toast message --}}

    <script src="{{ asset('js/custom.js') }}"></script>



    @stack('js')


    {{-- <script>
        @if (auth()->check())
            // Pusher.logToConsole = true;
            var user = @json(auth()->user());
            var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true,
                forceTLS: true,
            });

            var channel = pusher.subscribe('my-channel-test.' + user.id);

            channel.bind('comment-notification', function(data) {
                var badge = document.getElementById('notification-count');

                if (badge) {
                    var notiCount = parseInt(badge.innerText) || 0;
                    notiCount++;
                    badge.innerText = notiCount;

                    // remove 'd-none' class to show badge
                    badge.classList.remove('d-none');
                }

                // console.log('New notification:', data.message);
            });

            var chatChannel = pusher.subscribe('my-channel-test.' + user.id);

            chatChannel.bind('chat-notification', function(data) {
                console.log('Chat notification received', data);
                var chatBadge = document.getElementById('chat-notification-count');

                if (chatBadge) {
                    var chatNotiCount = parseInt(chatBadge.innerText) || 0;
                    chatNotiCount++;
                    chatBadge.innerText = chatNotiCount;

                    // remove 'd-none' class to show chatBadge
                    chatBadge.classList.remove('d-none');
                }

                // console.log('New chat notification:', data.message);
            });
        @endif
    </script> --}}
    {{--
   <script>

//     // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

     var pusher = new Pusher('4e8eae8a55fa0aa4492d', {
       cluster: 'ap1'
     });

     var channel = pusher.subscribe('my-channel-test');
     channel.bind('my-event-test', function(data) {
       alert(JSON.stringify(data));
        });
   </script> --}}


</body>

</html>
