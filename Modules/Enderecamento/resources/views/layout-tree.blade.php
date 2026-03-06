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

        /* Minimalist Modal Setup */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(2px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-card {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
        }

        .modal-header h3 {
            margin: 0;
            color: var(--text-main);
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-close {
            background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer; transition: 0.2s;
        }
        .modal-close:hover { color: #ef4444; }

        .modal-body {
            padding: 1.5rem;
        }

        .simple-input-group {
            display: flex; flex-direction: column; gap: 0.4rem; margin-bottom: 1rem;
        }
        .simple-input-group label {
            font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.03em;
        }
        .simple-input-group input, .simple-input-group select {
            padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem;
        }
        
        .row-twos {
            display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;
        }

        .modal-footer {
            padding: 1.25rem 1.5rem;
            background: #f8fafc;
            border-top: 1px solid var(--border);
            display: flex; justify-content: flex-end; gap: 0.75rem;
        }

        /* Inline Tree Button */
        .btn-tree-add {
            background: #e0f2fe; color: #0284c7; border: 1px dashed #7dd3fc; border-radius: 4px; padding: 0.2rem 0.6rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.3rem; margin-left: 0.75rem; opacity: 0.8; transition: 0.2s;
        }
        .btn-tree-add:hover { opacity: 1; background: #bae6fd; }

        .sql-output-container {
            margin-top: 1rem; position: relative; display: none;
        }
        
        .sql-textarea {
            width: 100%; height: 200px; font-family: monospace; padding: 1rem; background: #1e293b; color: #e2e8f0; border: none; border-radius: 6px; resize: vertical; margin-bottom: 0px;
        }

        .btn-copy-sql {
            position: absolute; top: 10px; right: 10px; background: var(--primary); color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer; font-size: 0.8rem; display: flex; align-items: center; gap: 0.4rem;
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
        <button class="btn-tree-outline" style="color: var(--primary); border-color: var(--primary); margin-left: auto;" onclick="openWizzard('')">
            <i class="ph ph-plus-circle"></i> Gerar na Raiz
        </button>
    </div>

    <!-- Wizzard Minimalist Modal -->
    <div class="modal-overlay" id="wizzardModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="ph ph-magic-wand" style="color:var(--primary)"></i> Assistente Lógico</h3>
                <button class="modal-close" onclick="closeWizzard()"><i class="ph ph-x"></i></button>
            </div>
            <div class="modal-body" style="padding-bottom: 0;">
                <p id="wizzardSubtitle" style="font-size: 0.85rem; color: var(--text-muted); margin-top:0; margin-bottom: 1.5rem;">Gerando de forma encadeada baseada no layout atual.</p>
                
                <input type="hidden" id="wizParentId" value="">

                <div class="simple-input-group">
                    <label>Tipo de Divisão</label>
                    <select id="wizTipo">
                        <option value="1">1 - Rua / Corredor</option>
                        <option value="2">2 - Prédio / Estante</option>
                        <option value="3">3 - Nível / Andar</option>
                        <option value="4">4 - Vão / Posição</option>
                    </select>
                </div>

                <div class="row-twos">
                    <div class="simple-input-group">
                        <label title="Dica: Use 001 para auto preenchimento (ex: 005)">Início (De)</label>
                        <input type="text" id="wizInicio" placeholder="Ex: 01" value="01">
                    </div>
                    <div class="simple-input-group">
                        <label>Fim (Até)</label>
                        <input type="text" id="wizFim" placeholder="Ex: 10" value="10">
                    </div>
                </div>

                <div class="sql-output-container" id="sqlOutputContainer">
                    <button class="btn-copy-sql" onclick="copySql()"><i class="ph ph-copy"></i> Copiar</button>
                    <textarea id="sqlOutputContent" class="sql-textarea" readonly></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-tree-outline" onclick="closeWizzard()">Cancelar</button>
                <button class="btn-ajustar" onclick="generateBatchSql()" id="btnGenerateSql" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); width: auto;">
                    <i class="ph ph-code"></i> Retornar Script SQL
                </button>
            </div>
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
                const aliasBadge = (node.alias && !isAliasSame) ? `<span class="node-alias" title="Alias">${node.alias}</span>` : '';
                const formatado = (node.formatado && !isFormatadoSame) ? `<span title="Formatado">(${node.formatado})</span>` : '';
                const maxCub = node.max_cubagem ? `<span class="badge-cubagem"><i class="ph ph-cube"></i> Max: ${node.max_cubagem}</span>` : '';
                
                const addBtnHtml = `<button class="btn-tree-add" onclick="openWizzard('${node.id}', '${node.nome}')" title="Gerar Filhos"><i class="ph ph-plus"></i></button>`;

                return `
                    <div class="leaf-node">
                        <i class="ph ph-check-circle leaf-icon"></i>
                        <div class="leaf-info">
                            <div style="display:flex; align-items:center;">
                                <strong class="node-title">${node.nome}</strong> ${formatado} ${aliasBadge} ${addBtnHtml}
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

            const addGrpBtnHtml = `<button class="btn-tree-add" onclick="event.preventDefault(); openWizzard('${node.id}', '${node.nome}')" title="Gerar Filhos"><i class="ph ph-plus"></i> Add</button>`;

            return `
                <details class="tree-node">
                    <summary class="tree-summary">
                        <i class="ph ph-caret-right node-icon"></i>
                        <i class="ph ph-folder" style="color: #64748b;"></i>
                        <span class="node-title">${node.nome}</span>
                        ${addGrpBtnHtml}
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
        // WIZZARD SCRIPT LOGIC
        // ==========================================

        function openWizzard(baseParentId, parentName = '') {
            document.getElementById('sqlOutputContainer').style.display = 'none';
            document.getElementById('sqlOutputContent').value = '';

            document.getElementById('wizParentId').value = baseParentId;
            
            if (baseParentId !== '') {
                document.getElementById('wizzardSubtitle').innerHTML = `Instanciando Nível-Filho para herdar dinamicamente o nó <strong style="color:var(--primary)">${parentName}</strong>.`;
            } else {
                document.getElementById('wizzardSubtitle').innerHTML = `Injetando um Nível diretamente na Raiz Principal da árvore.`;
            }

            document.getElementById('wizzardModal').style.display = 'flex';
        }

        function closeWizzard() {
            document.getElementById('wizzardModal').style.display = 'none';
        }

        async function generateBatchSql() {
            const baseId = document.getElementById('wizParentId').value;
            const initValue = document.getElementById('wizInicio').value;
            const fimValue = document.getElementById('wizFim').value;
            const tipoValue = document.getElementById('wizTipo').value;

            if (!initValue || !fimValue) {
                showToast("Preencha De/Até corretamente");
                return;
            }

            const payloadNiveis = [];
            payloadNiveis.push({
                tipo_componente: parseInt(tipoValue),
                prefixo: '',
                sufixo: '',
                separador: '-',
                inicio: initValue,
                fim: fimValue
            });

            const btn = document.getElementById('btnGenerateSql');
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Gerando...';
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
                        base_parent_id: baseId,
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
                console.error('Error:', error);
                alert("Falha de conexão.");
            } finally {
                btn.innerHTML = '<i class="ph ph-code"></i> Retornar Script SQL';
                btn.disabled = false;
            }
        }

        function copySql() {
            const sqlText = document.getElementById('sqlOutputContent');
            sqlText.select();
            sqlText.setSelectionRange(0, 999999); 
            document.execCommand("copy");
            showToast("Script copiado! Atualize a página após aplicar o db.");
            window.getSelection().removeAllRanges();
        }

        // ==========================================
        
        async function loadTree() {
            try {
                const response = await fetch(`/api/enderecamentos/layout-fisico?tenant_id=${tenantId}&armazem_id=${armazemId}&enderecamento_id=${enderecamentoId}`);
                const result = await response.json();
                
                if (result.success) {
                    const treeData = buildTree(result.data);
                    renderTree(treeData);
                    populateParentSelect(treeData);
                } else {
                    showError(result.message || 'Erro ao carregar árvore de layout.');
                }
            } catch (error) {
                console.error('Network Error:', error);
                showError('Falha de conexão com o servidor ao carregar Layout Físico.');
            }
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
