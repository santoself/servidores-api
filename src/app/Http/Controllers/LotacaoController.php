<?php

namespace App\Http\Controllers;

use App\Models\Lotacao;
use App\Models\Pessoa;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LotacaoController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $lotacoes = Lotacao::with(['pessoa', 'unidade'])
            ->paginate($perPage);

        return response()->json($lotacoes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pes_id' => 'required|exists:pessoa,pes_id',
            'unid_id' => 'required|exists:unidade,unid_id',
            'lot_data_lotacao' => 'required|date',
            'lot_data_remocao' => 'nullable|date|after:lot_data_lotacao',
            'lot_portaria' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Verificar se a pessoa já está lotada na mesma unidade sem remoção
        $lotacaoExistente = Lotacao::where('pes_id', $request->pes_id)
            ->where('unid_id', $request->unid_id)
            ->whereNull('lot_data_remocao')
            ->first();

        if ($lotacaoExistente) {
            return response()->json([
                'error' => 'Esta pessoa já está lotada nesta unidade sem data de remoção'
            ], 400);
        }

        $lotacao = Lotacao::create($request->all());

        return response()->json($lotacao->load(['pessoa', 'unidade']), 201);
    }

    public function show($id)
    {
        $lotacao = Lotacao::with(['pessoa', 'unidade'])
            ->findOrFail($id);

        return response()->json($lotacao);
    }

    public function update(Request $request, $id)
    {
        $lotacao = Lotacao::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'pes_id' => 'sometimes|exists:pessoa,pes_id',
            'unid_id' => 'sometimes|exists:unidade,unid_id',
            'lot_data_lotacao' => 'sometimes|date',
            'lot_data_remocao' => 'nullable|date|after:lot_data_lotacao',
            'lot_portaria' => 'sometimes|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $lotacao->update($request->all());

        return response()->json($lotacao->load(['pessoa', 'unidade']));
    }

    public function destroy($id)
    {
        $lotacao = Lotacao::findOrFail($id);
        $lotacao->delete();

        return response()->json(null, 204);
    }
}
