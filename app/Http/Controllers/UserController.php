<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Name_User' => 'required|string|max:255',
            'Username_User' => 'required|string|max:255|unique:users,Username_User',
            'Password_User' => 'required|string|min:6',
        ]);

        User::create([
            'Name_User' => $request->Name_User,
            'Username_User' => $request->Username_User,
            'Password_User' => Hash::make($request->Password_User),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'Name_User' => 'required|string|max:255',
            'Username_User' => 'required|string|max:255|unique:users,Username_User,' . $id . ',Id_User',
        ];

        if ($request->filled('Password_User')) {
            $rules['Password_User'] = 'string|min:6';
        }

        $request->validate($rules);

        $user->Name_User = $request->Name_User;
        $user->Username_User = $request->Username_User;
        
        if ($request->filled('Password_User')) {
            $user->Password_User = Hash::make($request->Password_User);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
