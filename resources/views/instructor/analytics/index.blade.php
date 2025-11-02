@extends('layouts.app') @section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Analytics Dashboard</h1>
        <div class="row"> <!-- Stats Cards (unchanged) -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Quizzes</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalQuizzes }}</div>
                            </div>
                            <div class="col-auto"> <i class="fas fa-clipboard-list fa-2x text-gray-300"></i> </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Students</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                            </div>
                            <div class="col-auto"> <i class="fas fa-user-graduate fa-2x text-gray-300"></i> </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Attempts</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAttempts }}</div>
                            </div>
                            <div class="col-auto"> <i class="fas fa-clipboard-check fa-2x text-gray-300"></i> </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Avg. Score</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $totalAttempts > 0 ? round(\App\Models\QuizAttempt::avg('score'), 1) : '0' }} </div>
                            </div>
                            <div class="col-auto"> <i class="fas fa-chart-line fa-2x text-gray-300"></i> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- Performance Tables -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-white">Quiz Performance</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered" id="quizTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Quiz</th>
                                        <th>Attempts</th>
                                        <th>Avg. Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quizPerformance as $quiz)
                                        <tr>
                                            <td>{{ $quiz['title'] }}</td>
                                            <td>{{ $quiz['attempts'] }}</td>
                                            <td>{{ $quiz['avg_score'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-white">Student Performance</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered" id="studentTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Attempts</th>
                                        <th>Avg. Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($studentPerformance as $student)
                                        <tr>
                                            <td>{{ $student['name'] }}</td>
                                            <td>{{ $student['attempts'] }}</td>
                                            <td>{{ $student['avg_score'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#quizTable').DataTable({
                    "pageLength": 5,
                    "lengthChange": true,
                    "ordering": true,
                    "searching": true,
                    "language": {
                        "search": "Search:",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries"
                    }
                });

                $('#studentTable').DataTable({
                    "pageLength": 5,
                    "lengthChange": true,
                    "ordering": true,
                    "searching": true,
                    "language": {
                        "search": "Search:",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries"
                    }
                });
            });
        </script>
    @endpush
@endsection
