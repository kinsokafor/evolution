<html>
    <head>
        <title>{{$site_name}} - @yield('title')</title>
        {!! Assoto\Stack::display('head') !!}
    </head>
    <body>
        @section('sidebar')
            This is the master sidebar.
        @show

        <div class="container">
            @yield('content')
        </div>
        {!! Assoto\Stack::display('footer') !!}
    </body>
</html>