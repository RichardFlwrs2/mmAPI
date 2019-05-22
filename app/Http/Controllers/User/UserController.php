<?php

namespace App\Http\Controllers\User;

use Validator;
use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    // ----------------------------------------------------------------------------------------------------- //
    // ? - I N D E X
    // ----------------------------------------------------------------------------------------------------- //
    public function index()
    {
        $usuarios = User::with(['role'])->get();

        return $this->showAll($usuarios);
    }



    // ----------------------------------------------------------------------------------------------------- //
    // ? - S T O R E
    // ----------------------------------------------------------------------------------------------------- //
    public function store(Request $request)
    {

        $data = json_decode( $request->input('data') , true );
        // $fileName = $request->file('file')->getClientOriginalName();
        $fileName = $request->file->getClientOriginalName();

        // $file = $request->file->store('pdf', 'local');
        $file = $request->file->store('');

        // dd( $file );

        Validator::make($data, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|numeric',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|min:6|max:16',
            'birthdayDate' => 'required',
            'puesto' => 'required',
            'address' => 'required',
        ])->validate();

        $data = json_decode( $request->input('data') );
        $data->password = bcrypt($data->password);
        $data->verified = User::USUARIO_NO_VERIFICADO;
        $data->verification_token = User::generarVerificationToken();
        $data->admin = User::USUARIO_REGULAR;

        $usuario = User::create((array) $data);

        return $this->showOne($usuario, 201);
    }




    // ----------------------------------------------------------------------------------------------------- //
    // ? - S H O W
    // ----------------------------------------------------------------------------------------------------- //
    public function show(User $user)
    {
        return $this->showOne($user);
    }



    // ----------------------------------------------------------------------------------------------------- //
    // ? - U P D A T E
    // ----------------------------------------------------------------------------------------------------- //
    public function update(Request $request, User $user)
    {
        $reglas = [
            'email' => 'email|unique:users,email,' . $user->id,
            'name' => 'present|max:250',
            'password' => 'min:6|confirmed',
            'role_id' => 'numeric',
            'phone' => 'present|min:6|max:16',
            'birthdayDate' => 'present',
            'puesto' => 'present',
            'address' => 'present',
        ];

        $this->validate($request, $reglas);

        $user->fill((array) $request->all());

        if ($request->has('email') && $user->email != $request->email) {
            $user->verified = User::USUARIO_NO_VERIFICADO;
            $user->verification_token = User::generarVerificationToken();
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        if (!$user->isDirty()) {
            return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);
        }

        $user->save();

        return $this->showOne($user);
    }



    // ----------------------------------------------------------------------------------------------------- //
    // ? - D E S T R O Y
    // ----------------------------------------------------------------------------------------------------- //
    public function destroy(User $user)
    {
        $user->delete();

        return $this->showOne($user);
    }


    // ----------------------------------------------------------------------------------------------------- //
    // ? - AUTH TOKEN VERIFY
    // ----------------------------------------------------------------------------------------------------- //
    public function verify($token)
    {
        $user = User::where('verification_token', $token)->firstOrFail();

        $user->verified = User::USUARIO_VERIFICADO;
        $user->verification_token = null;

        $user->save();

        return $this->showMessage('La cuenta ha sido verificada');
    }
}
