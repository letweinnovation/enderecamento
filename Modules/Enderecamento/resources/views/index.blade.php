@extends('enderecamento::layouts.master')

@section('title', 'Endereçamentos')

@push('styles')
    <style>
        .filters-card {
            background: var(--surface);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            margin-bottom: 1.5rem;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        /* Tenant Autocomplete */
        .tenant-input-wrapper {
            position: relative;
            width: 100%;
        }

        .tenant-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            z-index: 100;
            max-height: 250px;
            overflow-y: auto;
            display: none;
        }

        .tenant-dropdown.show {
            display: block;
        }

        .tenant-option {
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }

        .tenant-option:last-child {
            border-bottom: none;
        }

        .tenant-option:hover {
            background: #f0fdfa;
        }

        .tenant-option .name {
            font-weight: 600;
            color: var(--text-main);
        }

        .tenant-option .domain {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .tenant-option .id {
            font-size: 0.75rem;
            font-family: monospace;
            color: #9ca3af;
        }

        .selected-tenant {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0 0.75rem;
            background: #ecfeff;
            border: 1px solid #a5f3fc;
            border-radius: 8px;
            height: 44px;
        }

        .selected-tenant .info {
            flex: 1;
            line-height: 1.1;
        }

        .selected-tenant .name {
            font-weight: 600;
            color: var(--primary);
            font-size: 0.85rem;
        }

        .selected-tenant .id {
            font-size: 0.7rem;
            font-family: monospace;
            color: var(--text-muted);
        }

        .selected-tenant .remove {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--primary);
            padding: 0.25rem;
            border-radius: 4px;
        }

        .selected-tenant .remove:hover {
            background: #cffafe;
        }

        /* Loading spinner for tenant input */
        .tenant-input-wrapper.loading::after {
            content: "";
            position: absolute;
            right: 0.75rem;
            top: 50%;
            margin-top: -8px;
            width: 16px;
            height: 16px;
            border: 2px solid #e5e7eb;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            z-index: 10;
            pointer-events: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Filter Actions */
        .filter-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        /* Results */
        .results-card {
            background: var(--surface);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .results-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .results-count {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
        }

        .results-table th {
            padding: 1rem;
            text-align: left;
            color: var(--text-muted);
            font-weight: 600;
            border-bottom: 1px solid var(--border);
            font-size: 0.85rem;
        }

        .results-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.9rem;
        }

        .results-table tbody tr.main-row {
            cursor: pointer;
            transition: background 0.1s;
        }

        .results-table tbody tr.main-row:hover {
            background: #f0fdfa;
        }

        .results-table tbody tr.expanded {
            background: #f8fafc;
        }

        .results-table tbody tr.expanded td {
            border-bottom: none;
        }

        /* Endereçamentos Expandidos (Sub-tabela) */
        .sub-row {
            display: none;
            background: #f8fafc;
        }

        .sub-row.show {
            display: table-row;
        }

        .sub-row td {
            padding: 0 1rem 1rem 1rem;
            border-bottom: 1px solid var(--border);
        }

        .enderecos-container {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            padding: 1rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .enderecos-header {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .enderecos-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }

        .enderecos-table th {
            text-align: left;
            padding: 0.5rem;
            color: var(--text-muted);
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }

        .enderecos-table td {
            padding: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .enderecos-table tr:last-child td {
            border-bottom: none;
        }

        .expand-icon {
            transition: transform 0.2s;
            color: var(--text-muted);
        }

        .expanded .expand-icon {
            transform: rotate(90deg);
        }

        .loading-mini {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #e5e7eb;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 1rem;
        }

        /* Loading */
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .loading-spinner.show {
            display: block;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--border);
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        /* Status Badges */
        .status-badge {
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-blocked {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-empty {
            background: #f3f4f6;
            color: #6b7280;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1 class="page-title"><i class="ph ph-map-pin-area" style="color: var(--primary);"></i> Endereçamentos</h1>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <div class="filters-grid">
            <!-- Tenant Field -->
            <div class="form-group">
                <label class="form-label required">Tenant</label>
                <div class="tenant-input-wrapper">
                    <input type="text" id="tenantSearch" class="form-input"
                        placeholder="Digite o nome, domínio ou ID do tenant (mínimo 3 caracteres)..." autocomplete="off">
                    <div id="tenantDropdown" class="tenant-dropdown"></div>
                </div>
                <div id="selectedTenant" class="selected-tenant" style="display: none;">
                    <div class="info">
                        <div class="name" id="selectedTenantName"></div>
                        <div class="id" id="selectedTenantId"></div>
                    </div>
                    <button class="remove" onclick="clearSelectedTenant()" title="Remover" id="btn-remove-tenant">
                        <i class="ph ph-x"></i>
                    </button>
                </div>
                <input type="hidden" id="tenantId" value="">
            </div>
        </div>

        <div class="filter-actions">
            <button class="btn-secondary" onclick="clearFilters()" id="btn-limpar">
                <i class="ph ph-eraser"></i> Limpar
            </button>
            <button id="btn-buscar" class="btn-primary" onclick="searchEnderecos()" disabled>
                <i class="ph ph-magnifying-glass"></i> Buscar
            </button>
        </div>
    </div>

    <!-- Results -->
    <div class="results-card">
        <div class="results-header">
            <div class="results-title">
                <i class="ph ph-list"></i> Resultados
                <span class="results-count" id="resultsCount"></span>
            </div>
        </div>

        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner"></div>
            <p>Carregando armazéns...</p>
        </div>

        <div id="emptyState" class="empty-state">
            <i class="ph ph-magnifying-glass"></i>
            <p>Selecione um tenant e clique em Buscar para listar os armazéns.</p>
        </div>

        <table class="results-table" id="resultsTable" style="display: none;">
            <thead>
                <tr>
                    <th style="width: 40px;"></th>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>ID Externo</th>
                    <th>Tipo</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="resultsBody"></tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        let selectedTenantData = null;
        let debounceTimer = null;

        document.addEventListener('DOMContentLoaded', function () {
            const tenantSearch = document.getElementById('tenantSearch');

            tenantSearch.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                const query = this.value.trim();

                if (query.length < 3) {
                    hideTenantDropdown();
                    return;
                }

                debounceTimer = setTimeout(() => {
                    searchTenants(query);
                }, 300);
            });

            tenantSearch.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    hideTenantDropdown();
                }
            });

            document.addEventListener('click', function (e) {
                if (!e.target.closest('.tenant-input-wrapper')) {
                    hideTenantDropdown();
                }
            });
        });

        // Tenant Search (autocomplete)
        async function searchTenants(query) {
            const wrapper = document.querySelector('.tenant-input-wrapper');
            if (wrapper) wrapper.classList.add('loading');

            try {
                const response = await fetch(`/api/enderecamentos/tenants?q=${encodeURIComponent(query)}`);
                const tenants = await response.json();

                const dropdown = document.getElementById('tenantDropdown');

                if (tenants.length === 0) {
                    dropdown.innerHTML = '<div class="tenant-option" style="color: var(--text-muted); cursor: default;">Nenhum tenant encontrado</div>';
                } else {
                    dropdown.innerHTML = tenants.map(tenant => `
                        <div class="tenant-option" onclick="selectTenant('${tenant.id}', '${escapeHtml(tenant.name)}', '${escapeHtml(tenant.domain || '')}')">
                            <div class="name">${escapeHtml(tenant.name)}</div>
                            <div class="domain">${escapeHtml(tenant.domain || '-')}</div>
                            <div class="id">${escapeHtml(tenant.id)}</div>
                        </div>
                    `).join('');
                }

                dropdown.classList.add('show');
            } catch (error) {
                console.error('Error searching tenants:', error);
            } finally {
                if (wrapper) wrapper.classList.remove('loading');
            }
        }

        function selectTenant(id, name, domain) {
            selectedTenantData = { id, name, domain };

            document.getElementById('tenantId').value = id;
            document.getElementById('tenantSearch').style.display = 'none';

            document.getElementById('selectedTenantName').textContent = name;
            document.getElementById('selectedTenantId').textContent = id;
            document.getElementById('selectedTenant').style.display = 'flex';

            hideTenantDropdown();
            updateSearchButton();
        }

        function clearSelectedTenant() {
            selectedTenantData = null;
            document.getElementById('tenantId').value = '';
            document.getElementById('tenantSearch').value = '';
            document.getElementById('tenantSearch').style.display = 'block';
            document.getElementById('selectedTenant').style.display = 'none';

            updateSearchButton();
            clearResults();
        }

        function hideTenantDropdown() {
            document.getElementById('tenantDropdown').classList.remove('show');
        }

        function updateSearchButton() {
            const tenantId = document.getElementById('tenantId').value;
            document.getElementById('btn-buscar').disabled = !tenantId;
        }

        function clearFilters() {
            clearSelectedTenant();
        }

        function clearResults() {
            document.getElementById('resultsTable').style.display = 'none';
            document.getElementById('emptyState').style.display = 'block';
            document.getElementById('resultsCount').textContent = '';
            document.getElementById('resultsBody').innerHTML = '';
        }

        // Search armazéns by tenant
        async function searchEnderecos() {
            const tenantId = document.getElementById('tenantId').value;

            if (!tenantId) {
                showToast('Selecione um tenant primeiro.');
                return;
            }

            document.getElementById('loadingSpinner').classList.add('show');
            document.getElementById('emptyState').style.display = 'none';
            document.getElementById('resultsTable').style.display = 'none';

            try {
                const response = await fetch(`/api/enderecamentos/armazens?tenant_id=${encodeURIComponent(tenantId)}`);
                const result = await response.json();

                document.getElementById('loadingSpinner').classList.remove('show');

                if (result.success && result.data.length > 0) {
                    document.getElementById('resultsCount').textContent = `(${result.total} armazéns)`;
                    
                    let html = '';
                    result.data.forEach(arm => {
                        html += `
                            <tr class="main-row" onclick="toggleArmazem(this, ${arm.id}, '${tenantId}')">
                                <td><i class="ph ph-caret-right expand-icon"></i></td>
                                <td><code>${escapeHtml(String(arm.id))}</code></td>
                                <td><strong>${escapeHtml(arm.nome || '-')}</strong></td>
                                <td>${escapeHtml(arm.idExterno || '-')}</td>
                                <td>${escapeHtml(arm.tipoArmazem || '-')}</td>
                                <td>${formatStatus(arm.regStatus)}</td>
                            </tr>
                            <tr class="sub-row" id="sub-arm-${arm.id}">
                                <td colspan="6">
                                    <div class="enderecos-container">
                                        <div class="enderecos-header">
                                            <i class="ph ph-map-pin"></i> Endereçamentos - ${escapeHtml(arm.nome || 'Sem Nome')}
                                            <span class="loading-mini" id="load-arm-${arm.id}" style="display: none;"></span>
                                        </div>
                                        <div id="content-arm-${arm.id}"></div>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    document.getElementById('resultsBody').innerHTML = html;
                    document.getElementById('resultsTable').style.display = 'table';
                } else {
                    document.getElementById('emptyState').innerHTML = `
                        <i class="ph ph-package"></i>
                        <p>Nenhum armazém encontrado para este tenant.</p>
                    `;
                    document.getElementById('emptyState').style.display = 'block';
                }
            } catch (error) {
                document.getElementById('loadingSpinner').classList.remove('show');
                console.error('Error searching armazéns:', error);
                showToast('Erro ao buscar armazéns.');
                document.getElementById('emptyState').innerHTML = `
                    <i class="ph ph-warning"></i>
                    <p>Erro ao buscar armazéns. Tente novamente.</p>
                `;
                document.getElementById('emptyState').style.display = 'block';
            }
        }

        async function toggleArmazem(row, armazemId, tenantId) {
            const isExpanded = row.classList.contains('expanded');
            const subRow = document.getElementById(`sub-arm-${armazemId}`);
            const contentDiv = document.getElementById(`content-arm-${armazemId}`);
            const loader = document.getElementById(`load-arm-${armazemId}`);

            if (isExpanded) {
                // Collapse
                row.classList.remove('expanded');
                subRow.classList.remove('show');
            } else {
                // Expand
                row.classList.add('expanded');
                subRow.classList.add('show');
                
                // Fetch if not already loaded
                if (contentDiv.innerHTML.trim() === '') {
                    loader.style.display = 'inline-block';
                    try {
                        const response = await fetch(`/api/enderecamentos/enderecos?tenant_id=${encodeURIComponent(tenantId)}&armazem_id=${armazemId}`);
                        const result = await response.json();
                        
                        if (result.success && result.data.length > 0) {
                            let table = `
                                <table class="enderecos-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Descrição</th>
                                            <th>ID Externo</th>
                                            <th>Formatação</th>
                                            <th>Tipo</th>
                                            <th>Cubagem Padrão</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;
                            
                            result.data.forEach(end => {
                                table += `
                                    <tr>
                                        <td style="font-family: monospace; color: #64748b;">${end.id}</td>
                                        <td>${escapeHtml(end.descricao || '-')}</td>
                                        <td>${escapeHtml(end.idExterno || '-')}</td>
                                        <td><strong>${escapeHtml(end.formatacao || '-')}</strong></td>
                                        <td>${escapeHtml(end.tipoEnderecamento || '-')}</td>
                                        <td>${end.indConsiderarCubagem ? (end.CUBAGEMPADRAO || '-') : '<span style="color:#cbd5e1">-</span>'}</td>
                                        <td>${formatStatus(end.regStatus)}</td>
                                    </tr>
                                `;
                            });
                            
                            table += `</tbody></table>`;
                            contentDiv.innerHTML = table;
                        } else {
                            contentDiv.innerHTML = '<p style="color: var(--text-muted); font-size: 0.85rem; padding: 0.5rem;">Nenhum endereçamento encontrado para este armazém.</p>';
                        }
                    } catch (err) {
                        contentDiv.innerHTML = '<p style="color: #ef4444; font-size: 0.85rem; padding: 0.5rem;">Erro ao carregar endereçamentos.</p>';
                    } finally {
                        loader.style.display = 'none';
                    }
                }
            }
        }

        function formatStatus(status) {
            if (status === 1 || status === '1') {
                return '<span class="status-badge status-active">Ativo</span>';
            } else if (status === 0 || status === '0') {
                return '<span class="status-badge status-blocked">Inativo</span>';
            }
            return '<span class="status-badge status-empty">-</span>';
        }
    </script>
@endpush
