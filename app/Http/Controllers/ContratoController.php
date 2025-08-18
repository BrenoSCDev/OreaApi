<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Saldo;
use App\Models\Bem;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\ModeloContrato;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use Dompdf\Dompdf;

class ContratoController extends Controller
{
    public function index()
    {
        $contratos = Contrato::with('cliente')
            ->orderBy('dtvenc', 'asc') // ordena do mais recente ao mais antigo
            ->get();

        return response()->json(['contratos' => $contratos]);
    }

    public function filter(Request $request)
    {
        $query = Contrato::query();

        if ($request->filled('chave')) {
            $query->where('contrato_hash', 'like', '%' . $request->chave . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dtvenc')) {
            $data = Carbon::createFromFormat('d/m/y', $request->dtvenc);
            // return response()->json(['data' => $data]);
            $query->whereDate('dtvenc', $data);
        }

        $contratos = $query->with('cliente')
            ->orderBy('dtvenc', 'asc') // ordena do mais recente ao mais antigo
            ->get();

        return response()->json(['contratos' => $contratos]);
    }


    public function store(Request $request)
    {
        $dtvenc = $request->dtvenc;
        $formattedDate = Carbon::createFromFormat('d/m/y', $dtvenc);

        $contrato = new Contrato();
        do {
            $hash = Str::upper(Str::random(12));
        } while (Contrato::where('contrato_hash', $hash)->exists());

        $contrato->contrato_hash = $hash;
        $contrato->status = "Aguardando Aprovação";
        $contrato->valor_emprestimo = $request->valor_emprestimo;
        $contrato->juros = $request->juros;
        $contrato->valor_receber = (($request->valor_emprestimo * $request->juros) / 100) + $request->valor_emprestimo;
        $contrato->dtvenc = $formattedDate;
        $contrato->cliente_id = $request->cliente_id;

        $modelo = ModeloContrato::find($request->modelo_contrato_id);
        $pathToModel = storage_path("app/public/{$modelo->arquivo_path}");

        $templateProcessor = new TemplateProcessor($pathToModel);

        $cliente = Cliente::find($request->cliente_id);
        $templateProcessor->setValue('cliente.nome', $cliente->nome);
        $templateProcessor->setValue('cliente.sobrenome', $cliente->sobrenome);
        $templateProcessor->setValue('contrato.valor_emprestimo', number_format($contrato->valor_emprestimo, 2, ',', '.'));
        $templateProcessor->setValue('contrato.valor_receber', number_format($contrato->valor_receber, 2, ',', '.'));
        $templateProcessor->setValue('contrato.dtvenc', $contrato->dtvenc->format('d/m/Y'));
        $templateProcessor->setValue('bem.nome', $request->nome);
        $templateProcessor->setValue('bem.valor', number_format($request->valor, 2, ',', '.'));

        // Caminhos temporários
        $tempDocx = storage_path("app/temp/contrato_{$hash}.docx");
        $tempHtml = storage_path("app/temp/contrato_{$hash}.html");

        // Salva como .docx temporário
        $templateProcessor->saveAs($tempDocx);

        // Converte para HTML
        $phpWord = IOFactory::load($tempDocx);
        $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
        $htmlWriter->save($tempHtml);

        // Converte para PDF
        $htmlContent = file_get_contents($tempHtml);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Salva o PDF final
        $nomeArquivo = "contratos/contrato_{$hash}.pdf";
        $outputPath = storage_path("app/public/{$nomeArquivo}");
        file_put_contents($outputPath, $dompdf->output());

        // Atualiza o contrato com o caminho do PDF
        $contrato->documento_path = $nomeArquivo;
        $contrato->save();

        // Limpeza dos temporários
        unlink($tempDocx);
        unlink($tempHtml);

        // Log da transação
        $transaction = new Transaction();
        $transaction->tipo = "Novo Contrato";
        $transaction->desc = "Contrato cadastrado no nome de {$cliente->nome} {$cliente->sobrenome}.";
        $transaction->contrato_id = $contrato->id;
        $transaction->save();

        // Salva o bem
        $bem = new Bem();
        $bem->nome = $request->nome;
        $bem->desc = $request->desc;
        $bem->valor = $request->valor;
        $bem->tipo_bem_id = $request->tipo_bem_id;
        $bem->contrato_id = $contrato->id;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('bens', 'public');
            $bem->foto = $path;
        }

        $bem->save();

        $contrato->load('bem');

        return response()->json([
            'message' => 'Novo contrato cadastrado com sucesso!',
            'contrato' => $contrato
        ], 201);
    }

    public function ativa($id)
    {
        $contrato = Contrato::find($id);
        $contrato->status = "Ativo";
        $contrato->save();

        $valorFormatado = number_format($contrato->valor_emprestimo, 2, ',', '.');

        $transaction = new Transaction();
        $transaction->tipo = "Saida";
        $transaction->valor = $contrato->valor_emprestimo;
        $transaction->desc = "Compra de bem no valor de R$ {$valorFormatado} do contrato #{$contrato->contrato_hash} - cliente {$contrato->cliente->nome} {$contrato->cliente->sobrenome}.";
        $transaction->contrato_id = $contrato->id;
        $transaction->save();

        $saldo = Saldo::first();
        $saldo->valor = $saldo->valor - $contrato->valor_emprestimo;
        $saldo->save();

        return response()->json(['contrato' => $contrato], 200);
    }


    public function finaliza($id, Request $request)
    {
        $contrato = Contrato::find($id);
        $contrato->status = "Finalizado";
        $contrato->valor_recebido = $contrato->valor_receber;
        $contrato->dtrec = now();
        $contrato->save();

        $valorFormatado = number_format($contrato->valor_recebido, 2, ',', '.');

        $transaction = new Transaction();
        $transaction->tipo = "Entrada";
        $transaction->valor = $contrato->valor_recebido;
        $transaction->desc = "Pagamento no valor de R$ {$valorFormatado} do contrato #{$contrato->contrato_hash} pelo cliente {$contrato->cliente->nome} {$contrato->cliente->sobrenome}.";
        $transaction->contrato_id = $contrato->id;
        $transaction->save();

        $saldo = Saldo::first();
        $saldo->valor = $saldo->valor + $contrato->valor_recebido;
        $saldo->save();

        return response()->json(['contrato' => $contrato], 200);
    }

    public function inadimplente($id, Request $request)
    {
        $contrato = Contrato::find($id);
        $contrato->status = "Inadimplente";
        $contrato->save();

        $transaction = new Transaction();
        $transaction->tipo = "Indadimplência de Contrato";
        $transaction->valor = $contrato->valor_recebido;
        $transaction->desc = "Contrato #{$contrato->contrato_hash} do cliente {$contrato->cliente->nome} {$contrato->cliente->sobrenome} foi contabilizado como Inadimplente.";
        $transaction->contrato_id = $contrato->id;
        $transaction->save();

        return response()->json(['contrato' => $contrato], 200);
    }

    public function prolonga($id, Request $request)
    {
        $contrato = Contrato::find($id);
        $contrato->status = "Prolongado";

        $dtvenc = $request->dtvenc;
        $formattedDate = Carbon::createFromFormat('d/m/y', $dtvenc);
        $contrato->dtvenc = $formattedDate;

        $multa = ($contrato->valor_receber * $request->multa) / 100;
        $saldo = Saldo::first();
        $saldo->valor = $saldo->valor + $multa;
        $saldo->save();
        $contrato->save();

        $valorFormatado = number_format($multa, 2, ',', '.');
        $dataFormatada = $formattedDate->format('d/m/Y');

        $transaction = new Transaction();
        $transaction->tipo = "Prolongamento de Contrato";
        $transaction->valor = $contrato->valor_recebido;
        $transaction->desc = "Contrato #{$contrato->contrato_hash} pelo cliente {$contrato->cliente->nome} {$contrato->cliente->sobrenome} pelo motivo de que {$request->desc}. Nova data de vencimento é: {$dataFormatada}. Multa pago no valor de ${valorFormatado}.";
        $transaction->contrato_id = $contrato->id;
        $transaction->save();

        return response()->json(['contrato' => $contrato], 200);
    }

    public function marcarComoVencido($id)
    {
        $contrato = Contrato::find($id);

        if (!$contrato) {
            return response()->json(['error' => 'Contrato não encontrado.'], 404);
        }

        $contrato->status = 'Vencido';
        $contrato->save();

        return response()->json(['message' => 'Contrato marcado como vencido com sucesso.', 'contrato' => $contrato]);
    }

    public function uploadAssinaContrato(Request $request, $id)
    {
        $contrato = Contrato::find($id);

        if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
            return response()->json(['message' => 'Arquivo inválido.'], 400);
        }

        $filePath = $request->file('file')->store('modelos_contratos', 'public');

        $contrato->assinado_path = $filePath;
        $contrato->save();

        $contrato->load('bem');

        return response()->json([
            'message' => 'Arquivo enviado com sucesso!',
            'contrato' => $contrato,
        ]);
    }
}
