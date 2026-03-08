@extends('enderecamento::layouts.master')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-light: #e0f2fe;
            --primary-dark: #0369a1;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-body: #f8fafc;
            --surface: rgba(255, 255, 255, 0.8);
            --border: rgba(226, 232, 240, 0.8);
            --shadow-premium: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
        }

        /* Glassmorphism utility */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-premium);
            border-radius: 20px;
        }

        .layout-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            animation: fadeInDown 0.5s ease-out;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: white;
            border: 1px solid var(--border);
            color: var(--text-main);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .btn-back:hover {
            background: var(--primary);
            color: white;
            transform: translateX(-3px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        }

        .header-title-box h1 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 700;
            background: linear-gradient(135deg, var(--text-main) 0%, #475569 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .breadcrumbs {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-card {
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: transform 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
        }

        .info-card-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary-light) 0%, #bae6fd 100%);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .info-card-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .info-card-value {
            font-size: 1.1rem;
            color: var(--text-main);
            font-weight: 600;
        }

        /* Tree Styles Overhaul */
        .tree-container {
            padding: 2rem;
            min-height: 500px;
            margin-bottom: 5rem;
        }

        .tree-root {
            border-left: 2px dashed rgba(14, 165, 233, 0.2);
            padding-left: 1rem;
        }

        details.tree-node {
            margin: 8px 0 8px 1.5rem;
            position: relative;
        }

        details.tree-node::before {
            content: '';
            position: absolute;
            left: -1.5rem;
            top: 1.25rem;
            width: 1.5rem;
            height: 2px;
            background: rgba(14, 165, 233, 0.1);
        }

        summary.tree-summary {
            cursor: pointer;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
            user-select: none;
            font-size: 0.95rem;
            background: white;
            border: 1px solid var(--border);
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        summary.tree-summary:hover {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary-dark);
        }

        summary::-webkit-details-marker { display: none; }
        summary::marker { display: none; }

        .node-icon-caret {
            font-size: 0.8rem;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--text-muted);
        }

        details[open] > summary > .node-icon-caret {
            transform: rotate(90deg);
        }

        .leaf-node {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem 0.75rem 1.5rem;
            margin: 6px 0 6px 3rem;
            border-radius: 12px;
            font-size: 0.9rem;
            background: white;
            border: 1px solid var(--border);
            position: relative;
            transition: all 0.2s ease;
        }

        .leaf-node:hover {
            border-color: var(--success);
            background: #f0fdf4;
            transform: scale(1.01);
        }

        .leaf-node::before {
            content: '';
            position: absolute;
            left: -1.5rem;
            top: 50%;
            width: 1.5rem;
            height: 2px;
            background: rgba(16, 185, 129, 0.1);
        }

        .leaf-icon-status {
            color: var(--success);
            font-size: 1.1rem;
            margin-right: 0.75rem;
        }

        /* Smart Generator Modal */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .modal-card {
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .modal-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border);
            background: white;
        }

        .modal-body {
            padding: 2rem;
            overflow-y: auto;
            background: #fcfcfc;
        }

        .generator-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 2rem;
        }

        .levels-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .level-row {
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.25rem;
            position: relative;
            animation: slideInLeft 0.3s ease-out;
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .level-row-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 0.5rem;
        }

        .level-row-title {
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-remove-level {
            color: var(--danger);
            background: #fef2f2;
            border: none;
            width: 28px; height: 28px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-remove-level:hover {
            background: var(--danger);
            color: white;
        }

        .level-inputs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .level-input-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .level-input-group label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .level-input-group input, .level-input-group select {
            padding: 0.6rem 0.8rem;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .level-input-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .preview-sidebar {
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
            position: sticky;
            top: 0;
            height: fit-content;
        }

        .preview-stat {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .preview-stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .preview-list {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1rem;
            max-height: 200px;
            overflow-y: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            line-height: 1.6;
        }

        .btn-add-level {
            background: white;
            border: 2px dashed var(--primary);
            color: var(--primary);
            padding: 1rem;
            border-radius: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-add-level:hover {
            background: var(--primary-light);
        }

        /* Action bar premium */
        .action-bar-floating {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            padding: 1rem 2.5rem;
            display: flex;
            align-items: center;
            gap: 2rem;
            z-index: 1000;
            animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes slideUp {
            from { transform: translate(-50%, 50px); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }

        .btn-save-final {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-save-final:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-smart-generate {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .btn-smart-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(14, 165, 233, 0.4);
        }

        /* Tree Node Badges */
        .node-alias {
            font-family: 'JetBrains Mono', monospace;
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .draft-badge {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #10b981;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .btn-inline-add {
            background: var(--primary-light);
            color: var(--primary);
            border: none;
            width: 24px; height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s;
            margin-left: 0.5rem;
        }

        summary:hover .btn-inline-add, .leaf-node:hover .btn-inline-add {
            opacity: 1;
        }

        /* --- MOBILE RESPONSIVENESS (iPhone) --- */
        @media (max-width: 768px) {
            .layout-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1rem;
            }

            .layout-header div:last-child {
                width: 100%;
                margin-left: 0 !important;
            }

            .btn-smart-generate {
                width: 100%;
                justify-content: center;
            }

            .info-cards {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .tree-container {
                padding: 1rem;
            }

            .tree-node, .leaf-node {
                margin-left: 0.75rem !important;
            }

            .tree-node::before, .leaf-node::before {
                width: 0.75rem;
                left: -0.75rem;
            }

            /* Modal Adjustments */
            .modal-overlay {
                padding: 0.5rem;
            }

            .modal-card {
                max-height: 95vh;
                border-radius: 12px;
            }

            .modal-header {
                padding: 1rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .generator-grid {
                grid-template-columns: 1fr;
            }

            .preview-sidebar {
                position: relative;
                top: 0;
                margin-top: 2rem;
            }

            .level-inputs {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .action-bar-floating {
                width: 90%;
                padding: 0.75rem 1rem;
                gap: 1rem;
                flex-direction: column;
                bottom: 1rem;
                border-radius: 20px;
            }

            .action-bar-floating div[style*="width: 1px"] {
                display: none;
            }

            .btn-save-final {
                width: 100%;
                justify-content: center;
            }

            .leaf-node {
                padding: 0.6rem;
            }

            .node-icon-caret {
                font-size: 0.7rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="layout-header glass-card">
        <a href="/enderecamentos" class="btn-back" title="Voltar para Endereçamentos">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="header-title-box">
            <h1>Layout Físico Inteligente</h1>
            <div class="breadcrumbs">
                <i class="ph ph-buildings"></i> {{ $tenant->name ?? 'Tenant' }} 
                <i class="ph ph-caret-right"></i> {{ $armazem->nome ?? 'Armazém' }} 
                <i class="ph ph-caret-right"></i> <strong>{{ $enderecamento->Formatacao ?? $enderecamento->Descricao ?? 'Endereçamento' }}</strong>
            </div>
        </div>
        <div style="margin-left: auto;">
            <button class="btn-smart-generate" onclick="openSmartGenerator('')">
                <i class="ph ph-sparkle"></i> Gerador Inteligente
            </button>
        </div>
    </div>

    <div class="info-cards">
        <div class="info-card glass-card">
            <div class="info-card-icon">
                <i class="ph ph-stack"></i>
            </div>
            <div class="info-card-text">
                <span class="info-card-label">Modo de Operação</span>
                <span class="info-card-value">Composite Layout</span>
            </div>
        </div>
        <div class="info-card glass-card">
            <div class="info-card-icon" style="background: #fef3c7; color: #d97706;">
                <i class="ph ph-tree-structure"></i>
            </div>
            <div class="info-card-text">
                <span class="info-card-label">Estrutura Ativa</span>
                <span class="info-card-value">{{ $enderecamento->Descricao ?? 'N/A' }}</span>
            </div>
        </div>
        <div id="treeStatsCard" class="info-card glass-card">
            <div class="info-card-icon" style="background: #e0e7ff; color: #4338ca;">
                <i class="ph ph-map-pin"></i>
            </div>
            <div class="info-card-text">
                <span class="info-card-label">Nós Carregados</span>
                <span class="info-card-value" id="nodeCountBadge">Calculando...</span>
            </div>
        </div>
    </div>

    <div class="tree-container glass-card" id="treeContainer">
        <div class="loading-tree" id="treeLoading" style="text-align: center; padding: 4rem;">
            <i class="ph ph-spinner ph-spin" style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem; display: block;"></i>
            <span style="color: var(--text-muted); font-weight: 500;">Construindo árvore de endereços...</span>
        </div>
        
        <div class="tree-root" id="treeRoot" style="display: none;"></div>
        
        <div id="treeEmptyState" style="display: none; padding: 4rem; text-align: center;">
            <div style="background: #f1f5f9; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <i class="ph ph-tree-structure" style="font-size: 2.5rem; color: #94a3b8;"></i>
            </div>
            <h3 style="margin-bottom: 0.5rem; color: var(--text-main);">Nenhum layout físico encontrado</h3>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Comece gerando a estrutura inicial para este endereçamento.</p>
            <button class="btn-smart-generate" onclick="openSmartGenerator('')" style="margin: 0 auto;">
                <i class="ph ph-plus"></i> Iniciar Criação
            </button>
        </div>
    </div>

    <!-- Smart Generator Modal -->
    <div class="modal-overlay" id="smartGeneratorModal">
        <div class="modal-card glass-card" style="background: white !important;">
            <div class="modal-header">
                <div style="display:flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h2 style="margin:0; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 0.75rem;">
                            <i class="ph ph-sparkle" style="color: var(--primary);"></i> Gerador Inteligente
                        </h2>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">
                            Defina múltiplos níveis de hierarquia para gerar endereços em massa.
                        </p>
                    </div>
                    <button class="btn-back" style="width: 36px; height: 36px; border-radius: 10px;" onclick="closeSmartGenerator()">
                        <i class="ph ph-x"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <input type="hidden" id="wizParentId" value="">
                <div class="generator-grid">
                    <div class="left-panel">
                        <div class="levels-container" id="levelsContainer">
                            <!-- Levels will be injected here -->
                        </div>
                        <button class="btn-add-level" onclick="addNewLevelRow()" style="margin-top: 1.5rem; width: 100%;">
                            <i class="ph ph-plus-circle"></i> Adicionar Próximo Nível
                        </button>
                        
                        <div style="margin-top: 2rem; padding: 1rem; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; display: flex; gap: 1rem; align-items: flex-start;">
                            <i class="ph ph-info" style="color: #d97706; font-size: 1.5rem; margin-top: 2px;"></i>
                            <div style="font-size: 0.85rem; color: #92400e; line-height: 1.5;">
                                <strong>Dica de Endereçáveis:</strong> O último nível da hierarquia será marcado automaticamente como endereçável (folha). Se você criar apenas um nível, ele será a folha.
                            </div>
                        </div>
                    </div>
                    
                    <div class="right-panel">
                        <div class="preview-sidebar">
                            <h4 style="margin: 0 0 1rem 0; font-weight: 700; color: var(--text-main); font-size: 0.85rem; text-transform: uppercase;">Resumo da Operação</h4>
                            <div class="preview-stat" id="previewNodeTotal">0</div>
                            <div class="preview-stat-label">Novos endereços serão gerados</div>
                            
                            <h4 style="margin: 1.5rem 0 0.75rem 0; font-weight: 700; color: var(--text-main); font-size: 0.8rem;">Exemplo de Formatação</h4>
                            <div class="preview-list" id="samplePreviewList">
                                <em>Nenhum dado...</em>
                            </div>

                            <button class="btn-save-final" id="btnApplyPreview" onclick="applyPreviewNodes()" style="width: 100%; margin-top: 1.5rem;">
                                <i class="ph ph-plus-circle"></i> Injetar na Árvore
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Bar -->
    <div id="actionBar" class="action-bar-floating glass-card" style="display: none;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 12px; height: 12px; background: var(--success); border-radius: 50%; box-shadow: 0 0 10px var(--success);"></div>
            <span style="font-weight: 600; color: var(--text-main);">
                <span id="draftCount">0</span> alteração(ões) pendente(s)
            </span>
        </div>
        <div style="width: 1px; height: 30px; background: var(--border);"></div>
        <button class="btn-save-final" onclick="consolidateNewNodes()" id="btnSaveFinal">
            <i class="ph ph-cloud-arrow-up"></i> Gravar Modificações
        </button>
    </div>

    <!-- SQL Final Dialog -->
    <div class="modal-overlay" id="sqlFinalModal">
        <div class="modal-card" style="max-width: 700px; background: white !important;">
            <div class="modal-header">
                <h3 style="margin:0; font-weight: 800; color: var(--success); display: flex; align-items: center; gap: 0.75rem;">
                    <i class="ph ph-check-circle"></i> Script Consolidado
                </h3>
            </div>
            <div class="modal-body">
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
                    Abaixo está o script SQL para efetivar as mudanças na base de dados. Execute-o via console ou ferramenta de DBA.
                </p>
                <div style="position: relative;">
                    <textarea id="sqlFinalContent" readonly style="width: 100%; height: 300px; font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; padding: 1.5rem; background: #0f172a; color: #94a3b8; border: none; border-radius: 16px;"></textarea>
                    <button onclick="copyFinalSql()" style="position: absolute; top: 1rem; right: 1rem; background: var(--primary); color: white; border: none; padding: 0.5rem 1rem; border-radius: 10px; cursor: pointer; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="ph ph-copy"></i> Copiar SQL
                    </button>
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.5rem 2rem; border-top: 1px solid var(--border); display: flex; gap: 1rem;">
                <button class="btn-save-final" style="width: 100%;" onclick="location.reload();">
                    Finalizar e Recarregar Árvore
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const tenantId = '{{ $tenantId }}';
        const armazemId = '{{ $armazemId }}';
        const enderecamentoId = '{{ $enderecamentoId }}';
        
        window.layoutData = [];
        let levelCount = 0;

        document.addEventListener('DOMContentLoaded', fetchTreeData);

        async function fetchTreeData() {
            try {
                const response = await fetch(`/api/enderecamentos/layout-fisico?tenant_id=${tenantId}&armazem_id=${armazemId}&enderecamento_id=${enderecamentoId}`);
                const res = await response.json();

                if (res.success) {
                    window.layoutData = res.data;
                    renderTree();
                    updateUIStats();
                } else {
                    handleError(res.message);
                }
            } catch (e) {
                handleError('Falha crítica de conexão.');
            }
        }

        function renderTree() {
            const root = document.getElementById('treeRoot');
            const loading = document.getElementById('treeLoading');
            const empty = document.getElementById('treeEmptyState');

            loading.style.display = 'none';

            if (!window.layoutData || window.layoutData.length === 0) {
                root.style.display = 'none';
                empty.style.display = 'block';
                return;
            }

            empty.style.display = 'none';
            root.style.display = 'block';

            // Build Map
            const map = {};
            const forest = [];

            window.layoutData.forEach(n => {
                map[n.id] = { ...n, children: [] };
            });

            window.layoutData.forEach(n => {
                if (n.parent_id && map[n.parent_id]) {
                    map[n.parent_id].children.push(map[n.id]);
                } else {
                    forest.push(map[n.id]);
                }
            });

            let html = '';
            forest.forEach(rootNode => {
                html += generateTreeHtml(rootNode);
            });
            
            root.innerHTML = html;
        }

        function generateTreeHtml(node) {
            const isDraft = node.is_new ? 'draft-node' : '';
            const draftBadge = node.is_new ? '<span class="draft-badge">Rascunho</span>' : '';
            const addBtn = `<button class="btn-inline-add" onclick="event.preventDefault(); openSmartGenerator('${node.id}')" title="Gerar Filhos"><i class="ph ph-plus"></i></button>`;

            if (!node.children || node.children.length === 0) {
                const aliasNode = node.alias && node.alias !== node.nome ? `<span class="node-alias">${node.alias}</span>` : '';
                return `
                    <div class="leaf-node ${isDraft}">
                        <i class="ph ph-check-circle leaf-icon-status"></i>
                        <div style="flex: 1; display: flex; align-items: center; gap: 0.75rem;">
                            <strong style="font-weight: 600;">${node.nome}</strong>
                            <span style="color: var(--text-muted); font-size: 0.8rem;">(${node.formatado})</span>
                            ${aliasNode}
                            ${draftBadge}
                        </div>
                        ${addBtn}
                    </div>
                `;
            }

            let kidsHtml = '';
            node.children.forEach(c => kidsHtml += generateTreeHtml(c));

            return `
                <details class="tree-node ${isDraft}" open>
                    <summary class="tree-summary">
                        <i class="ph ph-caret-right node-icon-caret"></i>
                        <i class="ph ph-folder" style="color: #64748b; font-size: 1.1rem;"></i>
                        <span style="font-weight: 600;">${node.nome}</span>
                        <span style="color: var(--text-muted); font-size: 0.8rem;">(${node.formatado})</span>
                        ${draftBadge}
                        ${addBtn}
                    </summary>
                    ${kidsHtml}
                </details>
            `;
        }

        function updateUIStats() {
            document.getElementById('nodeCountBadge').textContent = window.layoutData.length;
            const draftNodes = window.layoutData.filter(n => n.is_new);
            const actionBar = document.getElementById('actionBar');
            if (draftNodes.length > 0) {
                document.getElementById('draftCount').textContent = draftNodes.length;
                actionBar.style.display = 'flex';
            } else {
                actionBar.style.display = 'none';
            }
        }

        // --- SMART GENERATOR LOGIC ---

        const BASE_LAYOUT_TYPES = [
            { id: 1, name: 'Nível 1' },
            { id: 2, name: 'Nível 2' },
            { id: 3, name: 'Nível 3' },
            { id: 4, name: 'Nível 4' },
            { id: 5, name: 'Nível 5' },
            { id: 6, name: 'Nível 6' }
        ];

        function openSmartGenerator(parentId) {
            document.getElementById('wizParentId').value = parentId;
            document.getElementById('levelsContainer').innerHTML = '';
            levelCount = 0;
            
            // Start with one level always
            addNewLevelRow();
            
            document.getElementById('smartGeneratorModal').style.display = 'flex';
            updatePreviewStats();
        }

        function closeSmartGenerator() {
            document.getElementById('smartGeneratorModal').style.display = 'none';
        }

        function addNewLevelRow() {
            levelCount++;
            const container = document.getElementById('levelsContainer');
            const div = document.createElement('div');
            div.className = 'level-row';
            div.id = `level_row_${levelCount}`;
            
            // Suggest type based on count
            const suggestedType = levelCount <= 6 ? levelCount : 6;

            div.innerHTML = `
                <div class="level-row-header">
                    <span class="level-row-title">Configuração do Nível ${levelCount}</span>
                    ${levelCount > 1 ? `<button class="btn-remove-level" onclick="removeLevelRow(${levelCount})"><i class="ph ph-trash"></i></button>` : ''}
                </div>
                <div class="level-inputs">
                    <div class="level-input-group">
                        <label>Identificador Tipo</label>
                        <select onchange="updatePreviewStats()">
                            ${BASE_LAYOUT_TYPES.map(t => `<option value="${t.id}" ${t.id === suggestedType ? 'selected' : ''}>${t.name}</option>`).join('')}
                        </select>
                    </div>
                    <div class="level-input-group">
                        <label>Início (De)</label>
                        <input type="text" value="01" placeholder="Ex: 01" oninput="updatePreviewStats()">
                    </div>
                    <div class="level-input-group">
                        <label>Fim (Até)</label>
                        <input type="text" value="10" placeholder="Ex: 10" oninput="updatePreviewStats()">
                    </div>
                </div>
            `;
            container.appendChild(div);
            updatePreviewStats();
        }

        function removeLevelRow(id) {
            const row = document.getElementById(`level_row_${id}`);
            if (row) row.remove();
            updatePreviewStats();
        }

        function collectLevelData() {
            const rows = document.querySelectorAll('.level-row');
            const levels = [];
            rows.forEach(row => {
                const inputs = row.querySelectorAll('input, select');
                levels.push({
                    tipo_componente: parseInt(inputs[0].value),
                    inicio: inputs[1].value,
                    fim: inputs[2].value
                });
            });
            return levels;
        }

        function updatePreviewStats() {
            const levels = collectLevelData();
            let total = 0;
            let sampleText = "";
            
            if (levels.length > 0) {
                // Calculation logic
                let combos = 1;
                levels.forEach(L => {
                    let start = parseInt(L.inicio);
                    let end = parseInt(L.fim);
                    if (!isNaN(start) && !isNaN(end)) {
                        combos *= (Math.abs(end - start) + 1);
                    } else {
                        // handle chars
                        combos *= (L.fim.charCodeAt(0) - L.inicio.charCodeAt(0) + 1);
                    }
                });
                
                // IF it's under a parent, we must multiply by the number of parents if it's a replication
                // (For simplicity in preview, we show per-target count if target exists)
                total = combos;
                
                // Simple Sample generator
                sampleText = generateSampleDraft(levels);
            }

            document.getElementById('previewNodeTotal').textContent = total;
            document.getElementById('samplePreviewList').innerHTML = sampleText || '<em>Formatando...</em>';
        }

        function generateSampleDraft(levels) {
            let names = [""];
            levels.forEach((L, idx) => {
                let start = L.inicio;
                let end = L.fim;
                let pad = start.length > 1 && start.startsWith('0') ? start.length : 0;
                
                let startVal, endVal, isNum = false;
                if (!isNaN(parseInt(start)) && !isNaN(parseInt(end))) {
                    startVal = parseInt(start); endVal = parseInt(end); isNum = true;
                } else {
                    startVal = start.charCodeAt(0); endVal = end.charCodeAt(0);
                }

                let newNames = [];
                // Only take first 2 for preview if names get too large
                let countToTake = isNum ? Math.min(Math.abs(endVal - startVal) + 1, 2) : 2;

                for (let i = 0; i < countToTake; i++) {
                    let currentVal = startVal + i;
                    let strVal = isNum ? String(currentVal) : String.fromCharCode(currentVal);
                    if (pad > 0 && isNum) strVal = strVal.padStart(pad, '0');
                    
                    names.forEach(n => {
                        newNames.push((n ? n + '-' : '') + strVal);
                    });
                }
                names = newNames;
            });
            
            return names.map(n => `<div><i class="ph ph-dot" style="color:var(--primary)"></i> ${n}</div>`).join('') + 
                   (names.length > 5 ? '<div>...</div>' : '');
        }

        async function applyPreviewNodes() {
            const parentId = document.getElementById('wizParentId').value;
            const levels = collectLevelData();
            const btn = document.getElementById('btnApplyPreview');
            
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Injetando...';
            btn.disabled = true;

            try {
                // Get base formatting if parent exists
                let baseFormat = '';
                if (parentId) {
                    const p = window.layoutData.find(n => String(n.id) === String(parentId));
                    if (p) baseFormat = p.formatado;
                }

                const response = await fetch('/api/enderecamentos/layout-fisico/preview-nodes', {
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
                        base_parent_id: parentId,
                        base_parent_format: baseFormat,
                        niveis: levels
                    })
                });
                
                const result = await response.json();
                if (result.success) {
                    window.layoutData = window.layoutData.concat(result.data);
                    renderTree();
                    updateUIStats();
                    closeSmartGenerator();
                    showToast("Preview injetado com sucesso!");
                } else {
                    alert('Erro ao gerar: ' + result.message);
                }
            } catch (e) {
                alert('Falha na requisição de preview.');
            } finally {
                btn.innerHTML = '<i class="ph ph-plus-circle"></i> Injetar na Árvore';
                btn.disabled = false;
            }
        }

        async function consolidateNewNodes() {
            const draftNodes = window.layoutData.filter(n => n.is_new);
            if(draftNodes.length === 0) return;

            const btn = document.getElementById('btnSaveFinal');
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Processando...';
            btn.disabled = true;

            try {
                const response = await fetch('/api/enderecamentos/layout-fisico/generate-script', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        tenant_id: tenantId,
                        armazem_id: armazemId,
                        enderecamento_id: enderecamentoId,
                        nodes: draftNodes
                    })
                });
                const res = await response.json();
                if (res.success) {
                    document.getElementById('sqlFinalContent').value = res.sql;
                    document.getElementById('sqlFinalModal').style.display = 'flex';
                } else {
                    alert('Erro no servidor: ' + res.message);
                }
            } catch (e) {
                alert('Erro de conexão ao consolidar.');
            } finally {
                btn.innerHTML = original;
                btn.disabled = false;
            }
        }

        function copyFinalSql() {
            const el = document.getElementById('sqlFinalContent');
            el.select();
            document.execCommand('copy');
            showToast("SQL copiado com sucesso!");
        }

        function handleError(msg) {
            document.getElementById('treeContainer').innerHTML = `
                <div style="text-align: center; padding: 4rem;">
                    <i class="ph ph-warning-circle" style="font-size: 3rem; color: var(--danger); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--text-main);">Ops! Algo deu errado</h3>
                    <p style="color: var(--text-muted);">${msg}</p>
                </div>
            `;
        }

        function showToast(msg) {
            // Simplified toast for now, can be improved with a real library
            alert(msg);
        }
    </script>
@endpush
