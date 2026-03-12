<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ComprasExport implements WithMultipleSheets
{
    protected $listaProductos;

    public function __construct($listaProductos)
    {
        $this->listaProductos = $listaProductos;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->listaProductos as $producto) {
            $sheets[] = new ComprasPorProductoExport($producto);
        }

        return $sheets;
    }
}
