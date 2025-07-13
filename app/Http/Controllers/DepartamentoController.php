<?php


namespace App\Http\Controllers;
use PDOException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Models\Departamento;
use App\Models\Pais;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function getListDepartamento()
    {
        try {
            return response()->json([
                'status' => true,
                'data' => Departamento::with('provincias')->get(),
                'message' => 'Departamentos obtenidos correctamente',
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
                'message' => 'Error al obtener departamentos: ',
                'data' => [],
            ], 500);
        }
    }

    public function registerDepartamento(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string',
                'pais_id' => 'required|integer|exists:paises,id',

            ], [
                'nombre.required' => 'El nombre es obligatorio.',
                'pais_id.required' => 'El pais es obligatorio.',
                'pais_id.exists' => 'El pais no existe.',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $subpais = Pais::where('id', $request->pais_id)->where('activo', 0)->first();
            if ($subpais) {
                return response()->json([
                    'status' => false,
                    'message' => 'El pais esta inactivo.',
                    'data' => [],
                ], 409);
            }

            $data = $request->only(['nombre', 'pais_id']);
            if ($request->hasFile('icono')) {
                $imagen = $request->file('icono');
                $nombreArchivo = time() . '_' . preg_replace('/\s+/', '_', $imagen->getClientOriginalName());
                $imagen->move(public_path('departamento'), $nombreArchivo);
                $data['icono'] = url('departamento/' . $nombreArchivo);
            }
            $dpto = Departamento::create($data);
            return response()->json([
                'status' => true,
                'message' => 'Departamento creado correctamente.',
                'data' => $dpto,
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
                'message' => 'Error al crear departamento: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function showDepartamento($id)
    {
        try {

            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:departamentos,id',
            ], [
                'id.required' => 'El id es obligatorio.',
                'id.exists' => 'El id no existe.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return response()->json([
                'status' => true,
                'data' => Departamento::with('provincias', 'pais')->findOrFail($id),
                'message' => 'Departamento obtenido correctamente',

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
                'message' => 'Error al obtener departamento: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function updateDepartamento(Request $request, $ids)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string',
                'pais_id' => 'required|integer|exists:paises,id',
            ], [
                'nombre.required' => 'El nombre es obligatorio.',
                'pais_id.required' => 'El país es obligatorio.',
                'pais_id.exists' => 'El país no existe.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Verificar si el país existe
            $pais = Pais::find($request->pais_id);


            if (!$pais) {
                return response()->json([
                    'status' => false,
                    'message' => 'El país no existe.',
                    'data' => [],
                ], 404);
            }
            if (!$pais->activo) {
                return response()->json([
                    'status' => false,
                    'message' => 'El país está inactivo.',
                    'data' => [],
                ], 409);
            }
            $dpto = Departamento::find($ids);
            $data = $request->only(['nombre', 'pais_id']);
            if ($request->hasFile('icono')) {
                if ($dpto->icono && str_contains($dpto->icono, url('/departamento/'))) {
                    $rutaAnterior = public_path('departamento/' . basename($dpto->icono));
                    if (file_exists($rutaAnterior)) {
                        unlink($rutaAnterior);
                    }
                }
                $imagen = $request->file('icono');
                $nombreArchivo = time() . '_' . preg_replace('/\s+/', '_', $imagen->getClientOriginalName());
                $imagen->move(public_path('departamento'), $nombreArchivo);
                $data['icono'] = url('departamento/' . $nombreArchivo);
            }
            if (!$dpto) {
                return response()->json([
                    'status' => false,
                    'message' => 'Departamento no encontrado.',
                    'data' => [],
                ], 404);
            }
            $dpto->update($data);
            return response()->json([
                'status' => true,
                'message' => 'Departamento actualizado correctamente.',
                'data' => $dpto,
            ], 200);

        } catch (QueryException | PDOException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de conexión a la base de datos.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar departamento: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }


    public function changeStatus($id)
    {
        try {
            $dpto = Departamento::findOrFail($id);
            $dpto->activo = !$dpto->activo;
            $dpto->save();
            return response()->json([
                'status' => true,
                'message' => 'Departamento actualizado correctamente.',
                'data' => $dpto,
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
                'message' => 'Error al actualizar departamento: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
