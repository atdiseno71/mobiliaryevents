<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresa = Empresa::first(); // Solo un registro
        return view('empresa.index', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'nit' => 'required|string|max:20',
            'nombre' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'pagina_web' => 'nullable|string|max:150',
            'pais' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:150',
            'telefonos' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $path;
        }

        $empresa->update($validated);

        return redirect()->back()->with('success', 'Información actualizada correctamente.');
    }
}
