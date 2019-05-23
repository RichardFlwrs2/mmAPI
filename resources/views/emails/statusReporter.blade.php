@component('mail::message')
# Hola {{$leader->name}}

<p> El usuario {{ $user_asigned->name }} a pasado su requisición a: <b> {{ $status->name }} </b> </p>

@component('mail::button', ['url' => ''])
    Ir al detalle de la orden
@endcomponent

@endcomponent
