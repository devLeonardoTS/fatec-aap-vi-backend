<h2>Mostrar cliente</h2>

@foreach ($messages as $message )
    <li>{{ $message->content }} | <a href="{{ route('messages.edit',['message' =>$message -> id]) }}">Editar</a> | <a href="{{ route('messages.show',['message' => $message->id]) }}" >Mostrar</a>
    <li></li>

    <br><br>
@endforeach