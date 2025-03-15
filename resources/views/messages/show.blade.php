
<h2>Mensagens - {{ $message->content }}</h2>

<form action="{{ route('messages.destroy',['message' => $message->id]) }}" method="post">
@csrf
<input type="hidden" name="_method" value="DELETE">
<button type="submit">Deletar</button>
</form>
