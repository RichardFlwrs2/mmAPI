<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\User;

class UserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'user_id' => (int)$user->id,
            'nombre' => (string)$user->name,
            'email' => (string)$user->email,
            'role_id' => (int)$user->role_id,
            'isAdmin' => ($user->admin === 'true'),
            'phone' => (string)$user->phone,
            'birthdayDate' => (string)$user->birthdayDate,
            'puesto' => (string)$user->puesto,
            'address' => (string)$user->address,
        ];
    }
}
