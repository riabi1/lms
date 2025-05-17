@extends('instructor.layout.instructor_layout')

@section('title', 'Report Details | Easy Learning')

@section('instructor')
    <div class="page-content p-4">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-flex align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('instructor.dashboard') }}" class="text-decoration-none"><i class="bx bx-home-alt me-1"></i>Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('instructor.reports.index') }}" class="text-decoration-none">My Reports</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Report Details</li>
                </ol>
            </nav>
            <div class="ms-auto">
                <a href="{{ route('instructor.reports.index') }}" class="btn btn-outline-secondary px-4">Back to Reports</a>
            </div>
        </div>

        <!-- Report Details Card -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0 fw-semibold">Report Details: {{ $report->title }}</h4>
            </div>
            <div class="card-body p-4">
                <!-- Session Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Report Information -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <h5 class="mb-3 fw-semibold">Report Information</h5>
                        <dl class="row g-2">
                            <dt class="col-4 fw-medium">Title:</dt>
                            <dd class="col-8">{{ $report->title }}</dd>
                            <dt class="col-4 fw-medium">Type:</dt>
                            <dd class="col-8">{{ ucfirst(str_replace('_', ' ', $report->type)) }}</dd>
                            <dt class="col-4 fw-medium">Course:</dt>
                            <dd class="col-8">{{ $report->course?->course_title ?? 'N/A' }}</dd>
                            <dt class="col-4 fw-medium">Status:</dt>
                            <dd class="col-8">
                                <span class="badge 
                                    @if ($report->status === 'pending') bg-warning text-dark
                                    @elseif ($report->status === 'fixed') bg-success
                                    @else bg-danger @endif rounded-pill px-3 py-2">
                                    {{ $report->status === 'pending' ? 'Pending Review' : 
                                       ($report->status === 'fixed' ? 'Resolved' : 'Not Resolved') }}
                                </span>
                            </dd>
                            <dt class="col-4 fw-medium">Submitted At:</dt>
                            <dd class="col-8">{{ \Illuminate\Support\Facades\Date::parse($report->created_at)->format('d M Y, H:i') }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3 fw-semibold">Description</h5>
                        <p class="border p-3 rounded bg-light" style="min-height: 120px;">{{ $report->description }}</p>
                        <h5 class="mb-3 mt-4 fw-semibold">Resolution Notes</h5>
                        <p class="border p-3 rounded bg-light" style="min-height: 120px;">
                            {{ $report->resolution_notes ?? 'No resolution notes provided.' }}
                        </p>
                    </div>
                </div>

                <!-- Status History -->
                <div class="row mt-5">
                    <div class="col-12">
                        <h5 class="mb-3 fw-semibold">Status History</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Status</th>
                                        <th>Resolution Notes</th>
                                        <th>Changed By</th>
                                        <th>Changed At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($report->statusHistories as $history)
                                        <tr>
                                            <td>{{ $history->status === 'pending' ? 'Pending Review' : 
                                                   ($history->status === 'fixed' ? 'Resolved' : 'Not Resolved') }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($history->resolution_notes ?? 'N/A', 50) }}</td>
                                            <td>{{ $history->changedBy ? ($history->changedBy->name ?? $history->changedBy->instructor_name ?? 'N/A') : 'N/A' }}</td>
                                            <td>{{ \Illuminate\Support\Facades\Date::parse($history->changed_at)->format('d M Y, H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No status history available.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Feedback Form -->
                @if (in_array($report->status, ['fixed', 'not_fixed']) && !$report->feedback)
                    <div class="row mt-5">
                        <div class="col-12">
                            <h5 class="mb-3 fw-semibold">Provide Feedback</h5>
                            <form action="{{ route('instructor.reports.feedback', $report->id) }}" method="POST" class="p-4 bg-light rounded-3">
                                @csrf
                                <div class="mb-3">
                                    <label for="feedback" class="form-label fw-medium">Feedback <span class="text-danger">*</span></label>
                                    <textarea name="feedback" id="feedback" class="form-control" rows="5" placeholder="Enter your feedback here..." required>{{ old('feedback') }}</textarea>
                                    @error('feedback')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary px-4">Submit Feedback</button>
                            </form>
                        </div>
                    </div>
                @elseif ($report->feedback)
                    <div class="row mt-5">
                        <div class="col-12">
                            <h5 class="mb-3 fw-semibold">Your Feedback</h5>
                            <p class="border p-3 rounded bg-light" style="min-height: 120px;">{{ $report->feedback }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection