<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        $perfis = Profile::withCount('users')->get();
        return response()->json([
            'success' => true,
            'data' => $perfis
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255|unique:profiles,name',
                'description' => 'nullable|string',
            ],
            [
                //Mensagens personalizadas
                'name.required' => 'O nome do perfil é obrigatório.',
                'name.string' => 'O nome do perfil deve ser um texto válido.',
                'name.max' => 'O nome do perfil não pode ter mais que :max caracteres.',
                'name.unique' => 'Já existe um perfil com este nome.',
                'description.string' => 'A descrição deve ser um texto válido.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $perfil = Profile::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Perfil criado com sucesso!',
            'data' => $perfil
        ], 201);
    }

    public function show($id)
    {
        $perfil = Profile::with('users')->find($id);

        if (!$perfil) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $perfil
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $perfil = Profile::find($id);

        if (!$perfil) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:profiles,name,' . $id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $perfil->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso!',
            'data' => $perfil
        ], 200);
    }

    public function destroy($id)
    {
        $perfil = Profile::find($id);

        if (!$perfil) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil não encontrado'
            ], 404);
        }

        // Verificar se há usuários vinculados
        if ($perfil->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir. Existem usuários vinculados a este perfil.'
            ], 409);
        }

        $perfil->delete();

        return response()->json([
            'success' => true,
            'message' => 'Perfil excluído com sucesso!'
        ], 200);
    }

    public function getUsers($id)
    {
        $perfil = Profile::with('users')->find($id);

        if (!$perfil) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $perfil->users
        ], 200);
    }
}