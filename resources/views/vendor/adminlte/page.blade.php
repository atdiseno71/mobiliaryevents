@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@section('adminlte_css')
    @stack('css')
    @yield('css')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <style>
        /* Switch container */
        .switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 24px;
        }

        /* Ocultar checkbox */
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* Fondo del switch */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .3s;
            border-radius: 34px;
        }

        /* Botón redondo */
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }

        /* Activado */
        .switch input:checked+.slider {
            background-color: #4CAF50;
        }

        .switch input:checked+.slider:before {
            transform: translateX(22px);
        }

        /* Deshabilitado */
        .switch input:disabled+.slider {
            opacity: .5;
            cursor: not-allowed;
        }

        #subreferencia_wrap {
            display: none;
        }

        /* para modulo de remisiones */
        .toggle-subref {
            color: #333 !important;
            /* color neutro */
            text-decoration: none !important;
            font-weight: 600;
        }

        .toggle-subref:hover,
        .toggle-subref:focus {
            color: #000 !important;
            /* un pelito más oscuro al hover */
            text-decoration: none !important;
        }

        .toggle-subref.btn-link {
            padding-left: 0 !important;
            padding-right: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            box-shadow: none !important;
            background: none !important;
            border: none !important;
        }

        /* BOTÓN GLOBAL DE EXPORTAR EXCEL — CUSTOM */
        .dt-button.buttons-excel.buttons-html5 {
            background: linear-gradient(135deg, #22c55e, #16a34a) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 8px 16px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
            transition: all 0.2s ease-in-out !important;
            margin-bottom: 12px !important;
        }

        /* Hover */
        .dt-button.buttons-excel.buttons-html5:hover {
            background: linear-gradient(135deg, #16a34a, #15803d) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(22, 163, 74, 0.35) !important;
        }

        /* Active */
        .dt-button.buttons-excel.buttons-html5:active {
            transform: translateY(0);
            box-shadow: 0 2px 5px rgba(22, 163, 74, 0.25) !important;
        }

        /* Ícono opcional para estilo más bacano */
        .dt-button.buttons-excel.buttons-html5::before {
            font-size: 16px;
            margin-right: 4px;
        }

        /* Icono centrado (imagen) */
        .card_icon {
            width: 54%;
            /* ajusta tamaño */
            height: 44px;
            margin-left: .5rem;
            /* separación del texto */
            display: inline-block;

            background-image: url('/img/logoarriba.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/override-adminlte.css') }}">

@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
    <div class="wrapper">

        {{-- Top Navbar --}}
        @if ($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if (!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        @empty($iFrameEnabled)
            @include('adminlte::partials.cwrapper.cwrapper-default')
        @else
            @include('adminlte::partials.cwrapper.cwrapper-iframe')
        @endempty

        {{-- Footer --}}
        @hasSection('footer')
            @include('adminlte::partials.footer.footer')
        @endif

        {{-- Right Control Sidebar --}}
        @if (config('adminlte.right_sidebar'))
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif

    </div>
@stop

@section('adminlte_js')
    <!-- Sweetalert2 for alerts more nice -->
    <script src="{{ asset('js/plugins/sweetalert2@11.js') }}"></script>
    <!-- -->
    <script src="{{ asset('js/plugins/select2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/parsley.min.js') }}"></script>
    <script src="{{ asset('vendor/inputmask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('js/plugins/datatable.v2.js') }}"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script language="JavaScript">
        $(document).ready(() => {
            $('.select2').select2();
        });
        history.forward();
    </script>
    @stack('js')
    @yield('js')
@stop
