@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/temperature_list.css') }}" rel="stylesheet">
@endsection

@section('content')

{{-- Filtry --}}
<div class="card filter-card shadow-sm">
    <div class="card-body">
        <div class="row align-items-end g-2">
            <div class="col-md-3">
                <label for="date_from" class="form-label mb-1">Od</label>
                <input type="date" id="date_from" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label mb-1">Do</label>
                <input type="date" id="date_to" class="form-control form-control-sm">
            </div>
            <div class="col-md-6 d-flex flex-column align-items-end">
                <label class="form-label mb-1">Szybki wybór</label>
                <div>
                    <button class="btn btn-sm btn-outline-secondary btn-period" data-period="today">Dzisiaj</button>
                    <button class="btn btn-sm btn-outline-secondary btn-period" data-period="yesterday">Wczoraj</button>
                    <button class="btn btn-sm btn-outline-secondary btn-period" data-period="week">Tydzień</button>
                    <button class="btn btn-sm btn-outline-secondary btn-period" data-period="month">Miesiąc</button>
                    <button class="btn btn-sm btn-outline-danger btn-period" data-period="all">Wszystkie</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Dwa boxy --}}
<div class="row">

    {{-- Temperatura --}}
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fa fa-thermometer-half me-1"></i> Temperatura (°C)</h6>
                <span id="temp-count" class="badge bg-secondary">0 odczytów</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="tempChart"></canvas>
                </div>
                <table class="table table-sm table-striped mb-1">
                    <thead>
                        <tr>
                            <th>Czas</th>
                            <th>Temp (°C)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="temp-tbody">
                        <tr><td colspan="3" class="no-data">Ładowanie...</td></tr>
                    </tbody>
                </table>
                <div id="temp-pagination"></div>
            </div>
        </div>
    </div>

    {{-- Wilgotność --}}
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fa fa-tint me-1"></i> Wilgotność (%)</h6>
                <span id="hum-count" class="badge bg-secondary">0 odczytów</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="humChart"></canvas>
                </div>
                <table class="table table-sm table-striped mb-1">
                    <thead>
                        <tr>
                            <th>Czas</th>
                            <th>Wilgotność (%)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="hum-tbody">
                        <tr><td colspan="3" class="no-data">Ładowanie...</td></tr>
                    </tbody>
                </table>
                <div id="hum-pagination"></div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const tempCtx = document.getElementById('tempChart').getContext('2d');
    const humCtx = document.getElementById('humChart').getContext('2d');

    // Inicjalizacja wykresów 
    let tempChart = new Chart(tempCtx, {
        type: 'bar',
        data: { labels: [], datasets: [{ label: 'Temperatura (°C)', data: [], backgroundColor: 'rgba(195, 28, 74, 0.6)', borderColor: '#C31C4A', borderWidth: 1 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: false } } }
    });

    let humChart = new Chart(humCtx, {
        type: 'bar',
        data: { labels: [], datasets: [{ label: 'Wilgotność (%)', data: [], backgroundColor: 'rgba(54, 162, 235, 0.6)', borderColor: 'rgba(54, 162, 235, 1)', borderWidth: 1 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: false } } }
    });

    // Ustawienia paginacji i przechowywanie danych
    const perPage = 20;
    let tempPage = 1;
    let humPage = 1;
    let allData = [];

    function formatDate(ts) {
        if (!ts) return '-';
        return ts.substring(0, 16).replace('T', ' ');
    }

    // Ajax pobierający dane 
    function fetchData() {
        const dateFrom = $('#date_from').val();
        const dateTo = $('#date_to').val();

        $.ajax({
            url: '/sensors/data/temperature',
            method: 'GET',
            data: { date_from: dateFrom, date_to: dateTo },
            success: function(res) {
                allData = res.data;
                tempPage = 1;
                humPage = 1;
                renderAll();
            },
            error: function() {
                $('#temp-tbody').html('<tr><td colspan="3" class="no-data">Błąd pobierania danych</td></tr>');
                $('#hum-tbody').html('<tr><td colspan="3" class="no-data">Błąd pobierania danych</td></tr>');
            }
        });
    }

    // Renderuje tabele i wykresy
    function renderAll() {
        renderTempTable();
        renderHumTable();
        updateCharts();
    }

    // Render tabeli z temperaturą
    function renderTempTable() {
        const total = allData.length;
        const slice = allData.slice((tempPage - 1) * perPage, tempPage * perPage);

        $('#temp-count').text(total + ' odczytów');

        if (slice.length === 0) {
            $('#temp-tbody').html('<tr><td colspan="3" class="no-data">Brak danych dla wybranego okresu</td></tr>');
            $('#temp-pagination').html('');
            return;
        }

        let rows = '';
        slice.forEach(function(row) {
            let statusClass = row.status === 'ok' ? 'status-ok' : 'status-error';
            rows += `<tr>
                <td>${formatDate(row.timestamp)}</td>
                <td>${row.temperature ?? '-'}</td>
                <td class="${statusClass}">${row.status}</td>
            </tr>`;
        });
        $('#temp-tbody').html(rows);

        buildPagination('temp-pagination', tempPage, total, function(page) {
            tempPage = page;
            renderTempTable();
        });
    }

    // Render tabeli z wilgotnością
    function renderHumTable() {
        const total = allData.length;
        const slice = allData.slice((humPage - 1) * perPage, humPage * perPage);

        $('#hum-count').text(total + ' odczytów');

        if (slice.length === 0) {
            $('#hum-tbody').html('<tr><td colspan="3" class="no-data">Brak danych dla wybranego okresu</td></tr>');
            $('#hum-pagination').html('');
            return;
        }

        let rows = '';
        slice.forEach(function(row) {
            let statusClass = row.status === 'ok' ? 'status-ok' : 'status-error';
            rows += `<tr>
                <td>${formatDate(row.timestamp)}</td>
                <td>${row.humidity ?? '-'}</td>
                <td class="${statusClass}">${row.status}</td>
            </tr>`;
        });
        $('#hum-tbody').html(rows);

        buildPagination('hum-pagination', humPage, total, function(page) {
            humPage = page;
            renderHumTable();
        });
    }

    // Aktualizuje dane na wykresach
    function updateCharts() {
        const slice = allData.slice(0, 30);
        const labels = slice.map(r => formatDate(r.timestamp));

        tempChart.data.labels = labels;
        tempChart.data.datasets[0].data = slice.map(r => r.temperature);
        tempChart.update();

        humChart.data.labels = labels;
        humChart.data.datasets[0].data = slice.map(r => r.humidity);
        humChart.update();
    }

    // Buduje paginację
    function buildPagination(containerId, currentPage, total, onPageChange) {
        const totalPages = Math.ceil(total / perPage);
        if (totalPages <= 1) {
            $('#' + containerId).html('');
            return;
        }

        let html = '<nav><ul class="pagination pagination-sm justify-content-center mt-1 mb-0">';

        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}">‹</a>
        </li>`;

        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
        }

        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">›</a>
        </li>`;

        html += '</ul></nav>';

        $('#' + containerId).html(html);

        $('#' + containerId + ' .page-link').on('click', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            if (!page || page < 1 || page > totalPages) return;
            onPageChange(page);
        });
    }

    $('.btn-period').on('click', function() {
        $('.btn-period').removeClass('active');
        $(this).addClass('active');

        const period = $(this).data('period');
        const today = new Date();
        let from = new Date();
        let to = new Date();

        if (period === 'all') {
            $('#date_from').val('');
            $('#date_to').val('');
            fetchData();
            return;
        } else if (period === 'yesterday') {
            from.setDate(today.getDate() - 1);
            to.setDate(today.getDate() - 1);
        } else if (period === 'week') {
            from.setDate(today.getDate() - 7);
        } else if (period === 'month') {
            from.setMonth(today.getMonth() - 1);
        }

        $('#date_from').val(from.toISOString().split('T')[0]);
        $('#date_to').val(to.toISOString().split('T')[0]);
        fetchData();
    });

    $('#date_from, #date_to').on('change', function() {
        fetchData();
    });

    fetchData();
</script>
@endsection