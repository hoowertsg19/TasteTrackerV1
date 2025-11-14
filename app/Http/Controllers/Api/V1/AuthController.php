<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Auth", description="Autenticación con Sanctum")
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/v1/auth/register",
     *   tags={"Auth"},
     *   summary="Registrar un nuevo usuario",
     *   @OA\RequestBody(required=true, @OA\JsonContent(
     *     required={"name","email","password"},
     *     @OA\Property(property="name", type="string", example="Admin"),
     *     @OA\Property(property="email", type="string", example="admin@example.com"),
     *     @OA\Property(property="password", type="string", example="secret123")
     *   )),
     *   @OA\Response(response=201, description="Registrado"),
     *   @OA\Response(response=422, description="Datos inválidos")
     * )
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return response()->json(['message' => 'Registrado', 'user' => $user], Response::HTTP_CREATED);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/auth/login",
     *   tags={"Auth"},
     *   summary="Obtener un token de acceso",
     *   @OA\RequestBody(required=true, @OA\JsonContent(
     *     required={"email","password"},
     *     @OA\Property(property="email", type="string", example="admin@example.com"),
     *     @OA\Property(property="password", type="string", example="secret123")
     *   )),
     *   @OA\Response(response=200, description="Autenticado"),
     *   @OA\Response(response=422, description="Credenciales inválidas")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json(['token' => $token], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *   path="/api/v1/auth/me",
     *   tags={"Auth"},
     *   summary="Obtener usuario autenticado",
     *   @OA\Response(response=200, description="OK"),
     *   security={{{"sanctum":{}}}}
     * )
     */
    public function me(Request $request)
    {
        return response()->json($request->user(), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/auth/logout",
     *   tags={"Auth"},
     *   summary="Revocar el token actual",
     *   @OA\Response(response=204, description="Sin contenido"),
     *   security={{{"sanctum":{}}}}
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Verificar si un email está registrado (endpoint público, sin auth).
     */
    public function checkEmail(Request $request)
    {
        try {
            $rawEmail = $request->query('email');

            if ($rawEmail === null || trim($rawEmail) === '') {
                return response()->json([
                    'exists' => false,
                    'message' => 'Email no proporcionado',
                ], 400);
            }

            $email = trim(mb_strtolower($rawEmail));

            // Validación básica de formato (opcional pero útil)
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'exists' => false,
                    'message' => 'Email no proporcionado',
                ], 400);
            }

            $exists = User::whereRaw('LOWER(email) = ?', [$email])->exists();

            Log::info('Auth check-email', [
                'email' => $email,
                'exists' => $exists,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'exists' => $exists,
                'message' => $exists ? 'Email registrado' : 'Email no registrado',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Auth check-email error', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'exists' => false,
                'message' => 'Error del servidor',
            ], 500);
        }
    }
}
