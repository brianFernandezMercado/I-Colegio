<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Database\QueryException;
use PDOException;
class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
            ], [
                'username.required' => 'El nombre de usuario es obligatorio.',
                'password.required' => 'La contraseña es obligatoria.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors(),
                ], 422);
            }



            $credentials = $request->only('username', 'password');

            if (!$token = auth()->attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Credenciales incorrectas',
                    'token' => null,
                    'user' => [],
                ], 401);
            }

            $user = auth()->user();
            if ($user->estado == 'inactivo') {
                return response()->json([
                    'status' => false,
                    'message' => 'El usuario esta inactivo',
                    'token' => null,
                    'user' => [],
                ], 401);
            }
            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Inicio de sesión exitoso',
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
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
                'token' => null,
                'user' => [],
            ], 500);
        }
    }


    // Registrar
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|unique:users',
                'nombre_completo' => 'required|string',
                'username' => 'required|string|unique:users',
                'password' => 'required|string|confirmed',
                'celular' => 'required|string',
                'descripcion' => 'required|string',
                'calificacion' => 'required|numeric',
                'zona' => 'required|string',
                'experiencia' => 'required|string',
                'ci' => 'required|string',
                'estado' => 'required|string',
                'rol' => 'required|string',
            ], [
                'email.required' => 'El correo electrónica es requerido.',
                'email.unique' => 'El correo electrónica ya esta registrado.',
                'email.email' => 'El correo electrónica no es valido.',
                'nombre_completo.required' => 'El nombre completo es requerido.',
                'username.required' => 'El username es requerido.',
                'username.unique' => 'El username ya esta registrado.',
                'password.required' => 'La contraseña es requerida.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
                'celular.required' => 'El celular es requerido.',
                'descripcion.required' => 'La descripción es requerida.',
                'calificacion.required' => 'La calificación es requerida.',
                'zona.required' => 'La zona es requerida.',
                'experiencia.required' => 'La experiencia es requerida.',
                'ci.required' => 'El CI es requerido.',
                'estado.required' => 'El estado es requerido.',
                'rol.required' => 'El rol es requerido.',
            ]);

            //imagen
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $nombreArchivo = time() . '_' . $imagen->getClientOriginalName();
                $imagen->move(public_path('imagenes'), $nombreArchivo);

                $urlimage = url('imagenes/' . $nombreArchivo);

                // Agregar al request para usarlo luego con fill() u otros
                $request->merge(['imagen' => $urlimage]);
            }

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors(),
                    'data' => [],
                    'status' => false
                ], 422);
            }
            if (User::where('ci', $request->input('ci'))->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'El c.i ya existe',
                    'data' => [],
                ], 500);
            } else {

                $user = User::create([
                    'email' => $request->input('email'),
                    'nombre_completo' => $request->input('nombre_completo'),
                    'username' => $request->input('username'),
                    'password' => bcrypt($request->input('password')),
                    'celular' => $request->input('celular'),
                    'descripcion' => $request->input('descripcion'),
                    'calificacion' => $request->input('calificacion'),
                    'imagen' => $request->input('imagen'),
                    'zona' => $request->input('zona'),
                    'experiencia' => $request->input('experiencia'),
                    'ci' => $request->input('ci'),
                    'estado' => $request->input('estado'),
                    'rol' => $request->input('rol'),
                ]);

                $token = auth()->login($user);

                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'data' => $user,
                    'status' => true,
                    'message' => 'Usuario registrado correctamente'
                ]);
            }
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
                'message' => 'Error al registrar usuario: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }


    }

    // Obtener usuario actual
    public function me()
    {
        try {
            return response()->json([
                'status' => true,
                'message' => 'Usuario obtenido correctamente',
                'data' => auth()->user(),

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
                'message' => 'Error al obtener usuario: ',
                'data' => [],
            ], 500);
        }
    }

    // Cerrar sesión
    public function logout()
    {
        try {
            auth()->logout();
            return response()->json([
                'status' => true,
                'message' => 'Sesion cerrada correctamente',
                'data' => [],

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
                'message' => 'Error al cerrar sesión: ',
                'data' => [],
            ], 500);
        }
    }

    // Refrescar token
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    // Formato de respuesta con token
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function update(Request $request)
    {
        try {
            $user = auth()->user();
            if ($user->estado == 'inactivo') {
                return response()->json([
                    'status' => false,
                    'message' => 'El usuario esta inactivo',
                    'token' => null,
                    'user' => [],
                ], 401);
            }
            // Validar datos recibidos
            $validator = Validator::make($request->all(), [
                'nombre_completo' => 'sometimes|string|max:255',
                'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
                'celular' => 'sometimes|string|max:20',
                'descripcion' => 'sometimes|string',
                'imagen' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'zona' => 'sometimes|string|max:100',
                'experiencia' => 'sometimes|string',
                'ci' => 'sometimes|string|max:20',
                'estado' => 'sometimes|in:activo,inactivo',
                'password' => 'sometimes|string|min:6|confirmed', // para cambiar contraseña (se requiere campo password_confirmation)
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar campos que se enviaron
            $data = $request->only([
                'nombre_completo',
                'username',
                'celular',
                'descripcion',
                'zona',
                'experiencia',
                'ci',
                'estado'
            ]);
            if ($request->hasFile('imagen')) {
                if ($user->imagen && str_contains($user->imagen, url('/imagenes/'))) {
                    $rutaImagenAnterior = public_path('imagenes/' . basename($user->imagen));
                    if (file_exists($rutaImagenAnterior)) {
                        unlink($rutaImagenAnterior);
                    }
                }
                $imagen = $request->file('imagen');
                $nombreArchivo = time() . '_' . $imagen->getClientOriginalName();
                $imagen->move(public_path('imagenes'), $nombreArchivo);
                $data['imagen'] = url('imagenes/' . $nombreArchivo);
            }
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $user->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Usuario actualizado correctamente',
                'user' => $user
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
                'message' => 'Error al actualizar el usuario: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $user = auth()->user();

            $request->validate([
                'estado' => 'required|in:activo,inactivo',
            ], [
                'estado.required' => 'El estado es obligatorio.',
                'estado.in' => 'El estado debe ser activo o inactivo.',
            ]);

            $user->estado = $request->estado;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Estado actualizado correctamente.',
                'user' => $user,
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
                'message' => 'Error al actualizar el estado: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }


    public function getListUser()
    {
        try {
            $users = User::all()->makeHidden(['password']);
            return response()->json([
                'status' => true,
                'message' => 'Usuarios obtenidos correctamente',
                'data' => $users,
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
                'message' => 'Error al obtener usuarios: ' . $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
