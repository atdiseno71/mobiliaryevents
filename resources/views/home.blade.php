@extends('layouts.app')

@section('template_title')
    Dashboard
@endsection

@section('css')
    <style>
        .dashboard-hero {
            position: relative;
            min-height: calc(100vh - 114px);
            border-radius: 0;
            overflow: hidden;
            background-image: url('{{ asset('img/back.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .dashboard-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(20, 20, 20, 0.55), rgba(20, 20, 20, 0.35));
            backdrop-filter: blur(1px);
        }

        .dashboard-content {
            position: relative;
            z-index: 2;
            padding: 60px 30px;
            min-height: calc(100vh - 114px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dashboard-panel {
            width: 100%;
            max-width: 900px;
            text-align: center;
        }

        .dashboard-title {
            color: #fff;
            font-size: 3rem;
            font-weight: 800;
            font-style: italic;
            margin-bottom: 2rem;
            text-shadow: 0 3px 10px rgba(0, 0, 0, 0.35);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 18px;
        }

        .dashboard-card {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            width: 100%;
            min-height: 48px;
            padding: 12px 16px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.95);
            color: #495057 !important;
            text-decoration: none !important;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
            transition: all .18s ease-in-out;
            font-size: 14px;
            font-weight: 600;
        }

        .dashboard-card i {
            width: 18px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            background: #ffffff;
            color: #222 !important;
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.18);
        }

        .dashboard-card:hover i {
            color: #333;
        }

        .dashboard-clock {
            margin-top: 24px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-size: 14px;
            backdrop-filter: blur(6px);
        }

        .dashboard-clock i {
            opacity: .9;
        }

        @media (max-width: 991.98px) {
            .dashboard-title {
                font-size: 2.3rem;
            }

            .dashboard-grid {
                grid-template-columns: repeat(2, minmax(180px, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .dashboard-content {
                padding: 30px 15px;
            }

            .dashboard-title {
                font-size: 1.8rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="dashboard-hero">
        <div class="dashboard-content">
            <div class="dashboard-panel">
                <h1 class="dashboard-title">{!! config('adminlte.logo') !!}</h1>

                <div class="dashboard-grid">
                    @can('remisiones.index')
                        <a href="{{ url('remisiones') }}" class="dashboard-card">
                            <i class="fas fa-file-alt"></i>
                            <span>Remisiones</span>
                        </a>
                    @endcan

                    @can('movimientos.index')
                        <a href="{{ url('movimientos') }}" class="dashboard-card">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Movimientos</span>
                        </a>
                    @endcan

                    @can('productos.index')
                        <a href="{{ url('productos') }}" class="dashboard-card">
                            <i class="fas fa-boxes"></i>
                            <span>Productos</span>
                        </a>
                    @endcan

                    @can('inventarios.index')
                        <a href="{{ url('inventarios') }}" class="dashboard-card">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Inventario</span>
                        </a>
                    @endcan

                    @can('combinaciones.index')
                        <a href="{{ url('combinaciones') }}" class="dashboard-card">
                            <i class="fas fa-box-open"></i>
                            <span>Combo Productos</span>
                        </a>
                    @endcan

                    @can('clientes.index')
                        <a href="{{ url('clientes') }}" class="dashboard-card">
                            <i class="fas fa-user-tie"></i>
                            <span>Clientes</span>
                        </a>
                    @endcan

                    @can('proveedores.index')
                        <a href="{{ url('proveedores') }}" class="dashboard-card">
                            <i class="fas fa-truck-loading"></i>
                            <span>Proveedores</span>
                        </a>
                    @endcan

                    @can('usuarios.index')
                        <a href="{{ url('usuarios') }}" class="dashboard-card">
                            <i class="fas fa-users"></i>
                            <span>Usuarios</span>
                        </a>
                    @endcan
                </div>

                <div class="dashboard-clock">
                    <i class="fas fa-clock"></i>
                    <span id="dashboardDateTime"></span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function updateDashboardDateTime() {
            const now = new Date();

            const formatted = now.toLocaleString('es-CO', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            const target = document.getElementById('dashboardDateTime');
            if (target) {
                target.textContent = formatted.charAt(0).toUpperCase() + formatted.slice(1);
            }
        }

        updateDashboardDateTime();
        setInterval(updateDashboardDateTime, 1000);
    </script>
@endsection
