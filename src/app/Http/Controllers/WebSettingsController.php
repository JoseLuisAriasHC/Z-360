<?php

namespace App\Http\Controllers;

use App\Http\Requests\WebSettingsRequest;
use App\Models\WebSettings;

class WebSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => WebSettings::all()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(WebSettings $webSettings)
    {
        return response()->json([
            'success' => true,
            'data' => $webSettings
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WebSettingsRequest $request, string $clave)
    {
        $setting = WebSettings::where('clave', $clave)->firstOrFail();

        $setting->update($request->only(['valor', 'nombre', 'descripcion']));

        return response()->json([
            'success' => true,
            'message' => "Configuración '{$clave}' actualizada correctamente",
            'data'    => $setting,
        ]);
    }
}
