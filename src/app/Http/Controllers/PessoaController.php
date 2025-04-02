<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PessoaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $servidores = Pessoa::with('fotos')
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
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $pessoa = Pessoa::create($request->only([
            'pes_nome', 'pes_data_nascimento', 'pes_sexo', 'pes_mae', 'pes_pai'
        ]));

        $file = $request->file('foto');
        $hash = md5_file($file->getRealPath());
        $fileName = $hash . '.' . $file->getClientOriginalExtension();

        // Salvar no MinIO
        Storage::disk('minio')->put($fileName, file_get_contents($file));

        // Salvar no banco de dados
        $foto = $pessoa->fotos()->create([
            'fp_data' => now(),
            'fp_bucket' => env('MINIO_BUCKET'),
            'fp_hash' => $fileName
        ]);

        return response()->json([
            'pessoa' => $pessoa,
        ], 201);
    }

    public function show($id)
    {
        $servidor = Pessoa::with('fotos')
            ->findOrFail($id);

        return response()->json($servidor);
    }

    public function update(Request $request, $id)
    {
        $pessoa = Pessoa::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'pes_nome' => 'sometimes|string|max:200',
            'pes_data_nascimento' => 'sometimes|date',
            'pes_sexo' => 'sometimes|string|max:9',
            'pes_mae' => 'sometimes|string|max:200',
            'pes_pai' => 'sometimes|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $pessoa->pdate($request->only([
            'pes_nome', 'pes_data_nascimento', 'pes_sexo', 'pes_mae', 'pes_pai'
        ]));

        return response()->json($pessoa->load('fotos'));
    }

    public function destroy($id)
    {
        $pessoa = Pessoa::findOrFail($id);

        // Remover fotos do MinIO
        foreach ($pessoa->fotos as $foto) {
            Storage::disk('minio')->delete($foto->fp_hash);
        }
        
        $pessoa->delete();

        return response()->json(null, 204);
    }

    public function uploadFoto(Request $request, $id)
    {
        $servidor = Pessoa::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:8192',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $file = $request->file('foto');
        $hash = md5_file($file->getRealPath());
        $fileName = $hash . '.' . $file->getClientOriginalExtension();

        // Salvar no MinIO
        Storage::disk('minio')->put($fileName, file_get_contents($file));

        // Salvar no banco de dados
        $foto = $servidor->pessoa->fotos()->create([
            'fp_data' => now(),
            'fp_bucket' => env('MINIO_BUCKET'),
            'fp_hash' => $fileName
        ]);

        return response()->json($foto, 201);
    }
}
