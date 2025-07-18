<?php

namespace App\Http\Controllers;
use PDOException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Models\Pais;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    public function getListPais()
    {
        try {
            return response()->json([
                'status' => true,
                'data' => Pais::all(),
                'message' => 'Paises obtenidos correctamente',
            ], 200);

        } catch (QueryException | PDOException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de conexión a la base de datos.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
                'token' => null,
                'user' => [],
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener paises: ',
                'data' => [],
            ], 500);
        }
    }

    public function registerPais(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string',

            ], [
                'nombre.required' => 'El nombre es obligatorio.',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $data = $request->only(['nombre']);
            if ($request->hasFile('icono')) {
                $imagen = $request->file('icono');
                $nombreArchivo = time() . '_' . preg_replace('/\s+/', '_', $imagen->getClientOriginalName());
                $imagen->move(public_path('pais'), $nombreArchivo);
                $data['icono'] = url('pais/' . $nombreArchivo);
            }
            $pais = Pais::create($data);
            return response()->json([
                'status' => true,
                'message' => 'Pais creado correctamente.',
                'data' => $pais,
            ], 201);
        } catch (QueryException | PDOException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de conexión a la base de datos.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
                'token' => null,
                'user' => [],
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear pais: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function showPais($id)
    {
        try {
            $pais = Pais::findOrFail($id);
            return response()->json([
                'status' => true,
                'data' => $pais,
                'message' => 'Pais obtenido correctamente',
            ]);
        } catch (QueryException | PDOException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de conexión a la base de datos.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
                'token' => null,
                'user' => [],
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener pais: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function updatePais(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string',
                 'icono' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ], [
                'nombre.required' => 'El nombre es obligatorio.',

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $pais = Pais::findOrFail($id);
            $data = $request->only(['nombre']);
            if ($request->hasFile('icono')) {
                if ($pais->icono && str_contains($pais->icono, url('/pais/'))) {
                    $rutaAnterior = public_path('pais/' . basename($pais->icono));
                    if (file_exists($rutaAnterior)) {
                        unlink($rutaAnterior);
                    }
                }
                $imagen = $request->file('icono');
                $nombreArchivo = time() . '_' . preg_replace('/\s+/', '_', $imagen->getClientOriginalName());
                $imagen->move(public_path('pais'), $nombreArchivo);
                $data['icono'] = url('pais/' . $nombreArchivo);
            }
            $pais->update($data);
            return response()->json([
                'status' => true,
                'message' => 'Pais actualizado correctamente.',
                'data' => $pais,
            ], 200);
        } catch (QueryException | PDOException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de conexión a la base de datos.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
                'token' => null,
                'user' => [],
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar pais: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function changeStatus($id)
    {
        try {
            $pais = Pais::findOrFail($id);
            $pais->activo = !$pais->activo;
            $pais->save();
            return response()->json([
                'status' => true,
                'message' => 'Pais actualizado correctamente.',
                'data' => $pais,
            ], 200);
        } catch (QueryException | PDOException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de conexión a la base de datos.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
                'token' => null,
                'user' => [],
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar pais: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
