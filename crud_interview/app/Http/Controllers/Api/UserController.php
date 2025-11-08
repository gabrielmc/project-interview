<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with(['profile', 'addresses'])->paginate(15);
        return response()->json([
            'success' => true,
            'data' => $usuarios
        ], 200);
    }

    public function search(Request $request)
    {
        $query = User::with(['profile', 'addresses']);
        // Filtro por Nome
        if ($request->has('nome') && !empty($request->nome)) {
            $query->where('name', 'LIKE', '%' . $request->nome . '%');
        }
        // Filtro por CPF
        if ($request->has('cpf') && !empty($request->cpf)) {
            $query->where('cpf', 'LIKE', '%' . $request->cpf . '%');
        }
        // Filtro por Período de Cadastro
        if ($request->has('data_inicio') && !empty($request->data_inicio)) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        if ($request->has('data_fim') && !empty($request->data_fim)) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }
        $usuarios = $query->paginate(15);
        return response()->json([
            'success' => true,
            'data' => $usuarios
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|size:11|unique:users,cpf',
            'profile_id' => 'required|exists:profiles,id',
            'password' => 'required|string|min:8',
            'addresses' => 'nullable|array',
            'addresses.*.cep' => 'required|string|size:8',
            'addresses.*.logradouro' => 'required|string|max:255',
            'addresses.*.numero' => 'required|string|max:10',
            'addresses.*.complemento' => 'nullable|string|max:255',
            'addresses.*.bairro' => 'required|string|max:100',
            'addresses.*.cidade' => 'required|string|max:100',
            'addresses.*.estado' => 'required|string|size:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'profile_id' => $request->profile_id,
            'password' => bcrypt($request->password),
        ]);

        // Vincular endereços, se fornecidos
        if ($request->has('addresses') && is_array($request->addresses)) {
            foreach ($request->addresses as $addressData) {
                $address = \App\Models\Address::firstOrCreate(
                    ['cep' => $addressData['cep'], 'numero' => $addressData['numero']],
                    $addressData
                );
                $usuario->addresses()->attach($address->id);
            }
        }
        $usuario->load(['profile', 'addresses']);
        return response()->json([
            'success' => true,
            'message' => 'Usuário criado com sucesso!',
            'data' => $usuario
        ], 201);
    }

    public function show($id)
    {
        $usuario = User::with(['profile', 'addresses'])->find($id);
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $usuario
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $usuario = User::find($id);
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'cpf' => 'sometimes|required|string|size:11|unique:users,cpf,' . $id,
            'profile_id' => 'sometimes|required|exists:profiles,id',
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $dados = $request->only(['name', 'email', 'cpf', 'profile_id']);
        if ($request->has('password') && !empty($request->password)) {
            $dados['password'] = bcrypt($request->password);
        }
        $usuario->update($dados);
        $usuario->load(['profile', 'addresses']);

        return response()->json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso!',
            'data' => $usuario
        ], 200);
    }

    public function destroy($id)
    {
        $usuario = User::find($id);
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], 404);
        }
        // Desvincular endereços antes de excluir
        $usuario->addresses()->detach();
        $usuario->delete();
        return response()->json([
            'success' => true,
            'message' => 'Usuário excluído com sucesso!'
        ], 200);
    }

    public function getAddresses($id)
    {
        $usuario = User::with('addresses')->find($id);
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $usuario->addresses
        ], 200);
    }
}