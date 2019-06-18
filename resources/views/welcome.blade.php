<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    SiRANGGA - API
                </div>

                <div class="links">
                    {{-- <a href="https://laravel.com/docs">Documentation</a>
                    <a href="https://laracasts.com">Laracasts</a>
                    <a href="https://laravel-news.com">News</a>
                    <a href="https://forge.laravel.com">Forge</a>
                    <a href="https://github.com/laravel/laravel">GitHub</a> --}}
                    @php
                        $api=['alat','alat-ruang','eselon-1','eselon-2','notifikasi','pinjam','pinjam-by-peminjam/{user_id}','pinjam-by-ruang/{ruang_id}','pinjam-alat','pinjam-alat-by-pinjamid/{pinjam_id}','pinjam-notes','pinjam-notes-by-pinjamid/{pinjam_id}','pinjam-notes-by-userid/{user_id}','pinjam-rate','pinjam-rate-by-pinjam/{pinjam_id}','role','ruang','slider','user'];
                    @endphp
                    <ul style="list-style: none">
                        @foreach ($api as $item)
                            <li style="padding:3px 0;border-bottom:1px dotted #ddd;">{{url('/')}}/api/{{$item}}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>
