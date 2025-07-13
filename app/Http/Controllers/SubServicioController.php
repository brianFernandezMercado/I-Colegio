<?php

namespace App\Http\Controllers;
use PDOException;
use Illuminate\Database\QueryException;
use App\Models\SubServicio;
use App\Models\CategoriaServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubServicioController extends Controller
{
    public function getListSubCategory()
    {
        try {
            return response()->json([
                'status' => true,
                'data' => SubServicio::with('categoria')->get(),
                'message' => 'Subservicios obtenidos correctamente',
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
                'message' => 'Error al obtener subservicios: ',
            ], 500);
        }
    }

    public function registerSubCategory(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string',
                'descripcion' => 'string',
                'categoria_servicio_id' => 'required|exists:categorias_servicios,id',
            ], [
                'nombre.required' => 'El nombre es obligatorio.',
                'categoria_servicio_id.required' => 'La categoría es obligatoria.',
                'categoria_servicio_id.exists' => 'La categoría no existe.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $sub = CategoriaServicio::where('id', $request->categoria_servicio_id)->where('activo', 0)->first();
            if ($sub) {
                return response()->json([
                    'status' => false,
                    'message' => 'El subservicio esta inactivo.',
                    'data' => [],
                ], 409);
            }

            $data = $request->only(['nombre', 'descripcion', 'categoria_servicio_id']);
            if ($request->hasFile('icono')) {
                $imagen = $request->file('icono');
                $nombreArchivo = time() . '_' . preg_replace('/\s+/', '_', $imagen->getClientOriginalName());
                $imagen->move(public_path('subservicios'), $nombreArchivo);
                $data['icono'] = url('subservicios/' . $nombreArchivo);
            }


            $sub = SubServicio::create($data);
            return response()->json([
                'status' => true,
                'message' => 'Subservicio creado correctamente.',
                'data' => $sub,
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
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function showSubCategory($id)
    {
        try {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:sub_servicios,id',
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
                'message' => 'Subservicio obtenido correctamente.',
                'data' => SubServicio::with('categoria')->findOrFail($id),
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
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function updateSubCategory(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string',
                'descripcion' => 'nullable|string',
                'icono' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'categoria_servicio_id' => 'required|exists:categorias_servicios,id',
            ], [
                'nombre.required' => 'El nombre es obligatorio.',
                'categoria_servicio_id.required' => 'La categoría es obligatoria.',
                'categoria_servicio_id.exists' => 'La categoría no existe.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $categoria = CategoriaServicio::find($request->categoria_servicio_id);

            if (!$categoria) {
                return response()->json([
                    'status' => false,
                    'message' => 'La categoría no existe.',
                    'data' => [],
                ], 404);
            }

            if (!$categoria->activo) {
                return response()->json([
                    'status' => false,
                    'message' => 'La categoría está inactiva.',
                    'data' => [],
                ], 409);
            }
            $sub = SubServicio::find($id);

            if (!$sub) {
                return response()->json([
                    'status' => false,
                    'message' => 'Subservicio no encontrado.',
                    'data' => [],
                ], 404);
            }
            $data = $request->only(['nombre', 'descripcion', 'categoria_servicio_id']);
            if ($request->hasFile('icono')) {
                if ($categoria->icono && str_contains($categoria->icono, url('/subservicios/'))) {
                    $rutaAnterior = public_path('subservicios/' . basename($categoria->icono));
                    if (file_exists($rutaAnterior)) {
                        unlink($rutaAnterior);
                    }
                }
                $imagen = $request->file('icono');
                $nombreArchivo = time() . '_' . preg_replace('/\s+/', '_', $imagen->getClientOriginalName());
                $imagen->move(public_path('subservicios'), $nombreArchivo);
                $data['icono'] = url('subservicios/' . $nombreArchivo);
            }

            $sub->update($request->only($data));
            return response()->json([
                'status' => true,
                'message' => 'Subservicio actualizado correctamente.',
                'data' => $sub,
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
                'message' => 'Error al actualizar subservicio: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }



    public function changeStatus($id)
    {
        try {
            $sub = SubServicio::findOrFail($id);

            if (!$sub) {
                return response()->json([
                    'status' => false,
                    'message' => 'Subservicio no encontrado.',
                    'data' => [],
                ], 404);
            }

            $sub->activo = !$sub->activo;
            $sub->save();

            return response()->json([
                'status' => true,
                'message' => 'Subservicio actualizado correctamente.',
                'data' => $sub,
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
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
