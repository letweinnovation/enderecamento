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

        /* Skeleton Node (In-line add) */
        .skeleton-node {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            margin: 6px 0 6px 3rem;
            border-radius: 12px;
            border: 2px dashed #cbd5e1;
            background: transparent;
            color: #94a3b8;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            max-width: fit-content;
        }

        .skeleton-node:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }

        .skeleton-node input {
            background: transparent;
            border: none;
            outline: none;
            color: var(--primary-dark);
            font-weight: 600;
            width: 100px;
            margin-left: 0.5rem;
        }

        /* Selection Mode Styles */
        .selection-mode .tree-summary, .selection-mode .leaf-node {
            opacity: 0.5;
            cursor: pointer;
        }

        .selection-mode .selectable-sibling {
            opacity: 1;
            border: 2px solid var(--primary);
            box-shadow: 0 0 10px rgba(14, 165, 233, 0.2);
        }

        .selection-mode .selected-target {
            background: var(--primary-light);
            border-color: var(--primary-dark);
            color: var(--primary-dark);
        }

        .selection-mode .selected-target i {
            color: var(--primary-dark);
        }

        .selection-overlay {
            position: fixed;
            top: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2000;
            padding: 1rem 2rem;
            display: none;
            align-items: center;
            gap: 1.5rem;
            animation: slideDown 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes slideDown {
            from { transform: translate(-50%, -50px); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
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

        /* Modals */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 3000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-card {
            width: 100%;
            background: white !important;
            border-radius: 24px;
            overflow: hidden;
            animation: modalScale 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modalScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-header { padding: 1.5rem 2rem; border-bottom: 1px solid var(--border); }
        .modal-body { padding: 2rem; }
        .modal-footer { padding: 1.5rem 2rem; }

        /* Tree Controls Header */
        .tree-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        /* Filter Switch Styles */
        .filter-switch {
            position: relative;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            gap: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .filter-switch:hover { background: #e2e8f0; }

        .filter-switch input { opacity: 0; width: 0; height: 0; }

        .filter-slider {
            position: relative;
            width: 36px;
            height: 20px;
            background-color: #cbd5e1;
            transition: .3s;
            border-radius: 20px;
        }

        .filter-slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }

        input:checked + .filter-slider { background-color: var(--primary); }
        input:checked + .filter-slider:before { transform: translateX(16px); }

        .filter-switch i {
            font-size: 1.1rem;
            color: var(--text-muted);
            transition: color 0.2s;
        }

        input:checked ~ i { color: var(--primary); }

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

            .info-cards {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .tree-container {
                padding: 1rem;
            }

            .tree-node, .leaf-node, .skeleton-node {
                margin-left: 0.75rem !important;
            }

            .tree-node::before, .leaf-node::before {
                width: 0.75rem;
                left: -0.75rem;
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
        <div style="margin-left: auto; display: flex; gap: 0.75rem; align-items: center;">
            <button class="btn-back" style="width: 44px; height: 44px; border-radius: 12px; background: var(--primary); color: white; border: none; cursor: pointer;" onclick="openHelpModal()" title="Ajuda / Como Usar">
                <i class="ph ph-question" style="font-size: 1.5rem;"></i>
            </button>
        </div>
    </div>

    <!-- Selection Overlay for Cloning Mode -->
    <div id="selectionOverlay" class="selection-overlay glass-card" style="background: white; border-top: 4px solid var(--primary);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="background: var(--primary-light); color: var(--primary-dark); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                <i class="ph ph-copy"></i>
            </div>
            <div>
                <strong style="display: block; font-size: 1rem; color: var(--text-main);">Modo de Replicação</strong>
                <span style="font-size: 0.85rem; color: var(--text-muted);">Selecione os destinos clicando neles na árvore.</span>
            </div>
        </div>
        <div style="margin-left: auto; display: flex; gap: 0.75rem;">
            <button class="btn-save-final" style="background: #f1f5f9; color: var(--text-muted); border: none; padding: 0.5rem 1.25rem;" onclick="cancelSelectionMode()">Cancelar</button>
            <button class="btn-save-final" style="padding: 0.5rem 1.25rem;" id="btnConfirmClone" onclick="confirmCloning()">Confirmar (<span id="selectedCount">0</span>)</button>
        </div>
    </div>

    <div class="info-cards" style="grid-template-columns: repeat(2, 1fr);">
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
        <!-- Tree Controls relocated here for better UX -->
        <div class="tree-controls">
            <span style="font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Visualização da Árvore</span>
            <div style="display: flex; gap: 0.5rem; background: #f1f5f9; padding: 0.35rem; border-radius: 10px; align-items: center;">
                <label class="filter-switch" title="Mostrar apenas rascunhos">
                    <input type="checkbox" id="chkOnlyDrafts" onchange="renderTree()">
                    <span class="filter-slider"></span>
                    <i class="ph ph-magic-wand"></i>
                </label>
                <div style="width: 1px; height: 20px; background: #cbd5e1; margin: 0 0.25rem;"></div>
                <button class="btn-back" style="width: 32px; height: 32px; border-radius: 6px; border: none; box-shadow: none; background: white; color: var(--primary);" onclick="expandAllNodes()" title="Expandir Tudo">
                    <i class="ph ph-caret-double-down"></i>
                </button>
                <button class="btn-back" style="width: 32px; height: 32px; border-radius: 6px; border: none; box-shadow: none; background: white; color: var(--primary);" onclick="collapseAllNodes()" title="Recolher Tudo">
                    <i class="ph ph-caret-double-up"></i>
                </button>
            </div>
        </div>

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
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Adicione o primeiro nível agora mesmo.</p>
            <div class="skeleton-node" style="margin: 0 auto;" onclick="focusOnSkeleton('root')">
                <i class="ph ph-plus"></i> <input type="text" placeholder="Novo Nó..." onkeydown="handleSkeletonKey(event, '')" id="skeleton_root">
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

    <!-- Help Modal -->
    <div class="modal-overlay" id="helpModal">
        <div class="modal-card glass-card" style="max-width: 600px; background: white !important;">
            <div class="modal-header">
                <h3 style="margin:0; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 0.75rem;">
                    <i class="ph ph-info"></i> Guia Rápido de Uso
                </h3>
            </div>
            <div class="modal-body" style="font-size: 0.95rem; line-height: 1.6; color: var(--text-main);">
                <div style="margin-bottom: 1.5rem;">
                    <strong style="color: var(--primary-dark); display: block; margin-bottom: 0.5rem;">🚀 Adição Relâmpago (Skeleton Nodes)</strong>
                    <p>Ao final de cada nível, existe um campo <strong>"Novo nível..."</strong>. Digite o nome e aperte <code>Enter</code> para adicionar instantaneamente. O foco irá para o próximo campo automaticamente.</p>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <strong style="color: var(--primary-dark); display: block; margin-bottom: 0.5rem;">👯 Replicação Visual (Clonagem)</strong>
                    <p>Clique no ícone <i class="ph ph-copy"></i> em um nó com estrutura de filhos. Clique nos outros nós do mesmo nível para selecionar os destinos e confirme no painel que aparecerá no topo.</p>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <strong style="color: var(--primary-dark); display: block; margin-bottom: 0.5rem;">⚙️ Controles de Árvore</strong>
                    <p>Use os botões <i class="ph ph-caret-double-down"></i> e <i class="ph ph-caret-double-up"></i> no cabeçalho para expandir ou recolher toda a estrutura de uma vez.</p>
                </div>
                <div style="padding: 1rem; background: #f0fdf4; border-radius: 12px; color: #166534; font-size: 0.85rem;">
                    <i class="ph ph-warning-circle"></i> <strong>Lembre-se:</strong> Suas alterações ficam como "Rascunho". Clique em <strong>"Gravar Modificações"</strong> no rodapé para efetivar os dados.
                </div>
            </div>
            <div class="modal-footer" style="padding: 1.5rem 2rem; border-top: 1px solid var(--border);">
                <button class="btn-save-final" style="width: 100%;" onclick="closeHelpModal()">Entendi, vamos lá!</button>
            </div>
        </div>
    </div>

    <!-- SQL Final Dialog -->
    <div class="modal-overlay" id="sqlFinalModal">
        <div class="modal-card glass-card" style="max-width: 700px; background: white !important;">
            <div class="modal-header">
                <h3 style="margin:0; font-weight: 800; color: var(--success); display: flex; align-items: center; gap: 0.75rem;">
                    <i class="ph ph-check-circle"></i> Script Consolidado
                </h3>
            </div>
            <div class="modal-body">
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
                    Abaixo está o script SQL para efetivar as mudanças na base de dados.
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
    <!-- Notification Modal (Replaces alert) -->
    <div class="modal-overlay" id="noticeModal">
        <div class="modal-card glass-card" style="max-width: 450px; background: white !important;">
            <div class="modal-body" style="text-align: center; padding: 2.5rem;">
                <div id="noticeIcon" style="width: 64px; height: 64px; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
                    <i class="ph ph-info"></i>
                </div>
                <h3 id="noticeTitle" style="margin-bottom: 0.5rem; font-weight: 800; color: var(--text-main);">Notificação</h3>
                <p id="noticeMessage" style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.5;"></p>
            </div>
            <div class="modal-footer" style="padding: 1rem 2rem 2rem; border: none; display: flex; justify-content: center;">
                <button class="btn-save-final" style="width: 100%; justify-content: center;" onclick="closeNoticeModal()">Entendi</button>
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
        let selectionMode = false;
        let selectionSourceId = null;
        let selectedTargets = [];

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
            const onlyDrafts = document.getElementById('chkOnlyDrafts')?.checked || false;

            // Capture open states before rendering
            const openStates = {};
            document.querySelectorAll('details[id]').forEach(d => {
                openStates[d.id] = d.open;
            });

            loading.style.display = 'none';

            if (!window.layoutData || window.layoutData.length === 0) {
                root.style.display = 'none';
                empty.style.display = 'block';
                return;
            }

            // Filtering Logic
            let activeNodes = window.layoutData;
            if (onlyDrafts) {
                const draftPathIds = new Set();
                window.layoutData.forEach(n => {
                    if (n.is_new) {
                        let curr = n;
                        while(curr) {
                            draftPathIds.add(String(curr.id));
                            curr = window.layoutData.find(parent => String(parent.id) === String(curr.parent_id));
                        }
                    }
                });
                activeNodes = window.layoutData.filter(n => draftPathIds.has(String(n.id)));
            }

            if (activeNodes.length === 0 && onlyDrafts) {
                root.innerHTML = '<div style="text-align:center; padding: 2rem; color: var(--text-muted); font-size: 0.9rem;">Nenhuma alteração pendente para filtrar.</div>';
                root.style.display = 'block';
                empty.style.display = 'none';
                return;
            }

            empty.style.display = 'none';
            root.style.display = 'block';

            // Build Map
            const map = {};
            const forest = [];

            activeNodes.forEach(n => {
                map[n.id] = { ...n, children: [] };
            });

            activeNodes.forEach(n => {
                if (n.parent_id && map[n.parent_id]) {
                    map[n.parent_id].children.push(map[n.id]);
                } else if (!n.parent_id || !activeNodes.find(p => String(p.id) === String(n.parent_id))) {
                    forest.push(map[n.id]);
                }
            });

            let html = '';
            forest.forEach(rootNode => {
                html += generateTreeHtml(rootNode);
            });
            
            // Add root skeleton ONLY if not filtering
            if (!onlyDrafts) {
                html += `
                    <div class="skeleton-node" onclick="event.stopPropagation(); focusOnSkeleton('')">
                        <i class="ph ph-plus"></i>
                        <input type="text" placeholder="Novo nível..." onkeydown="handleSkeletonKey(event, '')" id="skeleton_">
                    </div>
                `;
            }

            root.innerHTML = html;

            // Restore open states
            Object.keys(openStates).forEach(id => {
                const el = document.getElementById(id);
                if (el) el.open = openStates[id];
            });

            // If filtering, force open all paths to drafts
            if (onlyDrafts) {
                document.querySelectorAll('details').forEach(d => d.open = true);
            }
        }

        function generateTreeHtml(node) {
            const isDraft = node.is_new ? 'draft-node' : '';
            const draftBadge = node.is_new ? '<span class="draft-badge">Rascunho</span>' : '';
            
            // Clone and Add Buttons
            const actionBtns = `
                <div style="margin-left: auto; display: flex; gap: 0.5rem;" class="node-actions">
                    <button class="btn-inline-add" style="background: #f8fafc; color: #64748b;" onclick="event.preventDefault(); event.stopPropagation(); toggleSelectionMode('${node.id}')" title="Replicar Estrutura">
                        <i class="ph ph-copy"></i>
                    </button>
                    <button class="btn-inline-add" onclick="event.preventDefault(); event.stopPropagation(); focusOnSkeleton('${node.id}')" title="Adicionar Filho">
                        <i class="ph ph-plus"></i>
                    </button>
                </div>
            `;

            if (!node.children || node.children.length === 0) {
                const aliasNode = node.alias && node.alias !== node.nome ? `<span class="node-alias">${node.alias}</span>` : '';
                return `
                    <div class="leaf-node ${isDraft}" id="node_${node.id}" onclick="handleNodeClick('${node.id}')">
                        <i class="ph ph-check-circle leaf-icon-status"></i>
                        <div style="flex: 1; display: flex; align-items: center; gap: 0.75rem;">
                            <strong style="font-weight: 600;">${node.nome}</strong>
                            <span style="color: var(--text-muted); font-size: 0.8rem;">(${node.formatado})</span>
                            ${aliasNode}
                            ${draftBadge}
                        </div>
                        ${actionBtns}
                    </div>
                    <div id="kids_${node.id}" style="display:none"></div>
                `;
            }

            let kidsHtml = '';
            node.children.forEach(c => kidsHtml += generateTreeHtml(c));
            
            // Skeleton for this container
            kidsHtml += `
                <div class="skeleton-node" onclick="event.stopPropagation(); focusOnSkeleton('${node.id}')">
                    <i class="ph ph-plus"></i>
                    <input type="text" placeholder="Novo nível..." onkeydown="handleSkeletonKey(event, '${node.id}')" id="skeleton_${node.id}">
                </div>
            `;

            return `
                <details class="tree-node ${isDraft}" id="node_${node.id}" open>
                    <summary class="tree-summary" onclick="handleNodeClick('${node.id}')">
                        <i class="ph ph-caret-right node-icon-caret"></i>
                        <i class="ph ph-folder" style="color: #64748b; font-size: 1.1rem;"></i>
                        <span style="font-weight: 600;">${node.nome}</span>
                        <span style="color: var(--text-muted); font-size: 0.8rem;">(${node.formatado})</span>
                        ${draftBadge}
                        ${actionBtns}
                    </summary>
                    <div class="node-children">
                        ${kidsHtml}
                    </div>
                </details>
            `;
        }

        function handleNodeClick(nodeId) {
            if (!selectionMode) return;
            
            const node = window.layoutData.find(n => String(n.id) === String(nodeId));
            const source = window.layoutData.find(n => String(n.id) === String(selectionSourceId));
            
            if (node.parent_id !== source.parent_id || node.id === source.id) return;

            const idx = selectedTargets.indexOf(nodeId);
            const el = document.getElementById('node_' + nodeId);
            const summary = el.querySelector('.tree-summary') || el;

            if (idx > -1) {
                selectedTargets.splice(idx, 1);
                summary.classList.remove('selected-target');
            } else {
                selectedTargets.push(nodeId);
                summary.classList.add('selected-target');
            }
            
            document.getElementById('selectedCount').textContent = selectedTargets.length;
        }

        function focusOnSkeleton(parentId) {
            const input = document.getElementById('skeleton_' + parentId);
            if (input) {
                input.focus();
            }
        }

        function handleSkeletonKey(e, parentId) {
            if (e.key === 'Enter') {
                const name = e.target.value.trim();
                if (name) {
                    addNodeInLine(parentId, name);
                    e.target.value = '';
                }
            }
        }

        function addNodeInLine(parentId, name) {
            let parentNode = null;
            if (parentId) {
                parentNode = window.layoutData.find(n => String(n.id) === String(parentId));
            }

            // 1. Strict Contextual Validation: "Fazer sentido com o pai"
            let finalName = name;
            if (parentNode) {
                const prefix = parentNode.nome + '-';
                
                if (name.includes('-')) {
                    // If user manually typed a dash, it MUST start with parent prefix
                    if (!name.startsWith(prefix)) {
                        showNotice('Entrada Inválida', `Para o pai "${parentNode.nome}", o nome deve começar com "${prefix}".`, 'error');
                        return;
                    }
                } else {
                    // If user only typed a suffix (no dash), we auto-prefix it
                    finalName = prefix + name;
                }
            } else {
                // If it's a root node, we don't allow dashes initially to keep it clean, 
                // OR we just accept it if it's the top level.
                if (name.includes('-')) {
                    showNotice('Entrada Inválida', 'Níveis raiz não devem conter o caractere "-" manualmente.', 'error');
                    return;
                }
            }

            // 2. Uniqueness Check: "Nao pode duplicar"
            const exists = window.layoutData.some(n => 
                String(n.parent_id) === String(parentId) && 
                n.nome.toLowerCase() === finalName.toLowerCase()
            );
            
            if (exists) {
                showNotice('Nome Duplicado', `O nome "${finalName}" já existe neste nível.`, 'error');
                return;
            }

            const newId = 'draft_' + Math.random().toString(36).substr(2, 9);
            const baseFormat = parentNode ? parentNode.formatado : '';
            const suffix = finalName.includes('-') ? finalName.split('-').pop() : finalName;
            const formatado = baseFormat ? baseFormat + '-' + suffix : finalName;

            const newNode = {
                id: newId,
                parent_id: parentId || null,
                nome: finalName,
                formatado: formatado,
                is_new: true,
                is_enderecavel: false
            };

            window.layoutData.push(newNode);
            
            renderTree();
            focusOnSkeleton(parentId);
            updateUIStats();
        }

        function toggleSelectionMode(sourceId) {
            selectionMode = true;
            selectionSourceId = sourceId;
            selectedTargets = [];
            
            const source = window.layoutData.find(n => String(n.id) === String(sourceId));
            document.body.classList.add('selection-mode');
            document.getElementById('selectionOverlay').style.display = 'flex';
            document.getElementById('selectedCount').textContent = '0';

            // Highlight possible targets (siblings)
            window.layoutData.forEach(node => {
                if (node.parent_id === source.parent_id && node.id !== source.id) {
                    const el = document.getElementById('node_' + node.id);
                    if (el) {
                        const summary = el.querySelector('.tree-summary') || el;
                        summary.classList.add('selectable-sibling');
                    }
                }
            });
        }

        function cancelSelectionMode() {
            selectionMode = false;
            document.body.classList.remove('selection-mode');
            document.getElementById('selectionOverlay').style.display = 'none';
            
            // Clean classes
            document.querySelectorAll('.selectable-sibling, .selected-target').forEach(el => {
                el.classList.remove('selectable-sibling', 'selected-target');
            });
        }

        function confirmCloning() {
            if (selectedTargets.length === 0) return;
            
            const sourceId = selectionSourceId;
            const sourceNode = window.layoutData.find(n => String(n.id) === String(sourceId));
            const sourceName = sourceNode?.nome || "";
            const children = findDescendants(sourceId);
            
            if (children.length === 0) {
                showNotice('Nada para Clonar', "Este nó não possui estrutura de filhos para replicar.", 'info');
                cancelSelectionMode();
                return;
            }

            let newClones = [];
            selectedTargets.forEach(targetId => {
                const target = window.layoutData.find(n => String(n.id) === String(targetId));
                const clones = replicateChildren(sourceId, targetId, target.formatado, sourceName, target.nome);
                newClones = newClones.concat(clones);
            });

            window.layoutData = window.layoutData.concat(newClones);
            renderTree();
            updateUIStats();
            cancelSelectionMode();
            showToast(`Estrutura replicada para ${selectedTargets.length} destinos!`);
        }

        function findDescendants(parentId) {
            let results = [];
            const kids = window.layoutData.filter(n => String(n.parent_id) === String(parentId));
            kids.forEach(k => {
                results.push(k);
                results = results.concat(findDescendants(k.id));
            });
            return results;
        }

        function replicateChildren(sourceParentId, targetParentId, targetBaseFormat, sourceParentName, targetParentName) {
            let clones = [];
            const kids = window.layoutData.filter(n => String(n.parent_id) === String(sourceParentId));
            
            kids.forEach(k => {
                // Smart Name Adjustment: 
                let newName = k.nome;
                if (sourceParentName && targetParentName && k.nome.startsWith(sourceParentName)) {
                    newName = targetParentName + k.nome.substring(sourceParentName.length);
                }

                const newFormat = targetBaseFormat ? targetBaseFormat + '-' + (newName.includes('-') ? newName.split('-').pop() : newName) : newName;

                // Check if a node with this name already exists under the target parent
                const existing = window.layoutData.find(n => 
                    String(n.parent_id) === String(targetParentId) && 
                    n.nome.toLowerCase() === newName.toLowerCase()
                );

                if (existing) {
                    // MERGE: Node already exists, don't clone it, but clone its children recursively to this existing node
                    const subClones = replicateChildren(k.id, existing.id, existing.formatado, k.nome, newName);
                    clones = clones.concat(subClones);
                } else {
                    // CLONE: Node doesn't exist, create it
                    const newId = 'draft_' + Math.random().toString(36).substr(2, 9);
                    const clone = {
                        ...k,
                        id: newId,
                        parent_id: targetParentId,
                        nome: newName,
                        formatado: newFormat,
                        is_new: true
                    };
                    clones.push(clone);
                    
                    const subClones = replicateChildren(k.id, newId, newFormat, k.nome, newName);
                    clones = clones.concat(subClones);
                }
            });
            
            return clones;
        }

        function expandAllNodes() {
            document.querySelectorAll('details').forEach(n => n.open = true);
        }

        function collapseAllNodes() {
            document.querySelectorAll('details').forEach(n => n.open = false);
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
                    showNotice('Erro no Servidor', res.message, 'error');
                }
            } catch (e) {
                showNotice('Erro de Conexão', 'Não foi possível consolidar as mudanças.', 'error');
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
            showNotice('Sucesso', msg, 'success');
        }

        function showNotice(title, message, type = 'info') {
            const modal = document.getElementById('noticeModal');
            const iconWrap = document.getElementById('noticeIcon');
            const icon = iconWrap.querySelector('i');
            const titleEl = document.getElementById('noticeTitle');
            const msgEl = document.getElementById('noticeMessage');

            titleEl.textContent = title;
            msgEl.textContent = message;

            // Reset classes
            iconWrap.className = '';
            iconWrap.style.background = '';
            iconWrap.style.color = '';
            icon.className = 'ph';

            if (type === 'error') {
                iconWrap.style.background = '#fee2e2';
                iconWrap.style.color = 'var(--danger)';
                icon.classList.add('ph-warning-circle');
            } else if (type === 'success') {
                iconWrap.style.background = '#f0fdf4';
                iconWrap.style.color = 'var(--success)';
                icon.classList.add('ph-check-circle');
            } else {
                iconWrap.style.background = 'var(--primary-light)';
                iconWrap.style.color = 'var(--primary-dark)';
                icon.classList.add('ph-info');
            }

            modal.style.display = 'flex';
        }

        function closeNoticeModal() {
            document.getElementById('noticeModal').style.display = 'none';
        }

        function openHelpModal() {
            document.getElementById('helpModal').style.display = 'flex';
        }

        function closeHelpModal() {
            document.getElementById('helpModal').style.display = 'none';
        }
    </script>
@endpush
