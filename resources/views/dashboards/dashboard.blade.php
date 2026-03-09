@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="dashboard-container">
   <p style="text-align: center;">TEST</p>
</div>

@push('styles')
<style>
    .dashboard-container {
        padding: 20px;
    }
    .page-header {
        margin-bottom: 30px;
    }
</style>
@endpush
@endsection