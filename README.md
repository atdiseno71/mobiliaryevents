# Fierro POS - Sistema de Punto de Inventario

Este proyecto es un sistema de punto de inventario (POS) diseñado para la gestión de tiendas de herramientas.

## Instalación o clonación del proyecto 🔧

### Clonar el repositorio

Para obtener una copia del proyecto en su máquina local, ejecute el siguiente comando en su terminal:

```bash
https://github.com/atdiseno71/proyecto-inventario.git
```

### Instalación de dependencias

Una vez clonado el proyecto, instale las dependencias de PHP utilizando Composer:

```bash
composer install
```

---

## Scripts de Base de Datos y Mantenimiento

A continuación se detallan scripts SQL útiles para el mantenimiento, inicialización y corrección de datos en el sistema.

### 1. Inicialización de Inventario para Almacén Principal

Este script tiene como objetivo inicializar registros de inventario para todos los productos existentes que aún no tienen una entrada en el `almacen_id` principal (identificado como `1`).

#### Propósito
Asegurar que cada producto tenga un registro de inventario base en el almacén principal, facilitando la gestión de stock desde el momento de la creación del producto.

#### Descripción
El script realiza las siguientes acciones:
1.  **Selecciona Equipos-Insumos**: Identifica todos los productos de la tabla `productos`.
2.  **Verifica Existencia**: Comprueba si ya existe un registro en `inventarios` para el `almacen_id = 1`.
3.  **Inserta Nuevos Registros**: Si no existe, crea una entrada con stock en 0.

#### Código SQL

```sql
INSERT INTO inventarios (producto_id, almacen_id, stock, created_by, created_at, updated_at)
SELECT p.id, 1 AS almacen_id, 1 AS stock, 1 AS created_by, NOW(), NOW()
FROM productos p
LEFT JOIN inventarios i
    ON i.producto_id = p.id 
    AND i.almacen_id = 1
WHERE i.id IS NULL;
```

### 2. Actualización Masiva de Jerarquía de Equipos-Insumos

Estos scripts son útiles para corregir o sincronizar la estructura jerárquica de los productos (Grupo > Categoría > Subcategoría > Subreferencia) basándose en la `subreferencia_id` asignada.

#### Consultar árbol de relaciones actual
Esta consulta permite visualizar la relación actual entre productos, subreferencias, subcategorías, categorías y grupos.

```sql
SELECT p.id AS producto,
       p.subreferencia_id,
       sc.id AS subcategoria,
       c.id AS categoria,
       g.id AS grupo
FROM productos p
JOIN subreferencias sr ON sr.id = p.subreferencia_id
JOIN subcategorias sc ON sc.id = sr.subcategoria_id
JOIN categorias c ON c.id = sc.categoria_id
JOIN grupos g ON g.id = c.grupo_id;
```

#### Actualización masiva del árbol de relaciones
Este script actualiza las claves foráneas (`subcategoria_id`, `categoria_id`, `grupo_id`) en la tabla `productos` para que coincidan con la jerarquía definida por su `subreferencia_id`.

```sql
UPDATE productos p
JOIN subreferencias sr ON sr.id = p.subreferencia_id
JOIN subcategorias sc ON sc.id = sr.subcategoria_id
JOIN categorias c ON c.id = sc.categoria_id
JOIN grupos g ON g.id = c.grupo_id
SET 
    p.subcategoria_id = sc.id,
    p.categoria_id = c.id,
    p.grupo_id = g.id;
```