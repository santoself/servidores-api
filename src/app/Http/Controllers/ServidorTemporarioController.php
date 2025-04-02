<?php

namespace App\Http\Controllers;

use App\Models\ServidorTemporario;
use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServidorTemporarioController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $servidores = ServidorTemporario::with('pessoa')
            ->paginate($perPage);

        return response()->json($servidores);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pes_nome' => 'required|string|max:200',
            'pes_data_nascimento' => 'required|date',
            'pes_sexo' => 'required|string|max:9',
            'pes_mae' => 'required|string|max:200',
            'pes_pai' => 'required|string|max:200',
            'st_data_admissao' => 'required|date',
            'st_data_demissao' => 'nullable|date|after:st_data_admissao',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $pessoa = Pessoa::create($request->only([
            'pes_nome', 'pes_data_nascimento', 'pes_sexo', 'pes_mae', 'pes_pai'
        ]));

        $servidor = ServidorTemporario::create([
            'pes_id' => $pessoa->pes_id,
            'st_data_admissao' => $request->st_data_admissao,
            'st_data_demissao' => $request->st_data_demissao
        ]);

        return response()->json([
            'pessoa' => $pessoa,
            'servidor' => $servidor
        ], 201);
    }

    public function show($id)
    {
        $servidor = ServidorTemporario::with('pessoa')
            ->findOrFail($id);

        return response()->json($servidor);
    }

    public function update(Request $request, $id)
    {
        $servidor = ServidorTemporario::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'pes_nome' => 'sometimes|string|max:200',
            'pes_data_nascimento' => 'sometimes|date',
            'pes_sexo' => 'sometimes|string|max:9',
            'pes_mae' => 'sometimes|string|max:200',
            'pes_pai' => 'sometimes|string|max:200',
            'st_data_admissao' => 'sometimes|date',
            'st_data_demissao' => 'nullable|date|after:st_data_admissao',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $servidor->update($request->only(['st_data_admissao', 'st_data_demissao']));
        $servidor->pessoa->update($request->only([
            'pes_nome', 'pes_data_nascimento', 'pes_sexo', 'pes_mae', 'pes_pai'
        ]));

        return response()->json($servidor->load('pessoa'));
    }

    public function destroy($id)
    {
        $servidor = ServidorTemporario::findOrFail($id);
        $pessoa = $servidor->pessoa;

        $servidor->delete();
        $pessoa->delete();

        return response()->json(null, 204);
    }
}
