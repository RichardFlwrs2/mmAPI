@component('mail::message')
# Hola {{$leader->name}}

<p> El usuario {{ $user_asigned->name }} hace una petición para <b> borrar </b> la requisición </p>

@component('mail::button', ['url' => $url ])
    Ir al detalle de la orden
@endcomponent

@endcomponent
