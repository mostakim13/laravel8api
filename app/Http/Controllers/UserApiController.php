<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;

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
        $header = $request->header('Authorization');
        if ($header == '') {
            $message = "Authorization is required";
            return response()->json(["message" => $message], 422);
        }
        if ($header == "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6Im1vc3Rha2ltIiwiaWF0IjoxNTE2MjM5MDIyfQ.SqQfMTL20pi7kmWoHCEyAdBDW_wYqdYYevGEmnyk0Uk") {
            if ($request->isMethod('delete')) {
                $data = $request->all();

                User::whereIn('id', $data['ids'])->delete();

                $message = "User deleted successfully!";
                return response()->json(["message" => $message], 200);
            }
        } else {
            $message = "Authorization does not match";
            return response()->json(["message" => $message]);
        }
    }

    //register Using Passport
    public function registerUserUsingPassport(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            $rules = [
                "name" => "required",
                "email" => "required|email|unique:users",
                "password" => "required"
            ];
            $customMessage = [
                "name.required" => "Name is required",
                "email.required" => "Email is required",
                "email.email" => "Email must be valid",
                "password.required" => "Password is required"
            ];
            $validator = validator::make($data, $rules, $customMessage);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();

            if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
                $user = User::where('email', $data['email'])->first();
                $access_token = $user->createToken($data['email'])->accessToken;
                User::where('email', $data['email'])->update(['access_token' => $access_token]);
                $message = "User successfully registered";
                return response()->json(['message' => $message, 'access_token' => $access_token], 201);
            } else {
                $message = "Oops! Something went wrong";
                return response()->json(['message' => $message], 422);
            }
        }
    }
}