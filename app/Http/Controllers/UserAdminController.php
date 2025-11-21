<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    // Pantalla intermedia: elegir acción
    public function index()
    {
        return view('usuarios.index');
    }

    // Listado para eliminar usuarios
    public function eliminar()
    {
        $users = User::select('id','name','email','role')->orderBy('name')->get();
        return view('usuarios.eliminar', compact('users'));
    }

    // Borrar (con protecciones)
    public function destroy(User $user)
    {
        // No te borres a ti mism@:
        if (auth()->id() === $user->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // Evita borrar al único admin
        if ($user->role === 'admin' && User::where('role','admin')->count() <= 1) {
            return back()->with('error', 'No puedes eliminar al único administrador.');
        }

        $user->delete();
        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}
