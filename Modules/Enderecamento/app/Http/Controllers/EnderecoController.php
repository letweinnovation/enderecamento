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
                    'IND_ENDERECAVEL as is_enderecavel',
                    'LADO_ENDERECO as lado',
                    'CUBAGEM_MAXIMA as max_cubagem',
                    'TIPO_COMPONENTE as tipo_componente'
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
     * Preview new nodes visually on the frontend tree without saving them yet.
     */
    public function previewNodes(Request $request)
    {
        $niveis = $request->input('niveis', []);
        $baseParentId = $request->input('base_parent_id');

        if (empty($niveis)) {
            return response()->json(['success' => false, 'message' => 'Parâmetros inválidos.']);
        }

        try {
            $createdNodes = [];
            
            // Gerador recursivo
            $generateNodes = function($levelIndex, $parentId, $parentNameFormat) use (&$generateNodes, &$createdNodes, $niveis) {
                if (!isset($niveis[$levelIndex])) {
                    return;
                }

                $nivel = $niveis[$levelIndex];
                
                $inicio = (string)($nivel['inicio'] ?? '1');
                $fim = (string)($nivel['fim'] ?? '1');
                
                $padLength = 0;
                if (strlen($inicio) > 1 && str_starts_with($inicio, '0')) {
                    $padLength = strlen($inicio); // Detecção nativa do painel original do usuario
                }

                if (is_numeric($inicio) && is_numeric($fim)) {
                    $items = range((int)$inicio, (int)$fim);
                } else {
                    $items = range($inicio, $fim);
                }

                foreach ($items as $item) {
                    $formattedItem = (string)$item;
                    if ($padLength > 0 && is_numeric($item)) {
                        $formattedItem = str_pad($item, $padLength, '0', STR_PAD_LEFT);
                    }

                    $sigla = ($nivel['prefixo'] ?? '') . $formattedItem . ($nivel['sufixo'] ?? '');
                    
                    if ($parentNameFormat === '') {
                        $formatado = $sigla;
                    } else {
                        $separador = $nivel['separador'] ?? '-';
                        $formatado = $parentNameFormat . $separador . $sigla;
                    }
                    
                    $myId = 'draft_' . uniqid() . rand(100, 999);
                    
                    // A última folha da ramificação em lote criada será a endereçável
                    $enderecavel = ($levelIndex === count($niveis) - 1) ? 1 : 0;
                    
                    $node = [
                        'id' => $myId,
                        'parent_id' => $parentId === null ? null : (string)$parentId,
                        'nome' => $sigla,
                        'formatado' => $formatado,
                        'alias' => $formatado,
                        'is_enderecavel' => $enderecavel,
                        'lado' => null,
                        'max_cubagem' => null,
                        'tipo_componente' => (int)($nivel['tipo_componente'] ?? 1),
                        'is_new' => true,
                    ];
                    
                    $createdNodes[] = $node;

                    // Desce para o nível filho injetando este node draft como parentId
                    $generateNodes($levelIndex + 1, $myId, $formatado);
                }
            };

            $initialParentId = null;
            $initialParentFormat = '';

            // Se injetamos dentro de um nó existente, busca ele do DB pra herdar string e ID
            if ($baseParentId && !str_starts_with((string)$baseParentId, 'draft_')) {
                $baseParentObj = DB::connection('gace')
                    ->table('layout_endereco_fisico')
                    ->select('ENDERECO_FORMATADO')
                    ->where('ID', $baseParentId)
                    ->first();
                
                if ($baseParentObj) {
                    $initialParentId = $baseParentId;
                    $initialParentFormat = $baseParentObj->ENDERECO_FORMATADO;
                }
            } else if ($baseParentId && str_starts_with((string)$baseParentId, 'draft_')) {
               // Em uma tree altamente conectada do frontend isso é preenchido manualmente
               // Mas vamos exigir os root nodes base por ora ou pass formatado no request
               $initialParentId = $baseParentId;
               $initialParentFormat = $request->input('base_parent_format', '');
            }

            $generateNodes(0, $initialParentId, $initialParentFormat);

            return response()->json([
                'success' => true,
                'data' => $createdNodes
            ]);
        } catch (\Exception $e) {
            Log::error('Layout Fisico Preview Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao gerar preview.']);
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
        $nodes = $request->input('nodes', []);

        if (!$tenantId || !$armazemId || !$enderecamentoId || empty($nodes)) {
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

            // 1. Sort nodes by depth (number of dashes in formatado) 
            // to ensure parents are processed before children.
            usort($nodes, function($a, $b) {
                return substr_count($a['formatado'], '-') <=> substr_count($b['formatado'], '-');
            });

            // 2. Pre-fetch TIPO_COMPONENTE mapping from DB for this endereçamento
            // to use as reference for draft nodes.
            $existingNodes = DB::connection('gace')
                ->table('layout_endereco_fisico')
                ->select('ENDERECO_FORMATADO', 'TIPO_COMPONENTE')
                ->where('ID_ENDERECAMENTO', $enderecamentoId)
                ->where('GTIMETA_MCID', $tenantId)
                ->whereNotNull('ENDERECO_FORMATADO')
                ->get();

            $typeMapping = [];
            foreach ($existingNodes as $nodeObj) {
                $d = substr_count($nodeObj->ENDERECO_FORMATADO, '-');
                if (!isset($typeMapping[$d])) {
                    $typeMapping[$d] = $nodeObj->TIPO_COMPONENTE;
                }
            }

            $sqlLines = [];
            $sqlLines[] = "-- Script de Consolidação de Layout Físico Visual";
            $sqlLines[] = "-- Armazem ID: {$armazemId} | Enderecamento ID: {$enderecamentoId}";
            $sqlLines[] = "BEGIN;";

            $draftToRealId = [];

            foreach ($nodes as $node) {
                $myId = $currentId++;
                $draftToRealId[$node['id']] = $myId;
                
                $parentVal = 'NULL';
                if (!empty($node['parent_id'])) {
                    if (str_starts_with((string)$node['parent_id'], 'draft_')) {
                        $parentVal = $draftToRealId[$node['parent_id']] ?? 'NULL';
                    } else {
                        $parentVal = $node['parent_id'];
                    }
                }
                
                // If tipo_componente is missing or 1 (default), try to infer from mapping or siblings
                $tipoComponente = isset($node['tipo_componente']) ? (int)$node['tipo_componente'] : null;
                if (!$tipoComponente || $tipoComponente === 1) {
                    $depth = substr_count($node['formatado'], '-');
                    $tipoComponente = $typeMapping[$depth] ?? 1;
                }

                $formatado = $node['formatado'];
                $alias = $node['alias'] ?? $formatado;
                $enderecavel = (isset($node['is_enderecavel']) && $node['is_enderecavel']) ? 1 : 0;
                
                $sqlLines[] = "INSERT INTO layout_endereco_fisico " . 
                    "(ID, ID_ARMAZEM, ID_ENDERECAMENTO, ID_LAYOUT_ENDERECO_FISICO_PAI, TIPO_COMPONENTE, ENDERECO, ENDERECO_FORMATADO, ALIAS_ENDERECO, IND_DESABILITADO, IND_ENDERECO_PICKING, IND_ENDERECAVEL, GTI_MODIFIED_AT, GTI_MODIFIED_BY, GTIMETA_MCID, GTI_VERSION) " .
                    "VALUES ({$myId}, {$armazemId}, {$enderecamentoId}, {$parentVal}, {$tipoComponente}, '{$formatado}', '{$formatado}', '{$alias}', 0, 0, {$enderecavel}, '{$now}', '{$userId}', '{$tenantId}', 0);";
            }

            $sqlLines[] = "COMMIT;";

            return response()->json([
                'success' => true,
                'sql' => implode("\n", $sqlLines)
            ]);
        } catch (\Exception $e) {
            Log::error('Layout Fisico Batch Generate Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao gerar script SQL consolidado.']);
        }
    }
}
