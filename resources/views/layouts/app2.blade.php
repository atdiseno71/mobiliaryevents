@extends('adminlte::master')
<link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')
@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

<body class="hold-transition login-page">
    <!-- Document body -->
    @yield('content')

</body>
