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

    public function edit($id, Request $request)
    {
        $cliente = Cliente::findOrFail($id);

        $cliente->nome = $request->nome;
        $cliente->sobrenome = $request->sobrenome;
        $cliente->sexo = $request->sexo;
        $cliente->cpf_cnpj = $request->cpf_cnpj;
        $cliente->telefone = $request->telefone;
        $cliente->email = $request->email;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('clientes', 'public');
            $cliente->foto = $path;
        }

        $cliente->save();

        return response()->json(['message' => 'Cliente editado com sucesso!', 'cliente' => $cliente], 201);
    }

    public function editAddress($id, Request $request)
    {
        $cliente = Cliente::findOrFail($id);

        $cliente->estado = $request->uf;
        $cliente->cidade = $request->cidade;
        $cliente->cep = $request->cep;
        $cliente->bairro = $request->bairro;
        $cliente->rua = $request->rua;
        $cliente->complemento = $request->complemento;

        $cliente->save();

        return response()->json(['message' => 'Dados de endereço editado com sucesso!', 'cliente' => $cliente], 200);
    }

    public function client($id)
    {
        $cliente = Cliente::with('contratos.bem.tipo')->find($id);
        return response()->json(['cliente' => $cliente], 200);
    }

    public function filter(Request $request)
    {
        $query = Cliente::query();

        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        if ($request->filled('sobrenome')) {
            $query->where('sobrenome', 'like', '%' . $request->sobrenome . '%');
        }

        if ($request->filled('sexo')) {
            $query->where('sexo', $request->sexo);
        }

        if ($request->filled('cpf_cnpj')) {
            $query->where('cpf_cnpj', $request->cpf_cnpj);
        }

        if ($request->filled('telefone')) {
            $query->where('telefone', 'like', '%' . $request->telefone . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        $clientes = $query->with('contratos')->get();

        return response()->json(['clientes' => $clientes]);
    }
}
