<?php

return [
    [
        'text' => 'Configuración',
        'icon' => 'fas fa-cog', // configuración general
        'can' => 'getSelects',
        'submenu' => [
            [
                'text' => 'Empresa',
                'url' => 'empresa',
                'icon' => 'fas fa-building', // empresa
                'can' => 'empresa.index'
            ],
            [
                'text' => 'Roles',
                'url' => 'roles',
                'icon' => 'fas fa-user-shield', // mejor para roles/permisos
                'can' => 'roles.index'
            ],
            [
                'text' => 'Usuarios',
                'url' => 'usuarios',
                'icon' => 'fas fa-users-cog', // usuarios con configuración
                'can' => 'usuarios.index'
            ],
        ],
    ],
    [
        'text' => 'Parámetros',
        'icon' => 'fas fa-sliders-h', // más limpio
        'can' => 'getSelects',
        'submenu' => [
            [
                'text' => 'Almacen',
                'url' => 'almacenes',
                'icon' => 'fas fa-warehouse', // jerarquía/subniveles
                'can' => 'almacenes.index'
            ],
            [
                'text' => 'Tipo Eventos',
                'url' => 'tipos-evento',
                'icon' => 'fas fa-calendar-alt',
                'can' => 'tipos-evento.index'
            ],

            [
                'text' => 'Grupos',
                'url' => 'grupos',
                'icon' => 'fas fa-object-group', // perfecto para grupos
                'can' => 'grupos.index'
            ],
            [
                'text' => 'Categorias',
                'url' => 'categorias',
                'icon' => 'fas fa-tags', // categorías
                'can' => 'categorias.index'
            ],
            [
                'text' => 'SubCategorias',
                'url' => 'subcategorias',
                'icon' => 'fas fa-tag', // subcategoría
                'can' => 'subcategorias.index'
            ],
            [
                'text' => 'SubReferencias',
                'url' => 'subreferencias',
                'icon' => 'fas fa-stream', // jerarquía/subniveles
                'can' => 'subreferencias.index'
            ],
            [
                'text' => 'Productos',
                'url' => 'productos',
                'icon' => 'fas fa-boxes-stacked', // productos/inventario
                'can' => 'productos.index'
            ],
            [
                'text' => 'Combo Equipos',
                'url' => 'combinaciones',
                'icon' => 'fas fa-box', // productos/inventario
                'can' => 'combinaciones.index'
            ],
        ],
    ],
    [
        'text' => 'General',
        'icon' => 'fas fa-layer-group', // sección general (más coherente)
        'can' => 'getSelects',
        'submenu' => [
            [
                'text' => 'Clientes',
                'url' => 'clientes',
                'icon' => 'fas fa-user-group', // clientes
                'can' => 'clientes.index'
            ],
            [
                'text' => 'Proveedores',
                'url' => 'proveedores',
                'icon' => 'fas fa-truck', // proveedor
                'can' => 'proveedores.index'
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
                'icon' => 'fas fa-file-signature',
                'can' => 'remisiones.index'
            ],
            [
                'text' => 'Movimientos',
                'url' => 'movimientos',
                'icon' => 'fas fa-right-left',
                'can' => 'movimientos.index'
            ],
            [
                'text' => 'Inventario',
                'url' => 'inventarios',
                'icon' => 'fas fa-box-open',
                'can' => 'inventarios.index'
            ],
        ],
    ],
];
