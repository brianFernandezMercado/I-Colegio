<?php

namespace App\Http\Controllers;


use App\Models\SubCategoriaSeleccionadas;
use PDOException;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class SubCategoriasSeleccionadasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getListSeleccionadas()
    {
        try {
              return response()->json([
                  'status' => true,
                  'data' => SubCategoriaSeleccionadas::all(),
                  'message' => 'Categorias obtenidas correctamente',

              ]);
        } catch (QueryException | PDOException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de conexión a la base de datos.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
                'token' => null,
                'user' => [],
            ]);
        }
         catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener categorias: '. $th->getMessage(),
                'data' => [],
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function registerSeleccionadas(Request $request)
    {
         try {

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'sub_servicio_id' => 'required|exists:sub_servicios,id',
            ], [
                'user_id.required' => 'El id es obligatorio.',
                'user_id.exists' => 'El id no existe.',
                'sub_servicio_id.required' => 'El id es obligatorio.',
                'sub_servicio_id.exists' => 'El id no existe.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $sub = SubCategoriaSeleccionadas::where('user_id', $request->user_id)->where('sub_servicio_id', $request->sub_servicio_id)->first();
            if ($sub) {
                return response()->json([
                    'status' => false,
                    'message' => 'La categoria ya esta seleccionada.',
                    'data' => [],
                ], 409);
            }

            $sub = new SubCategoriaSeleccionadas();
            $sub->user_id = $request->user_id;
            $sub->sub_servicio_id = $request->sub_servicio_id;
            $sub->save();

            return response()->json([
                'status' => true,
                'message' => 'Categoria seleccionada correctamente.',
                'data' => $sub,
            ], 200);

         } catch (QueryException | PDOException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error de conexión a la base de datos.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null,
                'token' => null,
                'user' => [],
            ], 500);
         }
         catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
         }
    }

    /**
     * Display the specified resource.
     */
    public function showSeleccionadas(string $id)
    {
            try {
                 $data = SubCategoriaSeleccionadas::where('id', $id)->with('user', 'subServicio')->get();
                 return response()->json([
                     'status' => true,
                     'data' => $data,
                     'message' => 'Categorias obtenidas correctamente',
                 ]);
            }
             catch (QueryException | PDOException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de conexión a la base de datos.',
                    'error' => env('APP_DEBUG') ? $e->getMessage() : null,
                    'token' => null,
                    'user' => [],
                ], 500);
            }
             catch (\Throwable $th) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error al obtener categorias: '. $th->getMessage(),
                    'data' => [],
                ], 500);
            }
    }

    /**
     * Update the specified resource in storage.
     */
  public function updateSeleccionadas(Request $request, string $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'sub_servicio_id' => 'required|exists:sub_servicios,id',
        ], [
            'user_id.required' => 'El id de usuario es obligatorio.',
            'user_id.exists' => 'El usuario no existe.',
            'sub_servicio_id.required' => 'El subservicio es obligatorio.',
            'sub_servicio_id.exists' => 'El subservicio no existe.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $registro = SubCategoriaSeleccionadas::find($id);

        if (!$registro) {
            return response()->json([
                'status' => false,
                'message' => 'Registro no encontrado.',
                'data' => [],
            ], 404);
        }

        $registro->user_id = $request->user_id;
        $registro->sub_servicio_id = $request->sub_servicio_id;
        $registro->save();

        return response()->json([
            'status' => true,
            'message' => 'Categoría actualizada correctamente.',
            'data' => $registro,
        ], 200);

    } catch (QueryException | PDOException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error de conexión a la base de datos.',
            'error' => env('APP_DEBUG') ? $e->getMessage() : null,
            'data' => [],
        ], 500);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage(),
            'data' => [],
        ], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function changeStatus(string $id)
    {
        try {
            $sub = SubCategoriaSeleccionadas::findOrFail($id);
            $sub->activo = !$sub->activo;
            $sub->save();
            return response()->json([
                'status' => true,
                'message' => 'Categoría actualizada correctamente.',
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
