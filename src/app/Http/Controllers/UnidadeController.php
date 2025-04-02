<?php

namespace App\Http\Controllers;

use App\Models\Unidade;
use App\Models\Endereco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnidadeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $unidades = Unidade::with('enderecos.cidade')
            ->paginate($perPage);

        return response()->json($unidades);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unid_nome' => 'required|string|max:200',
            'unid_sigla' => 'required|string|max:20',
            'enderecos' => 'sometimes|array',
            'enderecos.*.end_tipo_logradouro' => 'required_with:enderecos|string|max:50',
            'enderecos.*.end_logradouro' => 'required_with:enderecos|string|max:200',
            'enderecos.*.end_numero' => 'required_with:enderecos|integer',
            'enderecos.*.end_bairro' => 'required_with:enderecos|string|max:100',
            'enderecos.*.cid_id' => 'required_with:enderecos|exists:cidade,cid_id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $unidade = Unidade::create($request->only(['unid_nome', 'unid_sigla']));

        if ($request->has('enderecos')) {
            foreach ($request->enderecos as $enderecoData) {
                $endereco = Endereco::create($enderecoData);
                $unidade->enderecos()->attach($endereco->end_id);
            }
        }

        return response()->json($unidade->load('enderecos.cidade'), 201);
    }

    public function show($id)
    {
        $unidade = Unidade::with('enderecos.cidade', 'lotacoes.pessoa')
            ->findOrFail($id);

        return response()->json($unidade);
    }

    public function update(Request $request, $id)
    {
        $unidade = Unidade::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'unid_nome' => 'sometimes|string|max:200',
            'unid_sigla' => 'sometimes|string|max:20',
            'enderecos' => 'sometimes|array',
            'enderecos.*.end_id' => 'sometimes|exists:endereco,end_id',
            'enderecos.*.end_tipo_logradouro' => 'sometimes|string|max:50',
            'enderecos.*.end_logradouro' => 'sometimes|string|max:200',
            'enderecos.*.end_numero' => 'sometimes|integer',
            'enderecos.*.end_bairro' => 'sometimes|string|max:100',
            'enderecos.*.cid_id' => 'sometimes|exists:cidade,cid_id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $unidade->update($request->only(['unid_nome', 'unid_sigla']));

        if ($request->has('enderecos')) {
            $enderecosAtuais = $unidade->enderecos->pluck('end_id')->toArray();
            $enderecosNovos = [];

            foreach ($request->enderecos as $enderecoData) {
                if (isset($enderecoData['end_id'])) {
                    // Atualizar endereço existente
                    $endereco = Endereco::find($enderecoData['end_id']);
                    $endereco->update($enderecoData);
                    $enderecosNovos[] = $endereco->end_id;
                } else {
                    // Criar novo endereço
                    $endereco = Endereco::create($enderecoData);
                    $enderecosNovos[] = $endereco->end_id;
                }
            }

            // Sincronizar relacionamentos
            $unidade->enderecos()->sync($enderecosNovos);

            // Remover endereços não mais utilizados
            $enderecosParaRemover = array_diff($enderecosAtuais, $enderecosNovos);
            Endereco::whereIn('end_id', $enderecosParaRemover)->delete();
        }

        return response()->json($unidade->load('enderecos.cidade'));
    }

    public function destroy($id)
    {
        $unidade = Unidade::findOrFail($id);
        
        // Remover endereços associados
        $enderecosIds = $unidade->enderecos->pluck('end_id')->toArray();
        $unidade->enderecos()->detach();
        Endereco::whereIn('end_id', $enderecosIds)->delete();

        $unidade->delete();

        return response()->json(null, 204);
    }
}
