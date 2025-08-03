<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('contratos')->get();
        return response()->json(['clientes' => $clientes]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'sobrenome' => 'required|string',
            'cpf_cnpj' => 'required|string|unique:clientes,cpf_cnpj',
        ]);

        $cliente = new Cliente();
        $cliente->nome = $request->nome;
        $cliente->sobrenome = $request->sobrenome;
        $cliente->sexo = $request->sexo;
        $cliente->cpf_cnpj = $request->cpf_cnpj;
        $cliente->telefone = $request->telefone ?? null;
        $cliente->email = $request->email ?? null;
        $cliente->cep = $request->cep ?? null;
        $cliente->rua = $request->rua ?? null;
        $cliente->numero = $request->numero ?? null;
        $cliente->complemento = $request->complemento ?? null;
        $cliente->bairro = $request->bairro ?? null;
        $cliente->cidade = $request->cidade ?? null;
        $cliente->estado = $request->estado ?? null;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('clientes', 'public');
            $cliente->foto = $path;
        }

        $cliente->save();

        return response()->json(['message' => 'Cliente cadastrado com sucesso!', 'cliente' => $cliente], 201);
    }

    public function edit(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'sobrenome' => 'required|string',
            'cpf_cnpj' => 'required|string|unique:clientes,cpf_cnpj',
        ]);

        $cliente = Cliente::find($request->cliente);
        $cliente->nome = $request->nome;
        $cliente->sobrenome = $request->sobrenome;
        $cliente->sexo = $request->sexo;
        $cliente->cpf_cnpj = $request->cpf_cnpj;
        $cliente->telefone = $request->telefone ?? null;
        $cliente->email = $request->email ?? null;
        $cliente->cep = $request->cep ?? null;
        $cliente->rua = $request->rua ?? null;
        $cliente->numero = $request->numero ?? null;
        $cliente->complemento = $request->complemento ?? null;
        $cliente->bairro = $request->bairro ?? null;
        $cliente->cidade = $request->cidade ?? null;
        $cliente->estado = $request->estado ?? null;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('clientes', 'public');
            $cliente->foto = $path;
        }

        $cliente->save();

        return response()->json(['message' => 'Cliente editado com sucesso!', 'cliente' => $cliente], 201);
    }

    public function client($id)
    {
        $cliente = Cliente::with('contratos.bem.tipo')->find($id);
        return response()->json(['cliente' => $cliente], 200);
    }
}
