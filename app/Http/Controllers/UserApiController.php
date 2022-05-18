<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserApiController extends Controller
{

    //fetch data
    public function showUser($id = null)
    {
        if ($id == '') {
            $users = User::get();
            return response()->json(['users' => $users], 200);
        } else {
            $users = User::findOrFail($id);
            return response()->json(['users' => $users], 200);
        }
    }


    //add single user
    public function addUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                "name" => "required",
                "email" => "required|email|unique:users",
                "password" => "required",
            ];

            $customMessage = [
                "name.required" => "Name is required",
                "email.required" => "Email is required",
                "email.email" => "Email must be valid",
                "password.required" => "Password is required",
            ];
            $validator = Validator::make($data, $rules, $customMessage);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();
            $message = 'User successfully added!';
            return response()->json(['message' => $message], 201);
        }
    }

    //multiple user add
    public function addMultipleUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                "users.*.name" => "required",
                "users.*.email" => "required|email|unique:users",
                "users.*.password" => "required",
            ];

            $customMessage = [
                "users.*.name.required" => "Name is required",
                "users.*.email.required" => "Email is required",
                "users.*.email.email" => "Email must be valid",
                "users.*.password.required" => "Password is required",
            ];
            $validator = Validator::make($data, $rules, $customMessage);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            foreach ($data['users'] as $users) {
                $user = new User();
                $user->name = $users['name'];
                $user->email = $users['email'];
                $user->password = bcrypt($data['password']);
                $user->save();
                $message = "Multiple user added successfully!";
            }
            return response()->json(["message" => $message], 201);
        }
    }

    //update user
    public function updateUser(Request $request, $id)
    {
        if ($request->isMethod("put")) {
            $data = $request->all();

            $rules = [
                "name" => "required",
                "password" => "required"
            ];
            $customMessage = [
                "name" => "Name is required",
                "password" => "Password is required"
            ];

            $validator = Validator::make($data, $rules, $customMessage);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $user = User::findOrFail($id);
            $user->name = $data['name'];
            $user->password = bcrypt($data['password']);
            $user->save();

            $message = "User updated successfully!";
            return response()->json(["message" => $message]);
        }
    }

    //update single record
    public function updateSingleRecord(Request $request, $id)
    {
        if ($request->isMethod("patch")) {
            $data = $request->all();

            $rules = [
                "name" => "required"
            ];
            $customMessage = [
                "name.required" => "Name is required"
            ];
            $validator = Validator::make($data, $rules, $customMessage);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user = User::findOrFail($id);
            $user->name = $data['name'];
            $user->save();

            $message = "Singe record updated successfully!";
            return response()->json(["message" => $message], 202);
        }
    }

    //delete single user
    public function deleteSingleUser($id)
    {
        User::findOrFail($id)->delete();
        $message = "User deleted successfully!";
        return response()->json(["message" => $message], 200);
    }

    //delete single user with json
    public function deleteSingleUserJson(Request $request)
    {
        if ($request->isMethod('delete')) {
            $data = $request->all();
            User::where('id', $data['id'])->delete();

            $message = "User deleted successfully!";
            return response()->json(["message" => $message], 200);
        }
    }

    //delete multiple user
    public function deleteMultipleUser($ids)
    {
        $ids = explode(',', $ids);
        User::whereIn('id', $ids)->delete();

        $message = "User deleted successfully!";
        return response()->json(["message" => $message], 200);
    }

    //delete multiple users with json
    public function deleteMultipleUserJson(Request $request)
    {
        if ($request->isMethod('delete')) {
            $data = $request->all();

            User::whereIn('id', $data['ids'])->delete();

            $message = "User deleted successfully!";
            return response()->json(["message" => $message], 200);
        }
    }
}