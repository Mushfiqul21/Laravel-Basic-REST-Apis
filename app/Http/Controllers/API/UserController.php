<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function create(Request $request){
        try {
            $data = Validator::make($request->all(),[
                'name' => 'required|string',
                'phone' => 'required|numeric',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);
            if($data->fails()){
                $result = ['status' => False, 'message' => 'Validation Failed', 'errors' => $data->errors()];
                return response()->json($result, 400);
            }
            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
            return response()->json(['status' => True, 'message' => 'User Created successfully', 'data' => $user]);
        }catch(\Exception $e){
            $result = ['status' => False, 'message' => $e->getMessage(), 'errors' => $e->errors()];
            return response()->json([$result], 400);
        }

    }

    public function show(){
        $users = User::all();
        $result = ['status' => True, 'message' => Count($users). ' Users Data Fetched', 'data' => $users];
        return response()->json($result, 200);
    }

    public function view($id){
        $user = User::find($id);
        if(!$user){
            $result = ['status' => False, 'message' => 'User Not Found'];
            return response()->json($result, 404);
        }
        $result = ['status' => True, 'message' => 'User Found', 'data' => $user];
        return response()->json($result, 200);
    }

    public function update(Request $request, $id){
        $user = User::find($id);
        if(!$user){
            $result = ['status' => False, 'message' => 'User Not Found'];
            return response()->json($result, 404);
        }
        $data = Validator::make($request->all(),[
            'name' => 'required|string',
        ]);
        if($data->fails()){
            $result = ['status' => False, 'message' => 'Validation Failed', 'errors' => $data->errors()];
            return response()->json($result, 400);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();
        return response()->json(['status' => True, 'message' => 'User Updated successfully', 'data' => $user]);
    }

    public function delete($id){
        $user = User::find($id);
        if(!$user){
            $result = ['status' => False, 'message' => 'User Not Found'];
            return response()->json($result, 404);
        }
        $user->delete();
        $result = ['status' => True, 'message' => 'User has been Deleted'];
        return response()->json($result, 200);
    }
}
