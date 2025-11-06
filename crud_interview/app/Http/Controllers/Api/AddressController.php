<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class AddressController extends Controller
{
    public function index()
    {
        $enderecos = Address::withCount('users')->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $enderecos
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cep' => 'required|string|size:8',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|size:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $endereco = Address::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Endereço criado com sucesso!',
            'data' => $endereco
        ], 201);
    }

    public function show($id)
    {
        $endereco = Address::with('users')->find($id);

        if (!$endereco) {
            return response()->json([
                'success' => false,
                'message' => 'Endereço não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $endereco
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $endereco = Address::find($id);

        if (!$endereco) {
            return response()->json([
                'success' => false,
                'message' => 'Endereço não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'cep' => 'sometimes|required|string|size:8',
            'logradouro' => 'sometimes|required|string|max:255',
            'numero' => 'sometimes|required|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'sometimes|required|string|max:100',
            'cidade' => 'sometimes|required|string|max:100',
            'estado' => 'sometimes|required|string|size:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $endereco->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Endereço atualizado com sucesso!',
            'data' => $endereco
        ], 200);
    }

    public function destroy($id)
    {
        $endereco = Address::find($id);

        if (!$endereco) {
            return response()->json([
                'success' => false,
                'message' => 'Endereço não encontrado'
            ], 404);
        }

        $endereco->users()->detach();
        $endereco->delete();

        return response()->json([
            'success' => true,
            'message' => 'Endereço excluído com sucesso!'
        ], 200);
    }

    public function attachUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $endereco = Address::find($id);
        if (!$endereco) {
            return response()->json([
                'success' => false,
                'message' => 'Endereço não encontrado'
            ], 404);
        }

        $endereco->users()->syncWithoutDetaching([$request->user_id]);

        return response()->json([
            'success' => true,
            'message' => 'Usuário vinculado ao endereço com sucesso!'
        ], 200);
    }

    public function detachUser($id, $userId)
    {
        $endereco = Address::find($id);
        if (!$endereco) {
            return response()->json([
                'success' => false,
                'message' => 'Endereço não encontrado'
            ], 404);
        }

        $endereco->users()->detach($userId);

        return response()->json([
            'success' => true,
            'message' => 'Usuário desvinculado do endereço com sucesso!'
        ], 200);
    }

    public function getUsers($id)
    {
        $endereco = Address::with('users')->find($id);

        if (!$endereco) {
            return response()->json([
                'success' => false,
                'message' => 'Endereço não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $endereco->users
        ], 200);
    }

    /**
     * Buscar CEP via API ViaCEP
     * GET /api/v1/cep/{cep}
     */
    public function searchCep($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);

        if (strlen($cep) != 8) {
            return response()->json([
                'success' => false,
                'message' => 'CEP inválido'
            ], 400);
        }

        try {
            $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['erro'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'CEP não encontrado'
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'cep' => $data['cep'],
                        'logradouro' => $data['logradouro'],
                        'complemento' => $data['complemento'],
                        'bairro' => $data['bairro'],
                        'cidade' => $data['localidade'],
                        'estado' => $data['uf'],
                    ]
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar CEP'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar CEP: ' . $e->getMessage()
            ], 500);
        }
    }
}