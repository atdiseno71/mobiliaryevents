<!DOCTYPE html>
<html lang="en">
    <head>
        {{-- Base Meta Tags --}}
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {{-- Title --}}
        <title>
            @yield('title', 'Factura')
        </title>
        {{-- Favicon --}}
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
        {{-- Custom Stylesheets --}}
        @yield('css')
        <link href="css/estilosfactura.css" rel="stylesheet" type="text/css" media="screen"/>
    </head>
    <body>
        <!-- Document body -->
        @yield('content')
        {{-- Custom Scripts --}}
        @yield('js')
    </body>
</html>
