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
}
