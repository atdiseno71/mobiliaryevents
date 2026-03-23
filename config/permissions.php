<?php

return [

    // ===== ROLES DEL SISTEMA =====
    'roles' => [
        'super_root',
        'Administrador',
        'Admin Parámetros',
        'Supervisor',
        'Transacciones',
        'Consulta',
    ],

    // ===== PERMISOS SUELTOS =====
    'global_permissions' => [
        'home.index',
        'empresa.index',
        'usuario.form.cambiar-contrasena',
        'usuario.cambiar-contrasena',
        'getSelects',
        'remisiones.pdf',
        'remisiones.clone',
        'inventarios.mover',
        'inventarios.movimientos',
        'productos.ver_valor_compra',
        'productos.ver_valor_alquiler',
        'movimientos.editar_cerrados',
        'movimientos.editar_estados',
    ],

    // ===== VISTAS CON CRUD =====
    'views' => [
        'roles',
        'usuarios',
        'clientes',
        'proveedores',
        'almacenes',
        'subreferencias',
        'grupos',
        'categorias',
        'subcategorias',
        'productos',
        'remisiones',
        'cotizaciones',
        'tipos-evento',
        'inventarios',
        'movimientos',
        'combinaciones',
    ],

    // Roles que reciben permisos CRUD por defecto
    'crud_default_roles' => [
        'super_root',
        'Administrador',
    ],

];
