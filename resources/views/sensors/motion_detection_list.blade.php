@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/motion_detection_list.css') }}" rel="stylesheet">
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

{{-- Box z danymi --}}
<div class="row">
    <div class="col-12 mb-4" style="padding-left: 0; padding-right: 0;">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fa-solid fa-person-walking me-1"></i> Odczyty czujnika ruchu <span class="text-muted" style="font-size: 11px; padding: 0;">(HC-SR04)</span></h6>
                <span id="dist-count" class="badge bg-secondary">0 odczytów</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="distChart"></canvas>
                </div>
                <table class="table table-sm table-striped mb-1">
                    <thead>
                        <tr>
                            <th>Czas</th>
                            <th>Odległość (cm)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="dist-tbody">
                        <tr><td colspan="3" class="no-data">Ładowanie...</td></tr>
                    </tbody>
                </table>
                <div id="dist-pagination"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const distCtx = document.getElementById('distChart').getContext('2d');

    // buduje wykres odległości
    let distChart = new Chart(distCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Odległość (cm)',
                data: [],
                backgroundColor: 'rgba(46, 134, 171, 0.6)',
                borderColor: '#2E86AB',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    const perPage = 20;
    let distPage = 1;
    let allData = [];

    function formatDate(ts) {
        if (!ts) return '-';
        return ts.substring(0, 16).replace('T', ' ');
    }

    // Formatuje odległośc na metry
    function formatDistance(val) {
        if (val === null || val === undefined) return '-';
        return (val / 100).toFixed(2) + ' m';
    }

    // Ajax pobierający dane
    function fetchData() {
        const dateFrom = $('#date_from').val();
        const dateTo = $('#date_to').val();

        $.ajax({
            url: '/sensors/data/distance',
            method: 'GET',
            data: { date_from: dateFrom, date_to: dateTo },
            success: function(res) {
                allData = res.data;
                distPage = 1;
                renderTable();
                updateChart();
            },
            error: function() {
                $('#dist-tbody').html('<tr><td colspan="3" class="no-data">Błąd pobierania danych</td></tr>');
            }
        });
    }

    // Budowa tabeli z danymi i paginacją
    function renderTable() {
        const total = allData.length;
        const slice = allData.slice((distPage - 1) * perPage, distPage * perPage);

        $('#dist-count').text(total + ' odczytów');

        if (slice.length === 0) {
            $('#dist-tbody').html('<tr><td colspan="3" class="no-data">Brak danych dla wybranego okresu</td></tr>');
            $('#dist-pagination').html('');
            return;
        }

        let rows = '';
        slice.forEach(function(row) {
            let statusClass = row.status === 'ok' ? 'status-ok' : 'status-error';
            rows += `<tr>
                <td>${formatDate(row.timestamp)}</td>
                <td>${formatDistance(row.distance)}</td>
                <td class="${statusClass}">${row.status}</td>
            </tr>`;
        });
        $('#dist-tbody').html(rows);

        buildPagination('dist-pagination', distPage, total, function(page) {
            distPage = page;
            renderTable();
        });
    }

    function updateChart() {

        const slice = allData.slice(0, 30);
        const labels = slice.map(r => formatDate(r.timestamp));
        const values = slice.map(r => r.distance ? (r.distance / 100).toFixed(2) : 0);

        distChart.data.labels = labels;
        distChart.data.datasets[0].data = values;
        distChart.data.datasets[0].label = 'Odległość (m)';
        distChart.update();
    }

    // Budowa paginacji
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