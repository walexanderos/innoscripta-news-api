<?php
namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Authentication & Authorization",
 *     description=""
 * )
 *
 */
class AuthController extends Controller
{
    use ApiResponse;
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new user",
     *     tags={"Authentication & Authorization"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="password_confirmation", type="string"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registration successful"),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(response="400", description="Bad request")
     * )
     *
     */
    public function register(Request $request)
    {
        $response = $this->authService->registerUser($request->all());

        if (isset($response['error'])) {
            return $this->message_error("Bad request", 400, $response['error']);
        }

        return $this->message_success($response, "Registration successful", 201);
    }

    /**
     * Login the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * * @OA\Post(
     *     path="/api/auth/login",
     *     summary="User Login",
     *     tags={"Authentication & Authorization"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *         )
     *     ),
     *
     * @OA\Response(
     *         response=209,
     *         description="Login successful",
     *     @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged in successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="access_token", type="string"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer")
     *             )
     *         ),
     *      ),
     *     @OA\Response(response="401", description="Bad request")
     * )
     *
     *
     *
     */
    public function login(Request $request)
    {
        $response = $this->authService->loginUser($request->only('email', 'password'));

        if (isset($response['error'])) {
            return $this->message_error("Bad request", 401, $response['error']);
        }

        return $this->message_success($response, "Logged in successfully", 200);
    }

    /**
     * Logout the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     *  @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="User Logout",
     *     tags={"Authentication & Authorization"},
     *     security={{"BearerToken":{}}},
     *     @OA\Response(response="200", description="Logged out successfully")
     * )
     */
    public function logout(Request $request)
    {
        $this->authService->logoutUser($request->user());
        return $this->message_success(null, "Logged out successfully");
    }
}
