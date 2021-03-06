<?php
namespace App\Http\Controllers;
use App\Atividade;
use App\Mensagem;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Validator;
use \Illuminate\Support\Facades\Auth;


class MensagemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
    {
        if(Auth::check()){
            $listaMensagens = Mensagem::where('user_id', Auth::id())->paginate(3);
        }else{
             $listaMensagens = Mensagem::paginate(3);
        }

        return view('mensagem.list',['mensagens' => $listaMensagens]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $atividades = Atividade::all();
        return view('mensagem.create',['atividades'=>$atividades]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //faço as validações dos campos
        //vetor com as mensagens de erro
        $messages = array(
            'titulo.required' => 'É obrigatório um título para a mensagem',
            'texto.required' => 'É obrigatória um texto',
            'autor.required' => 'É obrigatório o cadastro do autor da mensagem',
        );
        //vetor com as especificações de validações
        $regras = array(
            'titulo' => 'required|string|max:255',
            'texto' => 'required',
            'autor' => 'required|string',
        );
        //cria o objeto com as regras de validação
        $validador = Validator::make($request->all(), $regras, $messages);
        //executa as validações
        if ($validador->fails()) {
            return redirect('mensagens/create')
            ->withErrors($validador)
            ->withInput($request->all);
        }
        //se passou pelas validações, processa e salva no banco...
        $obj_Mensagem = new Mensagem();
        $obj_Mensagem->titulo =       $request['titulo'];
        $obj_Mensagem->texto = $request['texto'];
        $obj_Mensagem->autor = $request['autor'];
        $obj_Mensagem->user_id = Auth::id();
        $obj_Mensagem->atividade_id = $request['atividade_id'];
        $obj_Mensagem->save();
        return redirect('/mensagens')->with('success', 'Mensagem criada com sucesso!!');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Mensagem  $mensagem
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mensagem = Mensagem::find($id);
        return view('mensagem.show',['mensagem'=>$mensagem]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Mensagem  $mensagem
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         $mensagem = Mensagem::find($id);
        return view('mensagem.edit',['mensagem'=>$mensagem]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mensagem  $mensagem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //faço as validações dos campos

        //vetor com as mensagens de erro
        $messages = array(
            'titulo.required' => 'É obrigatório um título para a atividade',
            'texto.required' => 'É obrigatória uma descrição para a atividade',
            'autor.required' => 'É obrigatório o cadastro da data/hora da atividade',
        );

        //vetor com as especificações de validações
        $regras = array(
            'titulo' => 'required|string|max:255',
            'texto' => 'required',
            'autor' => 'required|string',
        );

        //cria o objeto com as regras de validação
        $validador = Validator::make($request->all(), $regras, $messages);

        //executa as validações
        if ($validador->fails()) {
            return redirect("mensagens/$id/edit")
            ->withErrors($validador)
            ->withInput($request->all);
        }

        //se passou pelas validações, processa e salva no banco...
        $obj_atividade = Mensagem::findOrFail($id);
        $obj_atividade->titulo =       $request['titulo'];
        $obj_atividade->texto = $request['texto'];
        $obj_atividade->autor = $request['autor'];
        $obj_atividade->user_id = Auth::id();
        $obj_atividade->save();

        return redirect('/mensagens')->with('success', 'Mensagem alterada com sucesso!!');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Mensagem  $mensagem
     * @return \Illuminate\Http\Response
     */

    public function delete($id)
    {
        $obj_Mensagem = Mensagem::find($id);
        return view('mensagem.delete',['mensagem' => $obj_Mensagem]);
    }

    public function destroy($id)
    {
        $obj = Mensagem::findOrFail($id);
        $obj->delete($id);
        return redirect('/mensagens')->with('success','Mensagem excluída com Sucesso!!');
    }
}