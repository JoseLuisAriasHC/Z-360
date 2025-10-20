<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarcaRequest;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MarcaControllerADM extends Controller
{
    /**
     * Listar todas las marcas.
     */
    public function index()
    {
        $marcas = Marca::all();

        return response()->json([
            'success' => true,
            'data' => $marcas
        ]);
    }

    /**
     * Guardar una nueva marca.
     */
    public function store(MarcaRequest $request)
    {
        $validated = $request->validated();
        $log_path = null;

        if ($request->hasFile('logo')) {
            $log_path = $request->file('logo')->store('marca_logos', 'public');
        }

        $marca = Marca::create([
            'nombre'       => $validated['nombre'],
            'talla_offset' => $validated['talla_offset'],
            'logo'     => $log_path
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Marca creada correctamente',
            'data' => $marca
        ], 201);
    }

    /**
     * Mostrar una marca específica.
     */
    public function show(Marca $marca)
    {
        return response()->json([
            'success' => true,
            'data' => $marca
        ]);
    }

    public function update(MarcaRequest $request, Marca $marca)
    {
        $validatedData = $request->validated();
        $logoPath = null;
        if ($request->hasFile('logo') && $marca->logo && Storage::disk('public')->exists($marca->logo))
            Storage::disk('public')->delete($marca->logo);

        $updateData = [
            'nombre' => $validatedData['nombre'], 
            'talla_offset' => $validatedData['talla_offset'],
        ];
        
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('marca_logos', 'public');
            $updateData['logo'] = $logoPath;
        } else {
            $updateData['logo'] = $marca->logo;
        }

        $marca->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Marca actualizada correctamente',
            'data' => $marca
        ]);
    }
    /**
     * Eliminar una marca.
     */
    public function destroy(Marca $marca)
    {
        $marca->delete();

        return response()->json([
            'success' => true,
            'message' => 'Marca eliminada correctamente'
        ]);
    }

    /**
     * Eliminar múltiples marcas.
     * Recibe un array de IDs y los elimina de forma eficiente.
     */
    public function destroyMultiple(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:marcas,id',
        ]);

        $ids = $validated['ids'];
        $marcas = Marca::whereIn('id', $ids)->get();
        foreach ($marcas as $marca) {
            if ($marca->logo && Storage::disk('public')->exists($marca->logo)) {
                Storage::disk('public')->delete($marca->logo);
            }
        }
        $count = Marca::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => "{$count} marcas eliminadas correctamente."
        ]);
    }
}
