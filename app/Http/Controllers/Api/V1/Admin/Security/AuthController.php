<?php
namespace App\Http\Controllers\Api\V1\Admin\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\Admin\Security\AuthenticationRequest;
use App\Models\User;
use App\Http\Resources\Api\V1\Admin\Security\PermissionResource;
use App\Http\Resources\Api\V1\Admin\Security\User\UserIndexResource;
use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(AuthenticationRequest $request)
    {
        try {
            $user = User::where('username', $request->username)
                ->where('status', 'ACTIVE')
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect username or password.',
                ], 401);
            }

            $data = $this->getTokenAndRefreshToken($user);

            UserLogin::create([
                'type' => 'Login',
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'browser' => request()->header('User-Agent'),
                'login_at' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Login successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Login failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'username' => $request->username
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Login failed. Please try again.'
            ], 500);
        }
    }

    public function getTokenAndRefreshToken($user)
    {
        $request = request();

        $requestIdentity = 'User: ' . $user->username .
            ', Device: ' . $request->header('User-Agent') .
            ', IP: ' . $request->ip() .
            ', Timestamp: ' . Carbon::now()->toIso8601String();

        $expireSeconds = (int) env('SANCTUM_TOKEN_EXPIRATION', 7200);
        $refreshTokenExpiration = (int) env('SANCTUM_REFRESH_EXPIRATION', 604800); // Default 7 days

        // Generate refresh token
        $refreshToken = Str::random(64);

        $refreshTokenId = Str::uuid();

        // Store the refresh token in the database with expiration date and revoked flag
        DB::table('refresh_tokens')->insert([
            'id' => $refreshTokenId,
            'user_id' => $user->id,
            'name' => $requestIdentity,
            'token' => hash('sha256', $refreshToken), // Securely store the hashed token
            'expires_at' => Carbon::now()->addSeconds($refreshTokenExpiration),
            'revoked' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Create main access token
        $token = $user->createToken($refreshTokenId, ['admin'], now()->addSeconds($expireSeconds));

        return [
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => $expireSeconds,
            'refresh_token' => $refreshToken,
        ];
    }

    public function logout()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated.',
                ], 401);
            }

            // Get current token
            $currentToken = auth()->user()->currentAccessToken();
            if (!$currentToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated.',
                ], 401);
            }

            // Begin transaction
            DB::beginTransaction();
            try {
                // Revoke (not delete) the refresh token
                DB::table('refresh_tokens')
                    ->where('id', $currentToken->name)
                    ->update([
                        'revoked' => true,
                        'updated_at' => Carbon::now()
                    ]);

                // Delete the current access token
                $currentToken->delete();

                // Log the logout
                UserLogin::create([
                    'type' => 'Logout',
                    'user_id' => $user->id,
                    'ip_address' => request()->ip(),
                    'browser' => request()->header('User-Agent'),
                    'logout_at' => Carbon::now(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Logout successfully.',
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Logout failed: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Logout failed. Please try again.'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Logout failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Logout failed. Please try again.'
            ], 500);
        }
    }

    public function refreshToken(Request $request)
    {
        if (!$request->refresh_token) {
            return response()->json([
                'success' => false,
                'message' => 'Refresh token is required.'
            ], 400);
        }

        $refreshTokenRecord = DB::table('refresh_tokens')
            ->where('token', hash('sha256', $request->refresh_token))
            ->where('revoked', false)
            ->first();

        if (!$refreshTokenRecord || Carbon::now()->greaterThan($refreshTokenRecord->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired refresh token.'
            ], 401);
        }

        // Get the user and check status
        $user = User::where('id', $refreshTokenRecord->user_id)
            ->where('status', 'ACTIVE')
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive user.'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Delete old access token associated with this refresh token
            DB::table('personal_access_tokens')
                ->where('name', $refreshTokenRecord->id)
                ->delete();

            $expireSeconds = (int) env('SANCTUM_TOKEN_EXPIRATION', 7200);

            // Create new access token using same refresh token ID
            $token = $user->createToken($refreshTokenRecord->id, ['admin'], now()->addSeconds($expireSeconds));

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'access_token' => $token->plainTextToken,
                    'token_type' => 'Bearer',
                    'expires_in' => $expireSeconds,
                    'refresh_token' => $request->refresh_token, // Return same refresh token
                ],
                'message' => 'Refresh token successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Token refresh failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed. Please try again.'
            ], 500);
        }
    }

    public function info()
    {
        $user = auth()->user();
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }

        $permissions = [];
        $role = $user->role;
        $permissions = $role->permissions;

        return response()->json([
            'user' => new UserIndexResource($user),
            'permissions' =>  PermissionResource::collection($permissions),
        ]);
    }

    public function getUser()
    {
        $user = auth()->user();

        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => new UserIndexResource($user),
        ]);
    }

    public function checkUser()
    {

        $user = auth()->user();

        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}
