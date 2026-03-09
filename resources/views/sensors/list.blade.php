@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Temperatura i wilgotność (DHT11)</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Temperatura (°C)</th>
                            <th>Wilgotność (%)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($temperatures as $reading)
                        <tr>
                            <td>{{ $reading->timestamp }}</td>
                            <td>{{ $reading->temperature }}</td>
                            <td>{{ $reading->humidity }}</td>
                            <td>{{ $reading->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Odległość (HC-SR04)</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Odległość (cm)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($distances as $reading)
                        <tr>
                            <td>{{ $reading->timestamp }}</td>
                            <td>{{ $reading->distance }}</td>
                            <td>{{ $reading->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection