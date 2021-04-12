<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\Auth\UpdateStatusAccountNotification;
use App\Notifications\Auth\VerificationAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/auth/register",
     * summary="Register",
     * description="Register to member account",
     * operationId="",
     * tags={"auth"},
     * @OA\RequestBody(
     *      required=true,
     *      description="",
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="name",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="password_confirm",
     *                  type="string",
     *              )
     *          )
     *      )
     *   ),
     *   @OA\Response(
     *      response=422,
     *      description="Missing field parameter"
     *   ),
     *   @OA\Response(
     *      response=201,
     *      description="Successful operation"
     *   )
     * )
     * 
     * @param  Request  $request
     * @return Illuminate\Http\Resources\Json\Resource
     */
    public function register(Request $request)
    {
        $validate = $this->validateData($request->all(), 'register');
        if (!empty($validate) && $validate['status'] == 422) {
            return response()->json($validate, 422);
        }

        DB::beginTransaction();

        try {
            $codeUnique = mt_rand(100000,999999);

            $data = $request->all();
            $data['password'] = bcrypt($request->password);
            $data['number_verified'] = $codeUnique;

            $user = User::create($data);

            $user->notify(new VerificationAccount($user));

            DB::commit();

            return new UserResource($user);
        } catch (\Throwable $th) {
            DB::rollBack();

            $error['data'] = null;
            $error['status'] = 500;
            $error['message'] = $th->getMessage();
            $error['errors'] = null;

            return response()->json($error, 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/auth/verified",
     * summary="Verifified Account",
     * description="Verified account apps",
     * operationId="",
     * tags={"auth"},
     * @OA\RequestBody(
     *      required=true,
     *      description="",
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="numberUnique",
     *                  type="integer",
     *              )
     *          )
     *      )
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="Data not found"
     *   ),
     *   @OA\Response(
     *      response=201,
     *      description="Successful operation"
     *   )
     * )
     * 
     * @param  Request  $request
     * @return Illuminate\Http\Resources\Json\Resource
     */
    public function verified(Request $request)
    {
        $user = User::where([
            'number_verified' => $request->numberUnique,
            'email_verified_at' => null
        ])->first();

        if (!empty($user)) {
            $user->update([
                'email_verified_at' => now(),
                'number_verified' => 0,
                'is_active' => 1
            ]);

            $user->notify(new UpdateStatusAccountNotification($user));

            return new UserResource($user);
        } else {
            $error['data'] = null;
            $error['status'] = 404;
            $error['message'] = "Data not found.";
            $error['errors'] = null;

            return response()->json($error, 404);
        }
    }

    /**
     * @OA\Post(
     * path="/api/auth/login",
     * summary="Login in Account",
     * description="Login in account apps",
     * operationId="",
     * tags={"auth"},
     * @OA\RequestBody(
     *      required=true,
     *      description="",
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  type="string",
     *              )
     *          )
     *      )
     *   ),
     *   @OA\Response(
     *      response=422,
     *      description="Missing field parameter"
     *   ),
     *   @OA\Response(
     *      response=201,
     *      description="Successful operation"
     *   )
     * )
     * 
     * @param  Request  $request
     * @return Illuminate\Http\Resources\Json\Resource
     */
    public function login(Request $request)
    {
        $validate = $this->validateData($request->all(), 'login');

        if (!empty($validate) && $validate['status'] == 422) {
            return response()->json($validate, 422);
        }

        $credentials = request(['email', 'password']);
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'data' => 'null',
                'status' => 401,
                'message' => 'Unauthorized',
                'errors' => 'Unauthorized'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"auth"},
     *     summary="Logout for revoke token.",
     *     security={{"bearerAuth": {}}},
     *     description="",
     *     operationId="",
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="An authorization header is required after login",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Validation\ValidationException
     */

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'data' => null,
            'status' => 200,
            'message' => null,
            'errors' => null
        ], 200);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    private function validateData($request, $use)
    {
        if ($use == 'login') {
            $rules = [
                'email' => 'required|email|exists:users,email',
                'password' => 'required'
            ];
        } elseif ($use == 'register') {
            $rules = [
                'name' => 'required|max:200',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'password_confirm' => 'required|same:password'
            ];
        }

        $message = [
            'required' => 'The :attribute field is required.',
            'email' => 'We need to know your e-mail address!',
            'min' => 'The :attribute min :min',
            'same' => 'This :attribute not same',
            'max' => 'The :attribute max :max',
            'exists' => 'The :attribute must exists'
        ];

        $validator = Validator::make($request, $rules, $message);
        if ($validator->fails()) {
            $error['data'] = null;
            $error['status'] = 422;
            $error['message'] = $validator->messages()->first();
            $error['errors'] = null;

            return $error;
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
