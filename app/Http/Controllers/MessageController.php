<?php

namespace App\Http\Controllers;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    
    public readonly Message $message;
    public function __construct()
    {
      $this ->message = new Message;  
    }


    public function index()
    {
        $messages = $this->message->all();

        return view('messages',['messages' => $messages]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('messages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = $this->message;
        $messages -> content = $request->content;
        
        $messages->save();
        
        if($messages){
            return redirect()->back()->with('message', 'Mensagem criada com sucesso!');
           }
    
           else{
            return redirect()->back()->with('message', 'Erro Ao criar a mensagem!');
           }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
     return view('messages.show', ['message' => $message]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
     return view ('messages.update', ['message' => $message]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       $updated = $this->message->where('id', $id)->update(['content' => $request->content]);
       if($updated){
        return redirect()->back()->with('message', 'Mensagem atualizada com sucesso!');
       }

       else{
        return redirect()->back()->with('message', 'Erro ao atualizar a mensagem!');
       }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->message->where('id', $id)->delete();

        return redirect()->route('messages.index')->with('message', 'Mensagem excluida com sucesso!');
    }
}
