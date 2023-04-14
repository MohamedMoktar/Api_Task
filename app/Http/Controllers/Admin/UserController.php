<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get all users who is not admin
        $users = User::Where('isAdmin',0)->get();
        return response()->json([
            'message' => 'Ok',
            'status' => Response::HTTP_OK,
            'data' => UserResource::collection($users)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $request['password']=Hash::make($request->password);
        $user = User::create(
            $request->all(),
          );
      
        return response()->json([
            'message' => 'Created',
            'status' => Response::HTTP_CREATED,
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json([
                'message' => 'Ok',
                'status' => Response::HTTP_OK,
                'data' => new UserResource($user)
            ]);
        }else {
            return response()->json([
                'message' => 'Not Found',
                'status' => Response::HTTP_NOT_FOUND
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        $request['password']=Hash::make($request->password);
        $user = User::find($id);

        if ($user) {
                $user->update($request->all());
                return response()->json([
                    'message' => 'Updated',
                    'status' => Response::HTTP_NO_CONTENT
                ]);
            }
          
        else {
            return response()->json([
                'message' => 'Not Found',
                'status' => Response::HTTP_NOT_FOUND
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = user::find($id);
        if ($user) {
            $user->delete();
            return response()->json([
                'message' => 'Deleted',
                'status' => Response::HTTP_NO_CONTENT
            ]);
        } else {
            return response()->json([
                'message' => 'Not Found',
                'status' => Response::HTTP_NOT_FOUND
            ]);
        }
    }
}
