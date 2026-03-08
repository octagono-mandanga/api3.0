<?php

namespace App\Http\Controllers\Api\Root;

use App\Http\Controllers\Controller;
use App\Models\Institucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\InstitucionService;



class InstitucionController extends Controller
{
    public function index()
    {
        return response()->json(Institucion::with('sedes')->get());
    }

public function store(Request $request)
{
    // Usamos el namespace del modelo para que Laravel resuelva correctamente el esquema.tabla
    $validated = $request->validate([
        'legal_name'       => 'required|string',
        'nit'              => 'required|string|unique:App\Models\Institucion,nit', 
        'short_name'       => 'nullable|string|max:100',
        'dane_code'        => 'nullable|string|unique:App\Models\Institucion,dane_code',
        'institution_type' => 'nullable|string',
        'official_email'   => 'required|email',
        'website_url'      => 'nullable|string',
        'status'           => 'required|in:activo,inactivo',
        'branding_colors'  => 'nullable|array'
    ]);

    try {
        // Creación directa en auth.institutions
        $institucion = Institucion::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Institución creada exitosamente',
            'data'    => $institucion
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Fallo al crear la institución',
            'details' => $e->getMessage()
        ], 500);
    }
}

    public function show(Institucion $institucion)
    {
        return response()->json($institucion->load('sedes'));
    }

public function update(Request $request, Institucion $institucion)
    {
        // Usamos validación basada en la clase del Modelo para evitar errores de conexión
        $validated = $request->validate([
            'legal_name'       => 'required|string',
            'nit'              => 'required|string|unique:App\Models\Institucion,nit,' . $institucion->id,
            'short_name'       => 'nullable|string|max:100',
            'institution_type' => 'nullable|string',
            'official_email'   => 'required|email',
            'website_url'      => 'nullable|url',
            'status'           => 'required|in:activo,inactivo',
            'branding_colors'  => 'required|array',
            'dane_code'        => 'nullable|string'
        ]);

        try {
            // Actualización directa en auth.institutions
            $institucion->update($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Institución actualizada correctamente',
                'data'    => $institucion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Fallo al actualizar los datos',
                'details' => $e->getMessage()
            ], 500);
        }
    }

// Declaramos la propiedad protegida
    protected $institucionService;

    /**
     * Inyección de dependencias por constructor
     */
    public function __construct(InstitucionService $institucionService)
    {
        $this->institucionService = $institucionService;
    }

public function uploadLogo(Request $request)
{


$request->validate([
    'institution_id' => 'required|uuid|exists:App\Models\Institucion,id',
    'logo'           => 'required|string'
]);
    try {
        $institucion = Institucion::findOrFail($request->institution_id);
        
        // El servicio se encarga de redimensionar a 256x256 y convertir a JPG
        $url = $this->institucionService->actualizarLogoInstitucion($institucion, $request->logo);

        return response()->json([
            'status'  => 'success',
            'message' => 'Escudo institucional actualizado con éxito',
            'url'     => $url
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Fallo al procesar el escudo institucional',
            'details' => $e->getMessage()
        ], 500);
    }
}	
}
