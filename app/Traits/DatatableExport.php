<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait DatatableExport
{
    protected function dtApplyLength(Request $request, int $defaultPerPage, int $maxExport = 10000): void
    {
        $length = (int) $request->input('length', $defaultPerPage);

        if ($request->boolean('export')) {
            $length = $maxExport;
            $request->merge(['start' => 0]);
        }

        $length = max(1, min($length, $maxExport));
        $request->merge(['length' => $length]);
    }
}
