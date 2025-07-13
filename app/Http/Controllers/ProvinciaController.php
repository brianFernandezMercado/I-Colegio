<?php

namespace App\Http\Controllers;
use App\Models\Departamento;
use PDOException;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Models\Provincia;


class ProvinciaController extends Controller
{
    public function getListProvincia()
    {
        try {
            return response()->json([
                'status' => true,
                'data' => Provincia::with('departamento')->get(),
                'message' => 'provincias obtenidas correctamente',
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

    public function registerProvincia(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string',
                'descripcion' => 'string',
                'icono' => 'string',
                'departamento_id' => 'required|exists:categorias_servicios,id',
            ], [
                'nombre.required' => 'El nombre es obligatorio.',
                'departamento_id.required' => 'La provincia es obligatoria.',
                'departamento_id.exists' => 'La provincia no existe.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $sub = Departamento::where('id', $request->departamento_id)->where('activo', 0)->first();
            if ($sub) {
                return response()->json([
                    'status' => false,
                    'message' => 'El departamento esta inactivo.',
                    'data' => [],
                ], 409);
            }


            $sub = Provincia::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Provincia creado correctamente.',
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

    public function showProvincia($id)
    {
        try {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:provincias,id',
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
                'message' => 'Provincia obtenido correctamente.',
                'data' => Provincia::with('departamento')->findOrFail($id),
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

    public function updateProvincia(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string',
                'descripcion' => 'string',
                'icono' => 'string',
                'departamento_id' => 'required|exists:departamentos,id',
            ], [
                'nombre.required' => 'El nombre es obligatorio.',
                'departamento_id.required' => 'La departamento es obligatoria.',
                'departamento_id.exists' => 'La departamento no existe.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }



            $sub = Provincia::findOrFail($id);
            $sub->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Provincia actualizado correctamente.',
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
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function changeStatus($id)
    {
        try {

            $sub = Provincia::findOrFail($id);
            $sub->activo = !$sub->activo;
            $sub->save();
            return response()->json([
                'status' => true,
                'message' => 'Provincia actualizado correctamente.',
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
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
