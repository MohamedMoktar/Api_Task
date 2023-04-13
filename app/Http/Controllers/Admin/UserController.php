<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users =User::Where('isAdmin',0)->get();
        $data = [
            'users' => $users
        ];
      
        return view('admin.users.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['email_verified_at'] = Carbon::now();
        $data['password'] = Hash::make($request->password);
        User::create($data);
        return redirect()->route('users.index')->with([
            'message' => 'User Added Successfully',
            'alert' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user=User::where('id',$id);
        $data = [
            'user' => $user
        ];
        return view('admin.users.edit',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();
        $user=User::where('id',$id);

        $user->update($data);
        return redirect()->route('users.index')->with([
            'message' => 'User Updated Successfully',
            'alert' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if($user){
          
            $user->delete();
        
        return redirect()->route('users.index')->with([
            'message' => 'User Deleted Successfully',
            'alert' => 'danger'
        ]);
    }
    return redirect()->route('dashbord')->with(['message'=> 'Wrong ID!!']);
    }
}
