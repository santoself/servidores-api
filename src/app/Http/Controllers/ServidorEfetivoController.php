<?php

namespace App\Http\Controllers;

use App\Models\ServidorEfetivo;
use App\Models\Pessoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;
use Aws\S3\Exception\S3Exception;

class ServidorEfetivoController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $servidores = ServidorEfetivo::with('pessoa')
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
            'se_matricula' => 'required|string|max:20|unique:servidor_efetivo',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $pessoa = Pessoa::create($request->only([
            'pes_nome', 'pes_data_nascimento', 'pes_sexo', 'pes_mae', 'pes_pai'
        ]));

        $servidor = ServidorEfetivo::create([
            'pes_id' => $pessoa->pes_id,
            'se_matricula' => $request->se_matricula
        ]);

        return response()->json([
            'pessoa' => $pessoa,
            'servidor' => $servidor
        ], 201);
    }

    public function show($id)
    {
        $servidor = ServidorEfetivo::with('pessoa', 'pessoa.fotos', 'pessoa.lotacoes.unidade')
            ->findOrFail($id);

        return response()->json($servidor);
    }

    public function update(Request $request, $id)
    {
        $servidor = ServidorEfetivo::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'pes_nome' => 'sometimes|string|max:200',
            'pes_data_nascimento' => 'sometimes|date',
            'pes_sexo' => 'sometimes|string|max:9',
            'pes_mae' => 'sometimes|string|max:200',
            'pes_pai' => 'sometimes|string|max:200',
            'se_matricula' => 'sometimes|string|max:20|unique:servidor_efetivo,se_matricula,'.$servidor->pes_id.',pes_id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $servidor->update($request->only(['se_matricula']));
        $servidor->pessoa->update($request->only([
            'pes_nome', 'pes_data_nascimento', 'pes_sexo', 'pes_mae', 'pes_pai'
        ]));

        return response()->json($servidor->load('pessoa'));
    }

    public function destroy($id)
    {
        $servidor = ServidorEfetivo::findOrFail($id);
        $pessoa = $servidor->pessoa;

        // Remover fotos do MinIO
        foreach ($pessoa->fotos as $foto) {
            Storage::disk('minio')->delete($foto->fp_hash);
        }

        $servidor->delete();
        $pessoa->delete();

        return response()->json(null, 204);
    }

    public function uploadFoto(Request $request, $id)
    {
        $servidor = ServidorEfetivo::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:8192',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $file = $request->file('foto');
        $hash = md5_file($file->getRealPath());
        $fileName = rand(1, 9999). $hash . '.' . $file->getClientOriginalExtension();

        // Salvar no MinIO
        $salvou = Storage::disk('minio')->put($fileName, file_get_contents($file));
        // dd($value);

        if($salvou) {
            // Salvar no banco de dados
            $foto = $servidor->pessoa->fotos()->create([
                'fp_data' => now(),
                'fp_bucket' => env('MINIO_BUCKET'),
                'fp_hash' => $fileName
            ]);

            return response()->json($foto, 201);
        }
        else
        {
            return response()->json(['error'=> 'Erro ao subir foto'], 401);
        }
    }



    // public function uploadFoto(Request $request, $id)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 400);
    //     }

    //     $servidor = ServidorEfetivo::findOrFail($id);
    //     $file = $request->file('foto');
    //     $hash = md5_file($file->getRealPath());
    //     $fileName = $hash . '.' . $file->getClientOriginalExtension();

    //     try {
    //         // Debug: Verificar configuração antes do upload
    //         Log::info('Configuração MinIO:', [
    //             'bucket' => config('filesystems.disks.minio.bucket'),
    //             'endpoint' => config('filesystems.disks.minio.endpoint'),
    //             'key' => config('filesystems.disks.minio.key')
    //         ]);

    //         // 1. Verificar se o bucket existe
    //         if (!Storage::disk('minio')->exists('')) {
    //             Storage::disk('minio')->makeDirectory('');
    //             Log::info('Bucket criado com sucesso');
    //         }

    //         // 2. Tentar o upload
    //         Storage::disk('minio')->put($fileName, file_get_contents($file));
    //         Log::info('Arquivo enviado para o MinIO', ['file' => $fileName]);

    //         // 3. Verificar se o arquivo realmente foi enviado
    //         if (!Storage::disk('minio')->exists($fileName)) {
    //             throw new \Exception("O upload foi concluído, mas o arquivo não foi encontrado no MinIO");
    //         }

    //         // 4. Salvar no banco de dados
    //         $foto = $servidor->pessoa->fotos()->create([
    //             'fp_data' => now(),
    //             'fp_bucket' => env('MINIO_BUCKET'),
    //             'fp_hash' => $fileName
    //         ]);

    //         // 5. Gerar URL temporária para verificação
    //         $url = Storage::disk('minio')->temporaryUrl(
    //             $fileName,
    //             now()->addMinutes(5)
    //         );

    //         return response()->json([
    //             'message' => 'Upload realizado com sucesso',
    //             'foto' => $foto,
    //             'url_temporaria' => $url
    //         ], 201);

    //     } catch (S3Exception $e) {
    //         Log::error('Erro S3/MinIO:', [
    //             'error' => $e->getMessage(),
    //             'aws_code' => $e->getAwsErrorCode(),
    //             'details' => $e->getAwsErrorMessage()
    //         ]);
            
    //         return response()->json([
    //             'error' => 'Erro no servidor de armazenamento',
    //             'message' => $e->getAwsErrorMessage(),
    //             'code' => $e->getStatusCode()
    //         ], 500);

    //     } catch (\Exception $e) {
    //         Log::error('Erro geral no upload:', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
            
    //         return response()->json([
    //             'error' => 'Erro no processo de upload',
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function getFotoUrl($id, $fotoId)
    {
        $servidor = ServidorEfetivo::findOrFail($id);
        $foto = $servidor->pessoa->fotos()->findOrFail($fotoId);
        
        $url = Storage::disk('minio')->temporaryUrl(
            $foto->fp_hash,
            now()->addMinutes(5)
        );

        return response()->json(['url' => $url]);
    }

    public function getByUnidade($unidId)
    {
        $servidores = ServidorEfetivo::whereHas('pessoa.lotacoes', function($query) use ($unidId) {
            $query->where('unid_id', $unidId)
                ->whereNull('lot_data_remocao');
        })
        ->with(['pessoa' => function($query) {
            $query->select('pes_id', 'pes_nome', 'pes_data_nascimento')
                ->with(['fotos' => function($query) {
                    $query->select('fp_id', 'pes_id', 'fp_hash');
                }]);
        }])
        ->get()
        ->map(function($servidor) {
            $idade = now()->diffInYears($servidor->pessoa->pes_data_nascimento);
            
            return [
                'nome' => $servidor->pessoa->pes_nome,
                'idade' => $idade,
                'unidade' => $servidor->pessoa->lotacoes->first()->unidade->unid_nome,
                'foto_url' => $servidor->pessoa->fotos->isNotEmpty() 
                    ? Storage::disk('minio')->temporaryUrl(
                        $servidor->pessoa->fotos->first()->fp_hash,
                        now()->addMinutes(5)
                    )
                    : null
            ];
        });

        return response()->json($servidores);
    }

    public function getEnderecoFuncional(Request $request)
    {
        $nome = $request->input('nome');

        $servidores = ServidorEfetivo::whereHas('pessoa', function($query) use ($nome) {
            $query->where('pes_nome', 'like', "%$nome%");
        })
        ->with(['pessoa.lotacoes.unidade.enderecos.cidade'])
        ->get()
        ->map(function($servidor) {
            $lotacaoAtual = $servidor->pessoa->lotacoes->firstWhere('lot_data_remocao', null);
            
            if (!$lotacaoAtual || !$lotacaoAtual->unidade->enderecos->isNotEmpty()) {
                return null;
            }

            $endereco = $lotacaoAtual->unidade->enderecos->first();
            
            return [
                'servidor' => $servidor->pessoa->pes_nome,
                'unidade' => $lotacaoAtual->unidade->unid_nome,
                'endereco' => [
                    'logradouro' => $endereco->end_tipo_logradouro . ' ' . $endereco->end_logradouro,
                    'numero' => $endereco->end_numero,
                    'bairro' => $endereco->end_bairro,
                    'cidade' => $endereco->cidade->cid_nome . '/' . $endereco->cidade->cid_uf
                ]
            ];
        })
        ->filter()
        ->values();

        return response()->json($servidores);
    }
}
