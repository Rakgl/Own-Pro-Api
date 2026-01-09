<?php

namespace App\Http\Controllers\Api\V1\Admin\Security;

use App\Helpers\AppHelper;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\V1\Admin\Security\ChangePasswordRequest;
use App\Http\Requests\Api\V1\Admin\Security\UserRequest;
use App\Http\Resources\Api\V1\Admin\AuditResource;
use App\Http\Resources\Api\V1\Admin\Security\User\UserEditResource;
use App\Http\Resources\Api\V1\Admin\Security\User\UserIndexResource;
use App\Http\Resources\Api\V1\Admin\Security\User\UserLoginResource;
use App\Http\Resources\Api\V1\Admin\Security\User\UserShowResource;
use App\Models\IdTag;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $data = User::where(function ($q) use ($request) {
            if ($request->search) {
				$q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->search) . '%']);
            }

            if ($request->name) {
                $q->where('name', 'like', '%' . $request->name . '%');
            }

            if ($request->status) {
                $q->where('status', $request->status);
            }

        })
            ->exceptRoot()
            ->exceptCurrentUser()
            ->notDelete()->orderBy('created_at', 'desc')
            ->paginate($request->per_page);

        $resource = UserIndexResource::collection($data)->response()->getData(true);

        return response()->json([
            'data' => $resource['data'],
            'meta' => [
                'current_page' => $resource['meta']['current_page'],
                'last_page' => $resource['meta']['last_page'],
                'total' => $resource['meta']['total'],
            ]
        ]);
    }

    public function store(UserRequest $request)
    {
        $findDeletedUser = User::where('username', $request->username)
						->where('status', 'DELETED')
                        ->first();

		$role = Role::where('id', $request->role_id)->first();
		$path = 'uploads/images';
        $imagePath = $request->hasFile('image') 
					? AppHelper::uploadImage($request->file('image'), $path) 
					: null;
		if(!$role) {
			return response()->json([
				'success' => false,
				'message' => 'Role not found.'
			]);
		}
		$userId = Helper::getLoginUserId();
        if($findDeletedUser) {
            $findDeletedUser->update([
                'name' => $request->name,
                'email' => $request->email,
				'image' => $imagePath,
                'password' => Hash::make($request->password),
                'status' => $request->status,
				'role_id' => $request->role_id,
				'locale' => $request->locale ? $request->locale : 'en',
				'updated_by' => $userId
            ]);
	
            return response()->json([
                'success' => true,
                'message' => "User have been created."
            ]);
        }

		User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
			'image' => $imagePath,
            'password' => Hash::make($request->password),
			'role_id' => $request->role_id,
			'locale' => $request->locale ? $request->locale : 'en',
			'created_by' => $userId
        ]);

		return response()->json([
            'success' => true,
            'message' => "User have been created."
        ]);
    }

	public function edit($id) 
	{
		$user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "User not found."
            ]);
        }
        return response()->json([
            'data' => new UserEditResource($user),
        ]);
	}
    // Get user by id
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "User not found."
            ]);
        }
		$userLogins = $user->logins()->orderBy('created_at', 'desc')->limit(15)->get();
        return response()->json([
            'data' => new UserShowResource($user),
			'items' => UserLoginResource::collection($userLogins)
        ]);
    }

    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            $status["success"] = false;
            $status["message"] = "User not found.";
            return response()->json($status);
        }

		$role = Role::where('id', $request->role_id)->first();
		if(!$role) {
			return response()->json([
				'success' => false,
				'message' => 'Role not found.'
			]);
		}
		$userId = auth()->user()->id;

		$imagePath = Helper::updateImage($request, $user);
		
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'status' => $request->status,
			'role_id' => $request->role_id,
			'locale' => $request->locale ? $request->locale : $user->locale,
			'updated_by' => $userId,
			'update_num' => $user->update_num + 1,
			'image' => $imagePath,
        ]);

		if($user->status == 'INACTIVE') {
			$user->tokens()->delete();
		}

        return response()->json([
            'success' => true,
            'message' => "User have been updated.",
            'data' =>  $this->show($user->id)->getData(true),
        ]);
    }

    public function updateProfile(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            $status["success"] = false;
            $status["message"] = "User not found.";
            return response()->json($status);
        }

		$imagePath = Helper::updateImage($request, $user);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'username' => $request->username,
            'gender' => $request->gender,
			'image' => $imagePath,
			'avatar_fallback_color' => $request->avatar_fallback_color,
			'language' => $request->language,
        ]);

        return response()->json([
            'success' => true,
            'message' => "User have been updated.",
            'data' =>  $this->show($user->id)->getData(true),
        ]);
    }

	public function changePassword(ChangePasswordRequest $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "User not found."
            ], 404);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The current password is not correct.',
                'errors' => [
                    'current_password' => ['The current password is not correct.'],
                ],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'success' => true,
            'message' => 'Password has been updated successfully.',
            'data' => new UserIndexResource($user)
        ]);
    }


    // Delete user by id
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            $status["success"] = false;
            $status["message"] = "User not found.";
            return response()->json($status);
        }

        $user->update([
            'status' => Helper::deleted(),
			'updated_by' => auth()->user()->id,
			'update_num' => $user->update_num + 1
        ]);

        return response()->json([
            'success' => true,
            'message' =>  "User have been deleted."
        ]);
    }

    public function uploadProfile(Request $request, $userId)
	{ 
        $user = User::find($userId);

        if (!$user) {
			return response()->json([
				'success' => false,
				'message' => 'User not found'
			]);
        }

		$path = 'uploads/images';
        $imagePath = $request->hasFile('image') ? null : $request->image;
        if ($request->hasFile('image')) {
            $imagePath = AppHelper::uploadImage($request->file('image'), $path);
            Log::info('Uploaded new image: ' . $imagePath);
        }

        $user->update([
            'image' => $imagePath
        ]);

        return response()->json([
            'success' => true,
            'message' =>  "Profile have been uploaded.",
        ]);
    }

    public function removeProfile($userId) {
        $user = User::find($userId);

        if (!$user) {
			return response()->json([
				'success' => false,
				'message' => 'User not found'
			]);
        }

        $user->update([
            'image' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' =>  "Profile have been deleted.",
        ]);
    }

	public function audit($id)
	{
		$user = User::find($id);

		if (!$user) {
			return response()->json([
				'success' => false,
				'message' => "User not found."
			]);
		}
		$audits = $user->audits()->with('user')->latest()->paginate(10);

		return response()->json([
			'success' => true,
			'items' => AuditResource::collection($audits)
		]);
	}
}
