
<h2>Formulário de Contato</h2>
@if (session()->has('message'))
{{ session()->get('message') }}

@endif    

<form action="{{ route('messages.store') }}" method="POST">
    @csrf
        <label for="content">Nome:</label>
        <input type="text" id="content" name="content" required><br><br>
        <button type="submit">Enviar</button>
    </form>