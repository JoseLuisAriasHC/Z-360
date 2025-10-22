<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EcommerceSettingsRequest;
use App\Http\Requests\EmpresaSettingsRequest;
use App\Http\Requests\EnvioSettingsRequest;
use App\Http\Requests\WebSettingsRequest;
use App\Models\WebSettings;

class WebSettingsControllerADM extends Controller
{
    /**
     * Devuelve los datos de configuración de la empresa.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmpresaSettings()
    {
        $companyKeys = [
            'empresa_nombre',
            'empresa_direccion',
            'empresa_telefono',
            'empresa_email',
        ];

        $settings = WebSettings::whereIn('clave', $companyKeys)->get()->keyBy('clave');

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Devuelve las configuraciones relacionadas con el envío.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEnvioSettings()
    {
        $shippingKeys = [
            'coste_envio',
            'free_coste_envio_from',
        ];

        $settings = WebSettings::whereIn('clave', $shippingKeys)->get()->keyBy('clave');

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }


    /**
     * Devuelve las configuraciones relacionadas con el E-commerce (las que quedan).
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEcommerceSettings()
    {
        $ecommerceKeys = [
            'iva',
            'email_admin',
            'max_fotos_producto_por_usuario',
        ];

        $settings = WebSettings::whereIn('clave', $ecommerceKeys)->get()->keyBy('clave');

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Guarda múltiples configuraciones de la empresa a la vez.
     */
    public function saveEmpresaSettings(EmpresaSettingsRequest $request)
    {
        $data = $request->validated();
        foreach ($data as $clave => $valor) {
            WebSettings::where('clave', $clave)->update(['valor' => $valor]);
        }
        // Devolvemos el grupo de configuraciones actualizado (refrescado de la BD)
        $settings = $this->getEmpresaSettings()->getData()->data;


        return response()->json([
            'success' => true,
            'message' => "Configuraciones de Empresa actualizadas.",
            'data'    => $settings,
        ]);
    }

    /**
     * Guarda múltiples configuraciones de envío a la vez.
     */
    public function saveEnvioSettings(EnvioSettingsRequest $request)
    {
        $data = $request->validated();
        foreach ($data as $clave => $valor) {
            WebSettings::where('clave', $clave)->update(['valor' => $valor]);
        }
        // Devolvemos el grupo de configuraciones actualizado (refrescado de la BD)
        $settings = $this->getEnvioSettings()->getData()->data;

        return response()->json([
            'success' => true,
            'message' => "Configuraciones de Envío actualizadas",
            'data'    => $settings,
        ]);
    }

    /**
     * Guarda múltiples configuraciones de E-commerce a la vez.
     */
    public function saveEcommerceSettings(EcommerceSettingsRequest $request)
    {
        $data = $request->validated();
        $updatedCount = 0;

        foreach ($data as $clave => $valor) {
            WebSettings::where('clave', $clave)->update(['valor' => $valor]);
        }

        // Devolvemos el grupo de configuraciones actualizado (refrescado de la BD)
        $settings = $this->getEcommerceSettings()->getData()->data;

        return response()->json([
            'success' => true,
            'message' => "Configuraciones de E-commerce actualizadas.",
            'data'    => $settings,
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
}
