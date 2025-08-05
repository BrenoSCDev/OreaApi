<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(['users' => User::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'lastname' => 'required|string',
            'phone' => 'required|string',
            'company' => 'required|string',
            'email' => 'required|email|unique:users,email',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->phone = $request->phone;
        $user->company = $request->company;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->password = Hash::make("@Orea_PWD_USER2121");
        $user->save();

        return response()->json(['message' => 'Usuário criado com sucesso!', 'user' => $user], 201);
    }

    public function edit(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'lastname' => 'required|string',
        ]);

        $user = User::find($request->user);
        $user->name = $request->name;
        $user->lastname = $request->lastname;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::random(40) . '.' . $extension;
            $path = $file->storeAs('users', $filename, 'public');
            $user->foto = $path;
        }

        $user->save();

        return response()->json(['message' => 'Usuário editado com sucesso!', 'user' => $user], 201);
    }
}
