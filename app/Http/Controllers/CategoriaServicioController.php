<?php
namespace App\Http\Controllers;
use PDOException;
use App\Models\CategoriaServicio;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class CategoriaServicioController extends Controller
{
    public function getListCategory()
    {
        try {
            return response()->json([
                'status' => true,
                'data' => CategoriaServicio::all(),
                'message' => 'Categorias obtenidas correctamente',
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
                'message' => 'Error al obtener categorias: ',
                'data' => [],
            ], 500);
        }

    }

    public function registerCategory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',

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

            $data = $request->only(['nombre', 'descripcion']);

            // Procesar imagen si llega
            if ($request->hasFile('icono')) {
                $imagen = $request->file('icono');
                $nombreArchivo = time() . '_' . preg_replace('/\s+/', '_', $imagen->getClientOriginalName());
                $imagen->move(public_path('servicios'), $nombreArchivo);
                $data['icono'] = url('servicios/' . $nombreArchivo);
            }

            $categoria = CategoriaServicio::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Categoría creada correctamente.',
                'data' => $categoria,
            ], 201);
        } catch (QueryException | PDOException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de conexión a la base de datos.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al crear categoría: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }


    public function showCategory($id)
    {
        try {
            $categoria = CategoriaServicio::findOrFail($id);

            if ($categoria->activo) {
                return response()->json([
                    'status' => true,
                    'message' => 'Categoría obtenida correctamente.',
                    'data' => $categoria,
                ]);
            }


        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Categoría no encontrada.',
                'data' => [],
            ], 404);
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
                'message' => 'Error al obtener categoría: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function updateCategory(Request $request, $id)
{
    try {
        $categoria = CategoriaServicio::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
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
        $data = $request->only(['nombre', 'descripcion']);
        if ($request->hasFile('icono')) {
            if ($categoria->icono && str_contains($categoria->icono, url('/servicios/'))) {
                $rutaAnterior = public_path('servicios/' . basename($categoria->icono));
                if (file_exists($rutaAnterior)) {
                    unlink($rutaAnterior);
                }
            }
            $imagen = $request->file('icono');
            $nombreArchivo = time() . '_' . preg_replace('/\s+/', '_', $imagen->getClientOriginalName());
            $imagen->move(public_path('servicios'), $nombreArchivo);
            $data['icono'] = url('servicios/' . $nombreArchivo);
        }
        $categoria->update($data);
        return response()->json([
            'status' => true,
            'message' => 'Categoría actualizada correctamente.',
            'data' => $categoria,
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
            'message' => 'Error al actualizar categoría: ' . $th->getMessage(),
            'data' => [],
        ], 500);
    }
}

    public function changeStatus($id)
    {
        try {
            //code...
            $categoria = CategoriaServicio::findOrFail($id);

            if (!$categoria) {
                return response()->json([
                    'status' => false,
                    'message' => 'Categoría no encontrada.',
                    'data' => [],
                ], 404);
            }

            $categoria->activo = !$categoria->activo;
            $categoria->save();

            return response()->json([
                'status' => true,
                'message' => 'Categoría actualizada correctamente.',
                'data' => $categoria,
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
                'message' => 'Error al actualizar categoría: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
