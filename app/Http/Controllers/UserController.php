<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
      $users = User::all();
      $title = 'Listado de Usuarios';
      return view('users.index', compact('title','users'));
    }

    public function show(User $user)
    {
      // dd($user); // para comprobar resultados. si aparece el usuario
      return view('users.show', compact('user'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store()
    {
        $data = request()->validate([
          'name' => 'required',
          'email' => ['required','email','unique:users,email'],
          'login' => ['required','min:6','unique:users,login'],
          'password' => ['required','min:6'],
          'Admin' => '',
          'FacEsc' => '',
          'AgUnam' => '',
          'Jud' => '',
          'Sria' => '',
          'JSecc' => '',
          'JArea' => '',
          'Ofisi' => '',
          'Invit' => '',
          ],[
          'name.required' => 'El campo nombre es obligatorio',
          'email.required' => 'El campo email es obligatorio',
          'email.email' => 'El campo email no es valido',
          'email.unique' => 'Este correo ya ha sido utilizado',
          'login.required' => 'El login mínimo es de 6 caracteres',
          'login.unique' => 'Este login ya ha sido utilizado',
          'password.required' => 'El campo password es obligatorio',
          'password.min' => 'El password minimo es de 6 caracteres'
        ]);
        //$data = request()->all(); //Con esto funcional la prueba pero no la validacion

        $user = new User();
        $user->name =  $data['name'];
        $user->email = $data['email'];
        $user->login = $data['login'];
        $user->password = bcrypt($data['password']);
        $user->save();

        // borramos todos los roles asociados en la tabla role_table
        $user->roles()->detach();

        // verificamos si se encuentra verificada la casilla entonces lo asociamos a la tabla pivote
        if( isset($_POST['Admin'])) { $user->roles()->attach( $_POST['Admin'] ); }
        if( isset($_POST['FacEsc'])) { $user->roles()->attach( $_POST['FacEsc'] ); }
        if( isset($_POST['AgUnam'])) { $user->roles()->attach( $_POST['AgUnam'] ); }
        if( isset($_POST['Jud'])) { $user->roles()->attach( $_POST['Jud'] ); }
        if( isset($_POST['Sria'])) { $user->roles()->attach( $_POST['Sria'] ); }
        if( isset($_POST['JSecc'])) { $user->roles()->attach( $_POST['JSecc'] ); }
        if( isset($_POST['JArea'])) { $user->roles()->attach( $_POST['JArea'] ); }
        if( isset($_POST['Ofisi'])) { $user->roles()->attach( $_POST['Ofisi'] ); }
        $user->roles()->attach( '9' ); // por omision, el usuario tiene el rol de invitado

      return redirect()->route('users');  // redireccionamos al listado de usuarios

    }

    function edit(User $user)
    {
        return view('users.edit',['user'=> $user]);
    }

    function update(User $user)
    {
        // $data = request()->all(); // no se debe usar
        $data = request()->validate([
          'name' => 'required',
          'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
          'password' => '',
        ]);

        if($data['password'] != null){
            $data['password'] = bcrypt($data['password']);
        } else {
              unset($data['password']);
        }

        $user->update($data);

       // return redirect("usuarios/{$user->id}");
       return redirect()->route('users.show',['user'=>$user]); // Eloquet toma el Id por lo que se pudo especificar explicitamente $user->id
    }

    function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users'); // equivalente a la ruta 'usuarios'
    }

}
