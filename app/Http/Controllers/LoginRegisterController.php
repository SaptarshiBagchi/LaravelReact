<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginRegisterController extends Controller
{
    /**
     * Used md5 but could have used Hash facade as well
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'password' => 'required',
            'email' => 'required|email|exists:users,email',


        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }
        //the validator has not failed

        //check if user exists
        $user = User::where('email', $request->email)->where('password', md5($request->password))->first();
        if (empty($user)) {
            return response()->json([
                'error' => 'Invalid Credentials'
            ], 403);
        }
        return response()->json([
            'error' => null,
            'token' => $user->createToken($request->email)->plainTextToken
        ], 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'password' => 'required',
            'email' => 'required|email',


        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        //check if the user is unique
        $user  = User::where('email', $request->email)->first();
        if (!empty($user)) {
            return response()->json([
                'error' => 'Email already in use'
            ], 400);
        }

        //validation has succeeded and use does not exist
        $user = new User();
        $user->name = $request->name;
        $user->name_search = strtoupper($request->name);
        $user->email = $request->email;

        $user->password = md5($request->password);
        $user->save();

        return response()->json(['error' => null, 'message' => 'Account created successful! Please login and save your token'], 200);
    }
}
