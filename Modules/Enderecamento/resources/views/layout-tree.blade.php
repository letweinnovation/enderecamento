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

        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .info-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #f1f5f9;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .info-card-text {
            display: flex;
            flex-direction: column;
        }

        .info-card-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .info-card-value {
            font-size: 1rem;
            color: var(--text-main);
            font-weight: 500;
        }

        /* Batch Generator */
        .batch-panel {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            display: none;
        }

        .batch-level-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr auto;
            gap: 0.5rem;
            align-items: end;
            border: 1px solid #e2e8f0;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            background: white;
        }
        
        @media (max-width: 1024px) {
            .batch-level-row {
                grid-template-columns: 1fr 1fr;
            }
        }

        .batch-input-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .batch-input-group label {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .batch-input-group input, .batch-input-group select {
            padding: 0.4rem;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .btn-add-level {
            background: white;
            border: 1px dashed #cbd5e1;
            padding: 0.5rem;
            border-radius: 6px;
            width: 100%;
            cursor: pointer;
            margin-bottom: 1rem;
            transition: background 0.2s;
        }

        .btn-add-level:hover {
            background: #f1f5f9;
        }

        .btn-remove-level {
            background: #fee2e2;
            color: #ef4444;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            padding: 0.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 33px;
        }
        
        .sql-output-container {
            margin-top: 1rem;
            position: relative;
            display: none;
        }
        
        .sql-textarea {
            width: 100%;
            height: 300px;
            font-family: monospace;
            padding: 1rem;
            background: #1e293b;
            color: #e2e8f0;
            border: none;
            border-radius: 6px;
            resize: vertical;
        }

        .btn-copy-sql {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
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

    <div class="info-cards">
        <div class="info-card">
            <div class="info-card-icon">
                <i class="ph ph-buildings"></i>
            </div>
            <div class="info-card-text">
                <span class="info-card-label">Armazém</span>
                <span class="info-card-value">{{ $armazem->nome ?? '-' }}</span>
            </div>
        </div>
        <div class="info-card">
            <div class="info-card-icon">
                <i class="ph ph-map-pin"></i>
            </div>
            <div class="info-card-text">
                <span class="info-card-label">Endereçamento (Formatação)</span>
                <span class="info-card-value">{{ $enderecamento->Formatacao ?? '-' }}</span>
            </div>
        </div>
        <div class="info-card">
            <div class="info-card-icon">
                <i class="ph ph-tag"></i>
            </div>
            <div class="info-card-text">
                <span class="info-card-label">Descrição</span>
                <span class="info-card-value">{{ $enderecamento->Descricao ?? '-' }}</span>
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
        <button class="btn-tree-outline" style="color: var(--primary); border-color: var(--primary); margin-left: auto;" onclick="toggleBatchMode()">
            <i class="ph ph-magic-wand"></i> Gerador em Lote (SQL)
        </button>
    </div>

    <!-- Batch Generator Panel -->
    <div class="batch-panel" id="batchPanel">
        <h3 style="margin-top: 0; margin-bottom: 1rem; font-size: 1.1rem; color: var(--text-main);">Gerar Layout Físico em Lote</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">Crie níveis de endereços (ex: Ruas, Vãos, Posições). Defina os intervalos numéricos ou letras (A-Z) para cada subnível. A ferramenta irá construir a árvore gerando um Script SQL de <code>INSERT</code> puro.</p>
        
        <div id="levelsContainer"></div>
        
        <button class="btn-add-level" onclick="addBatchLevel()">
            <i class="ph ph-plus"></i> Adicionar Nível / Subnível
        </button>

        <div style="display: flex; gap: 1rem;">
            <button class="btn-ajustar" onclick="generateBatchSql()" id="btnGenerateSql" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="ph ph-file-sql"></i> Gerar Script SQL
            </button>
            <button class="btn-tree-outline" onclick="toggleBatchMode()">Cancelar</button>
        </div>

        <div class="sql-output-container" id="sqlOutputContainer">
            <button class="btn-copy-sql" onclick="copySql()"><i class="ph ph-copy"></i> Copiar Código</button>
            <textarea id="sqlOutputContent" class="sql-textarea" readonly></textarea>
        </div>
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

        // ==========================================
        // BATCH GENERATOR LOGIC
        // ==========================================
        let levelCount = 0;

        function toggleBatchMode() {
            const panel = document.getElementById('batchPanel');
            if (panel.style.display === 'block') {
                panel.style.display = 'none';
            } else {
                panel.style.display = 'block';
                if (levelCount === 0) {
                    addBatchLevel(); // add first level automatically
                }
            }
        }

        function addBatchLevel() {
            const container = document.getElementById('levelsContainer');
            const index = levelCount++;
            
            const div = document.createElement('div');
            div.className = 'batch-level-row';
            div.id = `level-row-${index}`;
            
            div.innerHTML = `
                <div class="batch-input-group">
                    <label>Tipo Componente</label>
                    <select class="level-tipo">
                        <option value="1">1 - Corredor</option>
                        <option value="2">2 - Prédio / Estante</option>
                        <option value="3">3 - Nível / Andar</option>
                        <option value="4">4 - Vão / Posição</option>
                    </select>
                </div>
                <div class="batch-input-group">
                    <label>Prefixo</label>
                    <input type="text" class="level-prefixo" placeholder="Ex: R" value="">
                </div>
                <div class="batch-input-group">
                    <label>Início (Ex: 1 ou A)</label>
                    <input type="text" class="level-inicio" value="1">
                </div>
                <div class="batch-input-group">
                    <label>Fim (Ex: 10 ou Z)</label>
                    <input type="text" class="level-fim" value="10">
                </div>
                <div class="batch-input-group">
                    <label>Sufixo</label>
                    <input type="text" class="level-sufixo" placeholder="Ex: A" value="">
                </div>
                <div class="batch-input-group" title="Zerar a esquerda (ex: 1 vira 01 se for 2)">
                    <label>Zeros (Pad)</label>
                    <input type="number" class="level-casas" value="2" min="1" max="10">
                </div>
                <div class="batch-input-group">
                    <label>Config Final</label>
                    <div style="display:flex; gap: 0.5rem;">
                        <input type="text" class="level-separador" placeholder="Sep: -" value="-" style="width: 50%" title="Separador do nível anterior">
                        <label style="display:flex; align-items:center; gap: 4px; white-space:nowrap; cursor:pointer;">
                            <input type="checkbox" class="level-enderecavel"> Endereçável?
                        </label>
                    </div>
                </div>
                <button class="btn-remove-level" onclick="remBatchLevel(${index})" title="Remover Nível">
                    <i class="ph ph-trash"></i>
                </button>
            `;
            container.appendChild(div);
        }

        function remBatchLevel(index) {
            const row = document.getElementById(`level-row-${index}`);
            if (row) row.remove();
        }

        async function generateBatchSql() {
            const rows = document.querySelectorAll('.batch-level-row');
            if (rows.length === 0) {
                showToast("Adicione pelo menos um nível.");
                return;
            }

            const payloadNiveis = [];
            rows.forEach((row, idx) => {
                payloadNiveis.push({
                    tipo_componente: parseInt(row.querySelector('.level-tipo').value),
                    prefixo: row.querySelector('.level-prefixo').value,
                    inicio: row.querySelector('.level-inicio').value,
                    fim: row.querySelector('.level-fim').value,
                    sufixo: row.querySelector('.level-sufixo').value,
                    casas_decimais: parseInt(row.querySelector('.level-casas').value || 0),
                    separador: row.querySelector('.level-separador').value,
                    enderecavel: row.querySelector('.level-enderecavel').checked
                });
            });

            const btn = document.getElementById('btnGenerateSql');
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Gerando SQL...';
            btn.disabled = true;

            try {
                const response = await fetch('/api/enderecamentos/layout-fisico/generate-script', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        tenant_id: tenantId,
                        armazem_id: armazemId,
                        enderecamento_id: enderecamentoId,
                        niveis: payloadNiveis
                    })
                });
                const result = await response.json();

                if (result.success) {
                    document.getElementById('sqlOutputContainer').style.display = 'block';
                    document.getElementById('sqlOutputContent').value = result.sql;
                    document.getElementById('sqlOutputContent').scrollIntoView({ behavior: 'smooth' });
                    showToast("Script gerado com sucesso!");
                } else {
                    alert('Erro: ' + result.message);
                }
            } catch (error) {
                console.error('Error generating sql:', error);
                alert("Falha de conexão.");
            } finally {
                btn.innerHTML = '<i class="ph ph-file-sql"></i> Gerar Script SQL';
                btn.disabled = false;
            }
        }

        function copySql() {
            const sqlText = document.getElementById('sqlOutputContent');
            sqlText.select();
            sqlText.setSelectionRange(0, 999999); 
            document.execCommand("copy");
            showToast("Script copiado para área de transferência!");
            window.getSelection().removeAllRanges();
        }

        // ==========================================
        
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
