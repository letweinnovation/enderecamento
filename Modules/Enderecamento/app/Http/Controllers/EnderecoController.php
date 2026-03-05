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
}
