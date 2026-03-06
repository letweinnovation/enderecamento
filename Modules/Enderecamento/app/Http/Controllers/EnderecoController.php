<?php

namespace Modules\Enderecamento\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnderecoController extends Controller
{
    /**
     * Display the main endereçamento page.
     */
    public function index()
    {
        return view('enderecamento::index');
    }

    /**
     * Search tenants by name, ID or domain (autocomplete).
     */
    public function searchTenants(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 3) {
            return response()->json([]);
        }

        try {
            $results = DB::connection('gace')->table('tenant')
                ->select('id', 'name', 'domain')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('id', 'like', "%{$query}%")
                      ->orWhere('domain', 'like', "%{$query}%");
                })
                ->limit(10)
                ->get();

            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Tenant search error: '.$e->getMessage());

            return response()->json([]);
        }
    }

    /**
     * Search armazéns by tenant ID (gtimeta_mcid).
     */
    public function searchArmazens(Request $request)
    {
        $tenantId = $request->get('tenant_id');

        if (! $tenantId) {
            return response()->json(['success' => false, 'message' => 'Tenant ID obrigatório.']);
        }

        try {
            $armazens = DB::connection('gace')->table('armazem')
                ->select(
                    'Id as id', 
                    'nome', 
                    'IDEXTERNO as idExterno', 
                    'IdTipoArmazem as tipoArmazem', 
                    'RegStatus as regStatus'
                )
                ->where('gtimeta_mcid', $tenantId)
                ->orderBy('nome')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $armazens,
                'total' => $armazens->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Armazem search error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Erro ao buscar armazéns.']);
        }
    }

    /**
     * Search endereçamentos by armazém ID.
     */
    public function searchEnderecos(Request $request)
    {
        $tenantId = $request->get('tenant_id');
        $armazemId = $request->get('armazem_id');

        if (! $tenantId || ! $armazemId) {
            return response()->json(['success' => false, 'message' => 'Tenant ID e Armazém ID são obrigatórios.']);
        }

        try {
            $enderecos = DB::connection('gace')
                ->table('enderecamento')
                ->join('armazemenderecamento', function($join) use ($tenantId) {
                    $join->on('enderecamento.Id', '=', 'armazemenderecamento.IdEnderecamento');
                })
                ->select(
                    'enderecamento.Id as id',
                    'enderecamento.Descricao as descricao',
                    'enderecamento.IDEXTERNO as idExterno',
                    'enderecamento.tipoEnderecamento',
                    'enderecamento.formatacao',
                    'enderecamento.RegStatus as regStatus',
                    'enderecamento.indConsiderarCubagem',
                    'enderecamento.CUBAGEMPADRAO'
                )
                ->where('armazemenderecamento.IdArmazem', $armazemId)
                ->where('enderecamento.gtimeta_mcid', $tenantId)
                ->orderBy('enderecamento.formatacao')
                ->orderBy('enderecamento.Descricao')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $enderecos,
                'total' => $enderecos->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Enderecamento search error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Erro ao buscar endereçamentos.']);
        }
    }

    /**
     * Return the view for the physical layout tree.
     */
    public function layoutFisico($tenantId, $armazemId, $enderecamentoId)
    {
        // Pega nomes apenas para exibir no cabeçalho
        $tenant = DB::connection('gace')->table('tenant')->where('id', $tenantId)->first();
        $armazem = DB::connection('gace')->table('armazem')->where('Id', $armazemId)->first();
        $enderecamento = DB::connection('gace')->table('enderecamento')->where('Id', $enderecamentoId)->first();

        return view('enderecamento::layout-tree', compact('tenantId', 'armazemId', 'enderecamentoId', 'tenant', 'armazem', 'enderecamento'));
    }

    /**
     * API to fetch purely layout physical tree items.
     */
    public function getLayoutFisico(Request $request)
    {
        $tenantId = $request->get('tenant_id');
        $armazemId = $request->get('armazem_id');
        $enderecamentoId = $request->get('enderecamento_id');

        if (! $tenantId || ! $armazemId || ! $enderecamentoId) {
            return response()->json(['success' => false, 'message' => 'Parâmetros obrigatórios não informados.']);
        }

        try {
            $layout = DB::connection('gace')
                ->table('layout_endereco_fisico')
                ->select(
                    'ID as id',
                    'ID_LAYOUT_ENDERECO_FISICO_PAI as parent_id',
                    'ENDERECO as nome',
                    'ENDERECO_FORMATADO as formatado',
                    'ALIAS_ENDERECO as alias',
                    'IND_ENDERECAVEL as enderecavel',
                    'LADO_ENDERECO as lado',
                    'CUBAGEM_MAXIMA as max_cubagem'
                )
                ->where('GTIMETA_MCID', $tenantId)
                ->where('ID_ARMAZEM', $armazemId)
                ->where('ID_ENDERECAMENTO', $enderecamentoId)
                ->where('IND_DESABILITADO', 0)
                ->orderBy('ID_LAYOUT_ENDERECO_FISICO_PAI')
                ->orderBy('ENDERECO', 'ASC')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $layout,
                'total' => $layout->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Layout Fisico API error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao buscar layout físico.']);
        }
    }

    /**
     * Generate SQL script for batch inserting physical layout addresses based on levels.
     */
    public function generateLayoutScript(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        $armazemId = $request->input('armazem_id');
        $enderecamentoId = $request->input('enderecamento_id');
        $niveis = $request->input('niveis', []);
        $baseParentId = $request->input('base_parent_id');

        if (!$tenantId || !$armazemId || !$enderecamentoId || empty($niveis)) {
            return response()->json(['success' => false, 'message' => 'Parâmetros inválidos.']);
        }

        try {
            $maxIdObj = DB::connection('gace')
                ->table('layout_endereco_fisico')
                ->selectRaw('MAX(ID) as max_id')
                ->first();
            
            $currentId = ($maxIdObj && $maxIdObj->max_id) ? (int)$maxIdObj->max_id + 1 : 1;
            
            $now = now()->format('Y-m-d H:i:s');
            $userId = auth()->id() ?? 'system';

            $sqlLines = [];
            $sqlLines[] = "-- Script de Geração de Layout Físico em Lote";
            $sqlLines[] = "-- Armazem ID: {$armazemId} | Enderecamento ID: {$enderecamentoId}";
            $sqlLines[] = "-- Atenção: Valide os IDs antes de executar se houver acesso concorrente pesado ao WMS.";
            $sqlLines[] = "BEGIN;";

            $generateNodes = function($levelIndex, $parentId, $parentNameFormat) use (&$generateNodes, &$currentId, &$sqlLines, $niveis, $tenantId, $armazemId, $enderecamentoId, $now, $userId) {
                if (!isset($niveis[$levelIndex])) {
                    return;
                }

                $nivel = $niveis[$levelIndex];
                
                $inicio = $nivel['inicio'] ?? 1;
                $fim = $nivel['fim'] ?? 1;
                
                if (is_numeric($inicio) && is_numeric($fim)) {
                    $items = range((int)$inicio, (int)$fim);
                } else {
                    $items = range($inicio, $fim);
                }

                foreach ($items as $item) {
                    $formattedItem = $item;
                    if (is_numeric($item) && isset($nivel['casas_decimais']) && (int)$nivel['casas_decimais'] > 0) {
                        $formattedItem = str_pad($item, (int)$nivel['casas_decimais'], '0', STR_PAD_LEFT);
                    }

                    $sigla = ($nivel['prefixo'] ?? '') . $formattedItem . ($nivel['sufixo'] ?? '');
                    
                    if ($parentNameFormat === '') {
                        $formatado = $sigla;
                    } else {
                        $separador = $nivel['separador'] ?? '';
                        $formatado = $parentNameFormat . $separador . $sigla;
                    }
                    
                    $myId = $currentId++;
                    
                    $tipoComponente = (int)($nivel['tipo_componente'] ?? 1);
                    
                    // A última folha sempre será a endereçável
                    $enderecavel = ($levelIndex === count($niveis) - 1) ? 1 : 0;
                    
                    $parentVal = $parentId === null ? 'NULL' : $parentId;

                    $sqlLines[] = "INSERT INTO layout_endereco_fisico " . 
                        "(ID, ID_ARMAZEM, ID_ENDERECAMENTO, ID_LAYOUT_ENDERECO_FISICO_PAI, TIPO_COMPONENTE, ENDERECO, ENDERECO_FORMATADO, ALIAS_ENDERECO, IND_DESABILITADO, IND_ENDERECO_PICKING, IND_ENDERECAVEL, GTI_MODIFIED_AT, GTI_MODIFIED_BY, GTIMETA_MCID, GTI_VERSION) " .
                        "VALUES ({$myId}, {$armazemId}, {$enderecamentoId}, {$parentVal}, {$tipoComponente}, '{$formatado}', '{$formatado}', '{$formatado}', 0, 0, {$enderecavel}, '{$now}', '{$userId}', '{$tenantId}', 0);";

                    $generateNodes($levelIndex + 1, $myId, $formatado);
                }
            };

            $initialParentId = null;
            $initialParentFormat = '';

            if ($baseParentId) {
                $baseParentObj = DB::connection('gace')
                    ->table('layout_endereco_fisico')
                    ->select('ENDERECO_FORMATADO')
                    ->where('ID', $baseParentId)
                    ->first();
                
                if ($baseParentObj) {
                    $initialParentId = $baseParentId;
                    $initialParentFormat = $baseParentObj->ENDERECO_FORMATADO;
                }
            }

            $generateNodes(0, $initialParentId, $initialParentFormat);

            $sqlLines[] = "COMMIT;";

            return response()->json([
                'success' => true,
                'sql' => implode("\n", $sqlLines)
            ]);
        } catch (\Exception $e) {
            Log::error('Layout Fisico Batch Generate Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao gerar script SQL.']);
        }
    }
}
