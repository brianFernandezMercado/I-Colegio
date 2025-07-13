<?php

namespace App\Http\Controllers;

use App\Models\AgendaUsuario;
use PDOException;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class AgendaUsuarioController extends Controller
{
    public function getListAgenda()
    {
        try {
            return response()->json([
                'status' => true,
                'data' => AgendaUsuario::with('usuario')->get(),
                'message' => 'Agenda obtenida correctamente',

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
                'message' => 'Error al obtener agenda: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }
    public function registerAgenda(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'fecha' => 'required|date',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i',
                'estado' => 'required|in:disponible,reservado',
            ], [
                'user_id.required' => 'El id de usuario es obligatorio.',
                'user_id.exists' => 'El usuario no existe.',
                'fecha.required' => 'La fecha es obligatoria.',
                'fecha.date' => 'La fecha debe ser una fecha válida.',
                'hora_inicio.required' => 'La hora de inicio es obligatoria.',
                'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
                'hora_fin.required' => 'La hora de fin es obligatoria.',
                'hora_fin.date_format' => 'La hora de fin debe tener el formato HH:MM.',
                'estado.required' => 'El estado es obligatorio.',
                'estado.in' => 'El estado debe ser "disponible" o "reservado".',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $agenda = AgendaUsuario::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Agenda creada correctamente.',
                'data' => $agenda,
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
                'message' => 'Error al crear agenda: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function showAgenda($id)
    {
        try {
            $agenda = AgendaUsuario::with('usuario')->findOrFail($id);

            if (!$agenda) {
                return response()->json([
                    'status' => false,
                    'message' => 'Agenda no encontrada.',
                    'data' => [],
                ], 404);
            }
            return response()->json([
                'status' => true,
                'data' => $agenda,
                'message' => 'Agenda obtenida correctamente',
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
                'message' => 'Error al obtener agenda: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function updateAgenda(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'fecha' => 'required|date',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i',
                'estado' => 'required|in:disponible,reservado',
            ], [
                'user_id.required' => 'El id de usuario es obligatorio.',
                'user_id.exists' => 'El usuario no existe.',
                'fecha.required' => 'La fecha es obligatoria.',
                'fecha.date' => 'La fecha debe ser una fecha válida.',
                'hora_inicio.required' => 'La hora de inicio es obligatoria.',
                'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
                'hora_fin.required' => 'La hora de fin es obligatoria.',
                'hora_fin.date_format' => 'La hora de fin debe tener el formato HH:MM.',
                'estado.required' => 'El estado es obligatorio.',
                'estado.in' => 'El estado debe ser "disponible" o "reservado".',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $agenda = AgendaUsuario::findOrFail($id);
            $agenda->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Agenda actualizada correctamente.',
                'data' => $agenda,
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
                'message' => 'Error al actualizar agenda: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function changeStatus($id)
    {
        try {
            $agenda = AgendaUsuario::findOrFail($id);
            $agenda->activo = !$agenda->activo;
            $agenda->save();
            return response()->json([
                'status' => true,
                'message' => 'Agenda actualizada correctamente.',
                'data' => $agenda,
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
                'message' => 'Error al actualizar agenda: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
