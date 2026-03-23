<?php

return [
    [
        'text' => 'Configuración',
        'icon' => 'fas fa-cogs',
        'can' => 'getSelects',
        'submenu' => [
            [
                'text' => 'Empresa',
                'url' => 'empresa',
                'icon' => 'fas fa-building',
                'can' => 'empresa.index',
            ],
            [
                'text' => 'Roles',
                'url' => 'roles',
                'icon' => 'fas fa-user-shield',
                'can' => 'roles.index',
            ],
            [
                'text' => 'Usuarios',
                'url' => 'usuarios',
                'icon' => 'fas fa-users',
                'can' => 'usuarios.index',
            ],
        ],
    ],
    [
        'text' => 'Parámetros',
        'icon' => 'fas fa-sliders-h',
        'can' => 'getSelects',
        'submenu' => [
            [
                'text' => 'Almacen',
                'url' => 'almacenes',
                'icon' => 'fas fa-warehouse',
                'can' => 'almacenes.index',
            ],
            [
                'text' => 'Tipo Eventos',
                'url' => 'tipos-evento',
                'icon' => 'fas fa-calendar-check',
                'can' => 'tipos-evento.index',
            ],
            [
                'text' => 'Grupos',
                'url' => 'grupos',
                'icon' => 'fas fa-layer-group',
                'can' => 'grupos.index',
            ],
            [
                'text' => 'Categorias',
                'url' => 'categorias',
                'icon' => 'fas fa-tags',
                'can' => 'categorias.index',
            ],
            [
                'text' => 'SubCategorias',
                'url' => 'subcategorias',
                'icon' => 'fas fa-tag',
                'can' => 'subcategorias.index',
            ],
            [
                'text' => 'SubReferencias',
                'url' => 'subreferencias',
                'icon' => 'fas fa-sitemap',
                'can' => 'subreferencias.index',
            ],
            [
                'text' => 'Productos',
                'url' => 'productos',
                'icon' => 'fas fa-boxes',
                'can' => 'productos.index',
            ],
            [
                'text' => 'Combo Productos',
                'url' => 'combinaciones',
                'icon' => 'fas fa-box-open',
                'can' => 'combinaciones.index',
            ],
        ],
    ],
    [
        'text' => 'General',
        'icon' => 'fas fa-briefcase',
        'can' => 'getSelects',
        'submenu' => [
            [
                'text' => 'Clientes',
                'url' => 'clientes',
                'icon' => 'fas fa-user-tie',
                'can' => 'clientes.index',
            ],
            [
                'text' => 'Proveedores',
                'url' => 'proveedores',
                'icon' => 'fas fa-truck-loading',
                'can' => 'proveedores.index',
            ],
        ],
    ],
    [
        'text' => 'Remisiones',
        'icon' => 'fas fa-file-invoice',
        'can' => 'getSelects',
        'submenu' => [
            [
                'text' => 'Remisiones',
                'url' => 'remisiones',
                'icon' => 'fas fa-file-alt',
                'can' => 'remisiones.index',
            ],
            [
                'text' => 'Cotizaciones',
                'url' => 'cotizaciones',
                'icon' => 'fas fa-file-signature',
                'can' => 'cotizaciones.index',
            ],
            [
                'text' => 'Movimientos',
                'url' => 'movimientos',
                'icon' => 'fas fa-exchange-alt',
                'can' => 'movimientos.index',
            ],
            [
                'text' => 'Inventario',
                'url' => 'inventarios',
                'icon' => 'fas fa-clipboard-list',
                'can' => 'inventarios.index',
            ],
        ],
    ],
];