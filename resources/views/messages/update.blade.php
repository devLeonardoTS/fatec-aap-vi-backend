
<h2>Edi/track></h2>
  
@if (session()->has('message'))
{{ session()->get('message') }}

@endif



<form action="{{ route('messages.update',['message' => $message->id])     }}" method="POST">
    @csrf
    <input type="hidden" name="_method" value="PUT">
        <label for="content">Nome:</label>
        <input type="text" id="content" name="content" value="{{ $message->content }}"> <br><br>
        <button type="submit">Enviar</button>
    </form>