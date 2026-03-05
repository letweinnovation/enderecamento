@extends('enderecamento::layouts.master')

@push('styles')
    <style>
        .layout-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: white;
            border: 1px solid var(--border);
            color: var(--text-main);
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .header-title-box h1 {
            font-size: 1.25rem;
            margin: 0;
            color: var(--text-main);
            font-weight: 600;
        }

        .breadcrumbs {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Tree Styles */
        .tree-container {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.5rem;
            min-height: 400px;
        }

        details.tree-node {
            margin: 4px 0 4px 1rem;
        }
        
        /* O primeiro não precisa da margem esquerda extra */
        .tree-root > details.tree-node {
            margin-left: 0;
        }

        summary.tree-summary {
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.1s;
            user-select: none;
            font-size: 0.9rem;
            color: var(--text-main);
        }

        summary.tree-summary:hover {
            background: #f8fafc;
        }
        
        summary.tree-summary::marker {
            display: none;
            content: "";
        }
        
        summary.tree-summary::-webkit-details-marker {
            display: none;
        }

        .node-icon {
            color: var(--text-muted);
            font-size: 1.1rem;
            transition: transform 0.2s;
        }
        
        details[open] > summary > .node-icon {
            transform: rotate(90deg);
        }

        .node-title {
            font-weight: 500;
        }

        .node-alias {
            font-family: monospace;
            color: var(--primary);
            background: #f0fdf4;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8rem;
            border: 1px solid #bbf7d0;
        }

        .leaf-node {
            display: flex;
            align-items: center;
            padding: 0.5rem 0.5rem 0.5rem 2.5rem;
            margin: 2px 0;
            border-radius: 6px;
            font-size: 0.9rem;
            border-left: 2px solid transparent;
        }

        .leaf-node:hover {
            background: #f8fafc;
            border-left-color: var(--primary);
        }

        .leaf-icon {
            color: #10b981;
            margin-right: 0.5rem;
        }

        .leaf-info {
            display: flex;
            flex-direction: column;
        }

        .leaf-badges {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.25rem;
            font-size: 0.75rem;
        }
        
        .badge-cubagem {
            color: #64748b;
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .loading-tree {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4rem;
            color: var(--text-muted);
        }

        .loading-tree i {
            font-size: 2rem;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
            color: var(--primary);
        }
        
        .tree-controls {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .btn-tree-outline {
            background: white;
            border: 1px solid var(--border);
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-main);
        }
        
        .btn-tree-outline:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
    </style>
@endpush

@section('content')
    <div class="layout-header">
        <a href="/enderecamentos" class="btn-back" title="Voltar para Endereçamentos">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="header-title-box">
            <h1>Layout Físico</h1>
            <div class="breadcrumbs">
                {{ $tenant->name ?? 'Tenant' }} <i class="ph ph-caret-right"></i> 
                {{ $armazem->nome ?? 'Armazém' }} <i class="ph ph-caret-right"></i> 
                <strong>{{ $enderecamento->Formatacao ?? $enderecamento->Descricao ?? 'Endereçamento' }}</strong>
            </div>
        </div>
    </div>

    <div class="tree-controls">
        <button class="btn-tree-outline" onclick="expandAll()">
            <i class="ph ph-arrows-out"></i> Expandir Tudo
        </button>
        <button class="btn-tree-outline" onclick="collapseAll()">
            <i class="ph ph-arrows-in"></i> Recolher Tudo
        </button>
    </div>

    <div class="tree-container" id="treeContainer">
        <div class="loading-tree" id="treeLoading">
            <i class="ph ph-spinner"></i>
            <span>Carregando árvore de endereços...</span>
        </div>
        <div class="tree-root" id="treeRoot" style="display: none;"></div>
    </div>
@endsection

@push('scripts')
    <script>
        const tenantId = '{{ $tenantId }}';
        const armazemId = '{{ $armazemId }}';
        const enderecamentoId = '{{ $enderecamentoId }}';

        document.addEventListener('DOMContentLoaded', fetchLayoutTree);

        async function fetchLayoutTree() {
            try {
                const response = await fetch(`/api/enderecamentos/layout-fisico?tenant_id=${tenantId}&armazem_id=${armazemId}&enderecamento_id=${enderecamentoId}`);
                const result = await response.json();

                if (result.success) {
                    buildTree(result.data);
                } else {
                    showError(result.message || 'Erro ao carregar árvore.');
                }
            } catch (error) {
                console.error('Error fetching layout:', error);
                showError('Falha de conexão ao buscar árvore.');
            }
        }

        function buildTree(flatData) {
            const rootEl = document.getElementById('treeRoot');
            const map = {};
            const roots = [];

            // 1) Initialize map
            flatData.forEach(node => {
                map[node.id] = { ...node, children: [] };
            });

            // 2) Build hierarchy
            flatData.forEach(node => {
                if (node.parent_id === null || node.parent_id === undefined) {
                    roots.push(map[node.id]);
                } else {
                    if (map[node.parent_id]) {
                        map[node.parent_id].children.push(map[node.id]);
                    } else {
                        // Parent is missing from data, treat as root to avoid orphan loss
                        roots.push(map[node.id]);
                    }
                }
            });

            // 3) Render HTML recursively
            if (roots.length === 0) {
                rootEl.innerHTML = '<div style="color: var(--text-muted); padding: 2rem; text-align: center;">Nenhum endereço encontrado para este layout.</div>';
            } else {
                let html = '';
                roots.forEach(r => {
                    html += generateNodeHtml(r);
                });
                rootEl.innerHTML = html;
            }

            document.getElementById('treeLoading').style.display = 'none';
            rootEl.style.display = 'block';
        }

        function generateNodeHtml(node) {
            // Is it a leaf node? (No children or is explicitly enderecavel and has no children)
            if (node.children.length === 0) {
                const aliasBadge = node.alias ? `<span class="node-alias">${node.alias}</span>` : '';
                const formatado = node.formatado ? `(${node.formatado})` : '';
                const maxCub = node.max_cubagem ? `<span class="badge-cubagem"><i class="ph ph-cube"></i> Max: ${node.max_cubagem}</span>` : '';
                
                return `
                    <div class="leaf-node">
                        <i class="ph ph-check-circle leaf-icon"></i>
                        <div class="leaf-info">
                            <div>
                                <strong class="node-title">${node.nome}</strong> ${formatado} ${aliasBadge}
                            </div>
                            <div class="leaf-badges">
                                ${maxCub}
                            </div>
                        </div>
                    </div>
                `;
            }

            // Group Node (has children)
            let childrenHtml = '';
            node.children.forEach(child => {
                childrenHtml += generateNodeHtml(child);
            });

            return `
                <details class="tree-node">
                    <summary class="tree-summary">
                        <i class="ph ph-caret-right node-icon"></i>
                        <i class="ph ph-folder" style="color: #64748b;"></i>
                        <span class="node-title">${node.nome}</span>
                    </summary>
                    ${childrenHtml}
                </details>
            `;
        }

        function expandAll() {
            document.querySelectorAll('details.tree-node').forEach(detail => detail.open = true);
        }

        function collapseAll() {
            document.querySelectorAll('details.tree-node').forEach(detail => detail.open = false);
        }

        function showError(msg) {
            document.getElementById('treeContainer').innerHTML = `
                <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                    <i class="ph ph-warning" style="font-size: 2rem; color: #ef4444; margin-bottom: 1rem;"></i>
                    <p>${msg}</p>
                </div>
            `;
        }
    </script>
@endpush
