@extends('layouts.app')

@section('content')
    <!-- Full-width Top Bar -->
    <div class="w-100 p-3" style="background-color: #224D3D; color: white;">
        <h4 class="mb-0">Greetings, {{ Auth::user()->name }}!</h4>
    </div>

    <!-- Page Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Notifications -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold">Notifications</div>
                    <div class="card-body">
                        <p class="text-muted">No new notifications.</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Deadlines -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold">Upcoming Deadlines</div>
                    <div class="card-body">
                        <p class="text-muted">No deadlines yet.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Progress -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Course Progress</div>
                    <div class="card-body">
                        <p class="text-muted">Progress details will appear here.</p>
                        <!-- Example progress bar -->
                        <div class="progress mt-2" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 45%;"
                                aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                                45%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
