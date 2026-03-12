<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ComprasPorProductoExport implements FromView, WithTitle
{
    protected $producto;

    public function __construct($producto)
    {
        $this->producto = $producto;
    }

    public function view(): View
    {
        return view('compra.show_excel', [
            'listaProducto' => $this->producto,
        ]);
    }

    public function title(): string
    {
        return substr($this->producto['combi_nombre'], 0, 30);
    }
}
