<!DOCTYPE html>
<html lang="en">
<head>
    @include('User.mycourses.body.header')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $course->course_name ?? 'My Courses' }} - EdaaLearning</title>
    
    <!-- External Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
    /* Global Styles */
    body {
        font-family: 'Inter', sans-serif;
        background: #EEEEEE;
        color: #2A4759;
        margin: 0;
        padding: 0;
    }
    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
    }
    .preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .header-menu-area {
        background: linear-gradient(90deg, #EAD196, #BF3131);
        padding: 20px 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .header-menu-content {
        padding: 0 20px;
    }
    .course-dashboard-header-title a {
        font-size: 1.6rem;
        font-weight: 800;
        color: #FFFFFF;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .course-dashboard-header-title a:hover {
        color: #F79B72;
    }
    .nav-right-button .btn {
        background: #F79B72;
        color: #FFFFFF;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 24px;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    .nav-right-button .btn:hover {
        background: #E07A4F;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    .certificate-btn {
        background: linear-gradient(90deg, #F79B72, #F7BFA3);
        color: #FFFFFF;
    }
    .course-dashboard-wrap {
        background: #FFFFFF;
        border: 1px solid #DDDDDD;
        border-radius: 16px;
        margin: 32px auto;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }
    .progress {
        height: 28px;
        border-radius: 14px;
        background: #DDDDDD;
        overflow: hidden;
    }
    .progress-bar {
        background: #A4B465;
        font-weight: 600;
        font-size: 1rem;
        transition: width 0.5s ease;
    }
    #mediaContainer iframe, #mediaContainer video {
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }
    .nav-tabs .nav-link {
        padding: 16px 32px;
        font-weight: 600;
        color: #007074;
        border: none;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link:hover, .nav-tabs .nav-link.active {
        color: #F79B72;
        background: #DDDDDD;
        border-radius: 8px;
    }
    .lecture-overview-wrap {
        padding: 32px;
    }
    .lecture-overview-item h3 {
        font-size: 1.6rem;
        font-weight: 800;
        color: #007074;
        margin-bottom: 16px;
    }
    .note-card {
        padding: 24px;
        margin-bottom: 24px;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    .note-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    .note-card.favorite {
        border: 3px solid #F79B72;
    }
    .note-card.bg-light-gray {
        background: #E5E5E5;
    }
    .note-card.bg-light-teal {
        background: #D9E3E5;
    }
    .note-card.bg-light-coral {
        background: #FBE8E1;
    }
    .note-card.bg-light-white {
        background: #F5F5F5;
    }
    .btn-add-note {
        background: #F79B72;
        color: #FFFFFF;
        padding: 12px 28px;
        border-radius: 24px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        animation: pulse 2s infinite;
    }
    .btn-add-note:hover {
        background: #E07A4F;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        animation: none;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    .modal-header {
        background: #007074;
        color: #FFFFFF;
        border-radius: 16px 16px 0 0;
        padding: 24px;
    }
    .modal-body {
        padding: 28px;
        background: #EEEEEE;
    }
    .note-form-container {
        display: none;
    }
    .filter-buttons {
        margin-bottom: 16px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .btn-filter {
        background: #007074;
        color: #FFFFFF;
        padding: 10px 24px;
        border-radius: 24px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    .btn-filter:hover {
        background: #007074;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    .btn-filter.active {
        background: #007074;
        transform: scale(1.05);
    }
    .btn-favorite-filter {
        background: #F79B72;
        color: #FFFFFF;
    }
    .btn-favorite-filter:hover {
        background: #E07A4F;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 12px;
    }
    .tag {
        background: #F79B72;
        color: #FFFFFF;
        padding: 4px 10px;
        border-radius: 14px;
        font-size: 0.9rem;
    }
    .favorite-star {
        cursor: pointer;
        font-size: 1.3rem;
        color: #F79B72;
        transition: transform 0.2s ease;
    }
    .favorite-star:hover {
        transform: scale(1.2);
    }
    .sort-container, .tag-filter-container {
        margin-bottom: 16px;
    }
    .tag-filter {
        padding: 8px 14px;
        border-radius: 14px;
        font-size: 0.95rem;
        border: 1px solid #DDDDDD;
    }
    .is-invalid {
        border-color: #D32F2F !important;
    }
    .invalid-feedback {
        color: #D32F2F;
        font-size: 0.9rem;
    }
    .text-muted {
        color: #666666 !important;
    }
    /* Question Edit Form */
    .edit-question-form {
        display: none;
        margin-top: 15px;
    }
    .question-actions .btn {
        margin-right: 5px;
    }
    </style>
</head>
<body>
    <!-- Global Certificate Variables -->
    @php
        $hasCertificate = isset($course->certificate) && $course->certificate === 'yes';
        $allLecturesCompleted = isset($progressPercentage) && $progressPercentage == 100;
        $allQuizzesPassed = !isset($course->quizzes) || $course->quizzes->isEmpty() || $course->quizzes->every(function ($quiz) use ($quizAttempts) {
            return isset($quizAttempts) && $quizAttempts->where('quiz_id', $quiz->id)->where('passed', true)->isNotEmpty();
        });
        $certificateAvailable = $hasCertificate && $allLecturesCompleted && $allQuizzesPassed;
    @endphp

    <!-- Preloader -->
    <div class="preloader">
        <div class="loader">
            <svg class="spinner" viewBox="0 0 50 50">
                <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
            </svg>
        </div>
    </div>

    <!-- Header Area -->
    <section class="header-menu-area">
        <div class="header-menu-content bg-dark">
            <div class="container-fluid">
                <div class="main-menu-content d-flex align-items-center">
                    <div class="course-dashboard-header-title pl-4">
                    <a href="{{ route('home') }}" class="logo">
                      <img src="{{ asset('frontend/images/logo2.png') }}" alt="Logo" class="img-fluid" style="max-height: 70px; filter: brightness(110%);">
                    </a>
                        <a href="{{ url('course/details/' . ($course->id ?? '') . '/' . ($course->course_name_slug ?? '')) }}">{{ $course->course_name ?? 'My Courses' }}</a>
                    </div>
                    <div class="menu-wrapper ml-auto">
                        <div class="nav-right-button d-flex align-items-center">
                            @if ($certificateAvailable)
                                <a href="{{ route('course.certificate', $course->id ?? '') }}" class="btn theme-btn theme-btn-sm theme-btn-transparent lh-26 text-white mr-2 certificate-btn">
                                    <i class="bx bx-certification mr-1"></i> Download Certificate
                                </a>
                            @endif
                            <a href="#" class="btn theme-btn theme-btn-sm theme-btn-transparent lh-26 text-white mr-2" data-toggle="modal" data-target="#ratingModal">
                                <i class="bx bx-star mr-1"></i> Leave a Rating
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Course Dashboard -->
    <section class="course-dashboard">
        <div class="course-dashboard-wrap">
            <div class="course-dashboard-container d-flex">
                <div class="course-dashboard-column">
                    <div class="lecture-viewer-container">
                        <div class="progress mb-4">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercentage ?? 0 }}%;" 
                                 aria-valuenow="{{ $progressPercentage ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $progressPercentage ?? 0 }}% Complete
                            </div>
                        </div>
                        <div class="lecture-video-item">
                            <div id="mediaContainer">
                                <iframe width="100%" height="500" id="videoIframe" class="d-none" src="" title="Course Lecture Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                <video width="100%" height="500" id="videoPlayer" class="d-none" controls>
                                    <source src="" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                            <div id="lectureContent" class="mt-4" style="font-size: 14px; text-align: left; padding: 0 40px;"></div>
                        </div>
                        @if (isset($progressPercentage) && $progressPercentage == 100 && isset($course->quizzes) && !$course->quizzes->isEmpty())
                            <div class="mt-4 text-center">
                                <button type="button" class="btn theme-btn" data-toggle="modal" data-target="#quizModal">
                                    <i class="bx bx-check-square mr-1"></i> Take Quizzes
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="lecture-video-detail">
                        <div class="lecture-tab-body bg-gray p-4">
                            <ul class="nav nav-tabs generic-tab" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab">Overview</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="question-and-ans-tab" data-toggle="tab" href="#question-and-ans" role="tab">Question & Ans</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="notes-tab" data-toggle="tab" href="#notes" role="tab">Notes</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade" id="course-content" role="tabpanel">
                                <!-- Mobile course content unchanged -->
                            </div>
                            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                <div class="lecture-overview-wrap">
                                    <div class="lecture-overview-item">
                                        <h3 class="fs-24 font-weight-semi-bold pb-2">About this course</h3>
                                        <p>{{ $course->course_title ?? 'No title available' }}</p>
                                    </div>
                                    <div class="section-block"></div>
                                    <div class="lecture-overview-item">
                                        <div class="lecture-overview-stats-wrap d-flex">
                                            <div class="lecture-overview-stats-item">
                                                <h3 class="fs-16 font-weight-semi-bold pb-2">By the numbers</h3>
                                            </div>
                                            <div class="lecture-overview-stats-item">
                                                <ul class="generic-list-item">
                                                    <li><span>Skill level:</span> {{ $course->label ?? 'N/A' }}</li>
                                                </ul>
                                            </div>
                                            <div class="lecture-overview-stats-item">
                                                <ul class="generic-list-item">
                                                    <li><span>Resources:</span> {{ isset($course->sections) ? ($course->sections->flatMap->lectures->pluck('resources_description')->filter()->count() > 0 ? 'Available' : 'None') : 'None' }}</li>
                                                    <li><span>Certificate:</span> {{ $hasCertificate ? 'Yes' : 'No' }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="section-block"></div>
                                    <div class="lecture-overview-item">
                                        <div class="lecture-overview-stats-wrap d-flex">
                                            <div class="lecture-overview-stats-item">
                                                <h3 class="fs-16 font-weight-semi-bold pb-2">Certificates</h3>
                                            </div>
                                            <div class="lecture-overview-stats-item lecture-overview-stats-wide-item">
                                                <p class="pb-3">Get Your Certification By completing the entire course{{ isset($course->quizzes) && !$course->quizzes->isEmpty() ? ' and quizzes' : '' }}</p>
                                                @if ($certificateAvailable)
                                                    <a href="{{ route('course.certificate', $course->id ?? '') }}" class="btn theme-btn theme-btn-sm theme-btn-transparent lh-26 text-white mr-2 certificate-btn">
                                                        <i class="bx bx-certification mr-1"></i> Download Certificate
                                                    </a>
                                                @elseif ($hasCertificate && !$allLecturesCompleted)
                                                    <p class="text-muted">Complete all lectures to unlock the certificate.</p>
                                                @elseif ($hasCertificate && !$allQuizzesPassed)
                                                    <p class="text-muted">Pass all quizzes to unlock the certificate.</p>
                                                @elseif (!$hasCertificate)
                                                    <p class="text-muted">This course does not offer a certificate.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="section-block"></div>
                                    <div class="lecture-overview-item">
                                        <div class="lecture-overview-stats-wrap d-flex">
                                            <div class="lecture-overview-stats-item">
                                                <h3 class="fs-16 font-weight-semi-bold pb-2">Description</h3>
                                            </div>
                                            <div class="lecture-overview-stats-item lecture-overview-stats-wide-item">
                                                <h3 class="fs-16 font-weight-semi-bold pb-2">From {{ $course->courseable->name ?? 'the Author' }}</h3>
                                                <p>{!! $course->description ?? 'No description available' !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="question-and-ans" role="tabpanel">
                                <div class="lecture-overview-wrap lecture-quest-wrap">
                                    <div class="new-question-wrap">
                                        <button class="btn theme-btn theme-btn-transparent back-to-question-btn d-none"><i class="bx bx-arrow-back mr-1"></i>Back to all questions</button>
                                        <div class="new-question-body pt-40px">
                                            <h3 class="fs-20 font-weight-semi-bold">Ask a Question</h3>
                                            <form id="questionForm" action="{{ route('course.question.submit', $course->id ?? '') }}" method="POST" class="mb-4">
                                                @csrf
                                                <div class="mb-3">
                                                    <label class="form-label">Your Question <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="question_text" rows="4" placeholder="Type your question here..." required></textarea>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <button type="submit" class="btn theme-btn">Submit Question</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="question-overview-result-wrap">
                                        <div class="lecture-overview-item">
                                            <div class="question-overview-result-header d-flex align-items-center justify-content-between">
                                                <h3 class="fs-17 font-weight-semi-bold">Questions in this course</h3>
                                                <button class="btn theme-btn theme-btn-sm theme-btn-transparent ask-new-question-btn">Ask a new question</button>
                                            </div>
                                        </div>
                                        <div class="section-block"></div>
                                        <div class="lecture-overview-item mt-0">
                                            <div id="questionsList">
                                                @if (isset($course->questions) && !$course->questions->isEmpty())
                                                    @foreach ($course->questions as $question)
                                                        <div class="question-card card mb-3" data-question-id="{{ $question->id }}">
                                                            <div class="card-body">
                                                                <p><strong>{{ $question->user->name }}</strong> <small class="text-muted">{{ $question->created_at->format('F j, Y, H:i') }}</small></p>
                                                                <p class="question-text">{{ $question->question_text }}</p>
                                                                @if (Auth::id() === $question->user_id)
                                                                    <div class="question-actions mt-2">
                                                                        <button class="btn btn-sm btn-warning edit-question-btn" data-question-id="{{ $question->id }}"><i class="bi bi-pencil"></i> Edit</button>
                                                                        <button class="btn btn-sm btn-danger delete-question-btn" data-question-id="{{ $question->id }}"><i class="bi bi-trash"></i> Delete</button>
                                                                    </div>
                                                                    <div class="edit-question-form">
                                                                        <form class="edit-question-form-inner" data-question-id="{{ $question->id }}" action="{{ route('course.question.update', $course->id) }}" method="POST">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <input type="hidden" name="question_id" value="{{ $question->id }}">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Edit Your Question <span class="text-danger">*</span></label>
                                                                                <textarea class="form-control" name="question_text" rows="4" placeholder="Type your question here..." required>{{ $question->question_text }}</textarea>
                                                                                <div class="invalid-feedback"></div>
                                                                            </div>
                                                                            <button type="submit" class="btn theme-btn">Update Question</button>
                                                                            <button type="button" class="btn btn-secondary btn-sm cancel-edit-question" data-question-id="{{ $question->id }}">Cancel</button>
                                                                        </form>
                                                                    </div>
                                                                @endif
                                                                @if ($question->status === 'answered' && $question->answers->isNotEmpty())
                                                                    <div class="answers mt-3">
                                                                        <h6 class="fs-15 font-weight-semi-bold">Answers</h6>
                                                                        @foreach ($question->answers as $answer)
                                                                            <div class="answer-card card mb-2">
                                                                                <div class="card-body">
                                                                                    <p><strong>{{ $answer->instructor->name }}</strong> <small class="text-muted">{{ $answer->created_at->format('F j, Y, H:i') }}</small></p>
                                                                                    <p>{{ $answer->answer_text }}</p>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @elseif ($question->status === 'pending')
                                                                    <p class="text-muted">Awaiting instructor response...</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-muted">No questions yet. Be the first to ask!</p>
                                                @endif
                                            </div>
                                            <div class="question-btn-box pt-35px text-center">
                                                <button class="btn theme-btn theme-btn-transparent w-100" type="button" id="loadMoreQuestions" style="display: none;">See More</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="notes" role="tabpanel">
                                <div class="lecture-overview-wrap pt-4">
                                    <div class="filter-buttons">
                                        <button class="btn btn-filter filter-all active">All Notes</button>
                                        <button class="btn btn-filter btn-favorite-filter filter-favorites">Favorites</button>
                                        <button class="btn btn-add-note add-note-btn"><i class="bx bx-plus"></i> Add Note</button>
                                    </div>
                                    <div class="sort-container">
                                        <label class="form-label me-2">Sort by:</label>
                                        <select class="form-select d-inline-block w-auto" id="sortNotes">
                                            <option value="due_date_asc">Due Date (Ascending)</option>
                                            <option value="due_date_desc">Due Date (Descending)</option>
                                            <option value="created_at_desc" selected>Created At (Newest)</option>
                                            <option value="created_at_asc">Created At (Oldest)</option>
                                            <option value="title_asc">Title (A-Z)</option>
                                            <option value="title_desc">Title (Z-A)</option>
                                        </select>
                                    </div>
                                    <div class="tag-filter-container">
                                        <label class="form-label me-2">Filter by Tag:</label>
                                        <select class="form-select d-inline-block w-auto tag-filter" id="tagFilter">
                                            <option value="">All Tags</option>
                                            @if (isset($course->notes) && !$course->notes->isEmpty())
                                                @foreach ($course->notes->pluck('tags')->filter()->flatMap(fn($tags) => explode(',', $tags))->unique()->map(fn($tag) => trim($tag)) as $tag)
                                                    <option value="{{ $tag }}">{{ $tag }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="note-form-container" id="noteFormContainer">
                                        <form id="noteForm" action="{{ route('mycourses.notes.store', $course->id ?? '') }}" method="POST" class="mb-4">
                                            @csrf
                                            <input type="hidden" name="_method" id="formMethod" value="POST">
                                            <input type="hidden" name="note_id" id="noteId">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="title" placeholder="e.g., Key Concept" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Due Date (optional)</label>
                                                    <input type="date" class="form-control" name="due_date">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Your Note <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="content" rows="3" placeholder="Write something personal..." required></textarea>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tags (comma-separated, optional)</label>
                                                <input type="text" class="form-control" name="tags" placeholder="e.g., urgent,review">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3 form-check">
                                                    <input type="checkbox" class="form-check-input" name="favorite" id="favorite" value="1">
                                                    <label class="form-check-label" for="favorite">Mark as Favorite</label>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Note Color</label>
                                                    <select class="form-control" name="color">
                                                        <option value="bg-light-blue">Light Blue</option>
                                                        <option value="bg-light-green">Light Green</option>
                                                        <option value="bg-light-yellow">Light Yellow</option>
                                                        <option value="bg-light-pink">Light Pink</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-add-note" id="submitNoteBtn">Add Note</button>
                                            <button type="button" class="btn btn-secondary btn-sm" id="resetNoteBtn">Cancel</button>
                                        </form>
                                    </div>
                                    <div class="alert alert-info mb-4">
                                        @php
                                            $motivations = [
                                                "With EdaaLearning, every note is a step towards mastering your skills!",
                                                "EdaaLearning is here for you: turn your ideas into knowledge!",
                                                "Stay motivated with EdaaLearning, your learning partner!",
                                                "At EdaaLearning, your notes reflect your journey to success!"
                                            ];
                                            $randomMotivation = $motivations[array_rand($motivations)];
                                        @endphp
                                        {{ $randomMotivation }}
                                    </div>
                                    <h6 class="mb-3">Your Personal Notes</h6>
                                    <div id="notesList">
                                        @if (!isset($course->notes) || $course->notes->isEmpty())
                                            <p class="text-muted">No notes yet. Start your journey with EdaaLearning!</p>
                                        @else
                                            @foreach ($course->notes as $note)
                                                <div class="note-card {{ $note->color ?? 'bg-light-blue' }} {{ $note->favorite ? 'favorite' : '' }} animate__animated animate__fadeIn" data-note-id="{{ $note->id }}">
                                                    <div class="note-header">
                                                        {{ $note->title }}
                                                        <i class="bx {{ $note->favorite ? 'bxs-star' : 'bx-star' }} favorite-star text-{{ $note->favorite ? 'warning' : 'muted' }} ms-2" data-id="{{ $note->id }}"></i>
                                                    </div>
                                                    <p class="mb-2">{{ $note->content }}</p>
                                                    @if ($note->due_date)
                                                        <small class="text-muted">Due: {{ \Carbon\Carbon::parse($note->due_date)->format('F j, Y') }}</small><br>
                                                    @endif
                                                    @if ($note->tags)
                                                        <div class="tags-container">
                                                            @foreach (explode(',', $note->tags) as $tag)
                                                                <span class="tag">{{ trim($tag) }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    <small class="text-muted">Added on {{ $note->created_at->format('F j, Y, H:i') }}</small>
                                                    <div class="note-actions mt-2">
                                                        <button class="btn btn-primary btn-sm edit-note-btn" data-note-id="{{ $note->id }}">Edit</button>
                                                        <button class="btn btn-danger btn-sm delete-note-btn" data-id="{{ $note->id }}">Delete</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="course-dashboard-sidebar-column">
                    <button class="sidebar-open" type="button"><i class="bx bx-menu"></i> Course content</button>
                    <div class="course-dashboard-sidebar-wrap custom-scrollbar-styled">
                        <div class="course-dashboard-side-heading d-flex align-items-center justify-content-between">
                            <h3 class="fs-18 font-weight-semi-bold">Course content</h3>
                            <button class="sidebar-close" type="button"><i class="bx bx-x"></i></button>
                        </div>
                        <div class="course-dashboard-side-content">
                            <div class="accordion generic-accordion generic--accordion" id="accordionCourseExample">
                                @if (isset($course->sections) && !$course->sections->isEmpty())
                                    @foreach ($course->sections as $section)
                                        <div class="card">
                                            <div class="card-header" id="headingOne{{ $section->id }}">
                                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne{{ $section->id }}" aria-expanded="true" aria-controls="collapseOne{{ $section->id }}">
                                                    <i class="bx bx-chevron-down"></i>
                                                    <span class="fs-15">{{ $section->section_title }}</span>
                                                    <span class="course-duration">
                                                        <span>({{ count($section->lectures) }})</span>
                                                    </span>
                                                </button>
                                            </div>
                                            <div id="collapseOne{{ $section->id }}" class="collapse" aria-labelledby="headingOne{{ $section->id }}" data-parent="#accordionCourseExample">
                                                <div class="card-body p-0">
                                                    <ul class="curriculum-sidebar-list">
                                                        @foreach ($section->lectures as $lecture)
                                                            <li class="course-item-link {{ $loop->first ? 'active' : '' }}">
                                                                <div class="course-item-content-wrap">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" 
                                                                               class="custom-control-input mark-completed" 
                                                                               id="courseCheckbox{{ $lecture->id }}" 
                                                                               data-lecture-id="{{ $lecture->id }}" 
                                                                               {{ isset($progress[$lecture->id]) && $progress[$lecture->id] ? 'checked' : '' }}>
                                                                        <label class="custom-control-label custom--control-label" for="courseCheckbox{{ $lecture->id }}"></label>
                                                                    </div>
                                                                    <div class="course-item-content">
                                                                        <h4 class="fs-15 lecture-title" 
                                                                            data-video-local="{{ $lecture->video ? Storage::url($lecture->video) : '' }}" 
                                                                            data-video-url="{{ $lecture->url }}" 
                                                                            data-content="{!! $lecture->content !!}">
                                                                            {{ $lecture->lecture_title }}
                                                                        </h4>
                                                                        @if ($lecture->resources_description)
                                                                            <p class="course-item-meta">
                                                                                <a href="#" class="text-primary" data-toggle="modal" data-target="#resourcesModal{{ $lecture->id }}">
                                                                                    <i class="bx bx-download"></i> Resources
                                                                                </a>
                                                                            </p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quiz Modal -->
    @if (isset($progressPercentage) && $progressPercentage == 100 && isset($course->quizzes) && !$course->quizzes->isEmpty())
        <div class="modal fade modal-container" id="quizModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom-gray">
                        <h5 class="modal-title fs-19 font-weight-semi-bold">Course Quizzes</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true" class="bx bx-x"></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @foreach ($course->quizzes as $quiz)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="fs-16 font-weight-semi-bold">{{ $quiz->title }}</h6>
                                    <p>{{ $quiz->description ?? 'No description' }}</p>
                                    <p><strong>Time Limit:</strong> {{ $quiz->time_limit ? $quiz->time_limit . ' minutes' : 'No limit' }}</p>
                                    @php
                                        $attemptCount = isset($quizAttempts) ? $quizAttempts->where('quiz_id', $quiz->id)->count() : 0;
                                        $lastAttempt = isset($quizAttempts) ? $quizAttempts->where('quiz_id', $quiz->id)->sortByDesc('completed_at')->first() : null;
                                        $hasPassed = isset($quizAttempts) && $quizAttempts->where('quiz_id', $quiz->id)->where('passed', true)->isNotEmpty();
                                    @endphp
                                    @if (!$hasPassed && $attemptCount < 3)
                                        <form action="{{ route('course.quiz.submit', ['courseId' => $course->id ?? '', 'quizId' => $quiz->id]) }}" method="POST">
                                            @csrf
                                            @foreach ($quiz->questions as $question)
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $question->question_text }}</label>
                                                    @php
                                                        $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                                        $options = is_array($options) ? $options : [];
                                                    @endphp
                                                    @foreach ($options as $key => $option)
                                                        <div class="form-check">
                                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" class="form-check-input" required>
                                                            <label class="form-check-label">{{ $option }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                            <p><strong>Attempts Remaining:</strong> {{ 3 - $attemptCount }}</p>
                                            <button type="submit" class="btn theme-btn">Submit Quiz</button>
                                        </form>
                                    @elseif ($hasPassed)
                                        <p class="text-success"><strong>Quiz Passed!</strong> You have successfully completed this quiz.</p>
                                    @else
                                        <p class="text-danger"><strong>Attempts Exhausted!</strong> Please wait until 
                                            {{ $lastAttempt ? $lastAttempt->completed_at->addMinute()->toTimeString() : 'later' }} to try again.</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Rating Modal -->
    <div class="modal fade modal-container" id="ratingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-gray">
                    <h5 class="modal-title fs-19 font-weight-semi-bold lh-24">How would you rate this course?</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true" class="bx bx-x"></span>
                    </button>
                </div>
                <div class="modal-body text-center py-5">
                    <form id="ratingForm" method="POST" action="{{ route('course.rate', $course->id ?? '') }}">
                        @csrf
                        <input type="hidden" name="rating" id="ratingValue" value="0">
                        <div class="leave-rating mt-5">
                            <input type="radio" name="rate" id="star5" value="5" />
                            <label for="star5" class="fs-45 star-label"></label>
                            <input type="radio" name="rate" id="star4" value="4" />
                            <label for="star4" class="fs-45 star-label"></label>
                            <input type="radio" name="rate" id="star3" value="3" />
                            <label for="star3" class="fs-45 star-label"></label>
                            <input type="radio" name="rate" id="star2" value="2" />
                            <label for="star2" class="fs-45 star-label"></label>
                            <input type="radio" name="rate" id="star1" value="1" />
                            <label for="star1" class="fs-45 star-label"></label>
                            <div class="rating-result-text fs-20 pb-4" id="ratingText">Select a rating</div>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="comment" id="comment" rows="3" placeholder="Add a comment (optional)"></textarea>
                        </div>
                        <button type="submit" class="btn theme-btn mt-3" id="submitRating" disabled>Submit Rating</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resources Modal -->
    @if (isset($course->sections) && !$course->sections->isEmpty())
        @foreach ($course->sections as $section)
            @foreach ($section->lectures as $lecture)
                @if ($lecture->resources_description || $lecture->video)
                    <div class="modal fade modal-container" id="resourcesModal{{ $lecture->id }}" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header border-bottom-gray">
                                    <h5 class="modal-title fs-19 font-weight-semi-bold">{{ $lecture->lecture_title }} - Resources</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span aria-hidden="true" class="bx bx-x"></span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @if ($lecture->resources_description)
                                        <p class="text-muted mb-3">{{ $lecture->resources_description }}</p>
                                    @endif
                                    @if ($lecture->video)
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i class="bx bx-video"></i> 
                                                <a href="{{ Storage::url($lecture->video) }}" target="_blank" class="text-primary">Download Video</a>
                                                <small>(Click to view/download)</small>
                                            </li>
                                        </ul>
                                    @endif
                                </div>
                                <div class="modal-footer justify-content-center border-top-gray">
                                    <button type="button" class="btn theme-btn" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endforeach
    @endif

    <!-- JavaScript -->
    <script type="text/javascript">
        function openFirstLecture() {
            const firstLecture = document.querySelector('.lecture-title');
            if (firstLecture) {
                firstLecture.click();
            }
        }

        function convertToEmbedUrl(url) {
            if (url && url.includes('youtube.com/watch?v=')) {
                const videoId = url.split('v=')[1]?.split('&')[0];
                return videoId ? `https://www.youtube.com/embed/${videoId}` : url;
            } else if (url && url.includes('youtu.be/')) {
                const videoId = url.split('youtu.be/')[1]?.split('?')[0];
                return videoId ? `https://www.youtube.com/embed/${videoId}` : url;
            }
            return url;
        }

        function viewLesson(videoLocal, videoUrl, textContent) {
            const iframe = document.getElementById("videoIframe");
            const videoPlayer = document.getElementById("videoPlayer");
            const videoSource = videoPlayer.querySelector("source");
            const contentDiv = document.getElementById("lectureContent");

            iframe.classList.add("d-none");
            videoPlayer.classList.add("d-none");
            iframe.setAttribute("src", "");
            videoSource.setAttribute("src", "");
            contentDiv.innerHTML = "";

            if (videoLocal && videoLocal.trim() !== "") {
                videoPlayer.classList.remove("d-none");
                videoSource.setAttribute("src", videoLocal);
                videoPlayer.load();
            } else if (videoUrl && videoUrl.trim() !== "") {
                const embedUrl = convertToEmbedUrl(videoUrl);
                iframe.classList.remove("d-none");
                iframe.setAttribute("src", embedUrl);
            }

            contentDiv.innerHTML = textContent && textContent.trim() !== "" 
                ? textContent 
                : "<p>No additional content available for this lecture.</p>";
        }

        document.querySelectorAll('.lecture-title').forEach((lectureTitle) => {
            lectureTitle.addEventListener('click', () => {
                const videoLocal = lectureTitle.getAttribute('data-video-local');
                const videoUrl = lectureTitle.getAttribute('data-video-url');
                const textContent = lectureTitle.getAttribute('data-content');
                viewLesson(videoLocal, videoUrl, textContent);
            });
        });

        window.addEventListener('load', () => {
            openFirstLecture();
        });

        $(document).ready(function() {
            const userId = {{ Auth::id() ?? 0 }};
            console.log('Authenticated user ID:', userId);

            // Lecture Completion
            $('.mark-completed').on('change', function() {
                const lectureId = $(this).data('lecture-id');
                const completed = $(this).is(':checked') ? 1 : 0;
                const $checkbox = $(this);

                $.ajax({
                    url: '{{ route("course.markLectureCompleted", $course->id ?? '') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        lecture_id: lectureId,
                        completed: completed
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('.progress-bar').css('width', response.progress + '%')
                                            .attr('aria-valuenow', response.progress)
                                            .text(response.progress + '% Complete');
                            if (response.progress === 100 && {{ isset($course->quizzes) && !$course->quizzes->isEmpty() ? 'true' : 'false' }}) {
                                $('#quizModal').modal('show');
                            }
                        } else {
                            toastr.error(response.message);
                            $checkbox.prop('checked', !completed);
                        }
                    },
                    error: function(xhr) {
                        console.error('Lecture completion error:', xhr.responseJSON);
                        toastr.error(xhr.responseJSON?.message || 'An error occurred');
                        $checkbox.prop('checked', !completed);
                    }
                });
            });

            // Rating
            $('.leave-rating input').on('change', function () {
                const rating = $(this).val();
                $('#ratingValue').val(rating);
                $('#ratingText').text(`You rated ${rating} star${rating > 1 ? 's' : ''}`);
                $('#submitRating').prop('disabled', false);
            });

            $('.star-label').on('mouseenter', function () {
                $(this).prevAll('label').addBack().css('color', '#f5c518');
            }).on('mouseleave', function () {
                if (!$('.leave-rating input:checked').length) {
                    $('.star-label').css('color', '#ddd');
                }
            });

            $('#ratingForm').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            $('#ratingModal').modal('hide');
                            toastr.success(response.message);
                        }
                    },
                    error: function (xhr) {
                        console.error('Rating submission error:', xhr.responseJSON);
                        toastr.error(xhr.responseJSON?.message || 'An error occurred');
                    }
                });
            });

            $('.copy-btn').on('click', function() {
                const copyInput = $(this).closest('.copy-to-clipboard').find('.copy-input');
                copyInput.select();
                document.execCommand('copy');
                $(this).closest('.copy-to-clipboard').find('.success-message').fadeIn().delay(1000).fadeOut();
            });

            // Notes JavaScript
            const $form = $('#noteForm');
            const $notesList = $('#notesList');
            const $formContainer = $('#noteFormContainer');
            const $sortSelect = $('#sortNotes');
            const $tagFilter = $('#tagFilter');
            let notes = @json(isset($course->notes) ? $course->notes->toArray() : []);
            let currentFilter = 'all';
            let currentTag = '';

            console.log('Initial notes:', notes);

            $formContainer.hide();

            $('.add-note-btn').on('click', function() {
                $formContainer.slideDown();
                $form[0].reset();
                $form.find('.form-control').removeClass('is-invalid');
                $form.find('.invalid-feedback').empty();
                $form.data('mode', 'create');
                $form.attr('action', '{{ route("mycourses.notes.store", $course->id ?? '') }}');
                $('#formMethod').val('POST');
                $('#submitNoteBtn').text('Add Note');
                $('#noteId').remove();
            });

            $('#resetNoteBtn').on('click', function() {
                $form[0].reset();
                $form.find('.form-control').removeClass('is-invalid');
                $form.find('.invalid-feedback').empty();
                $form.data('mode', 'create');
                $form.attr('action', '{{ route("mycourses.notes.store", $course->id ?? '') }}');
                $('#formMethod').val('POST');
                $('#submitNoteBtn').text('Add Note');
                $('#noteId').remove();
                $formContainer.slideUp();
            });

            $form.on('submit', function(e) {
                e.preventDefault();
                const mode = $(this).data('mode') || 'create';
                const url = mode === 'create' ? $(this).attr('action') : '{{ url("mycourses/notes/update") }}/' + $('#noteId').val();
                const formData = $(this).serializeArray();
                if (mode === 'edit') {
                    formData.push({ name: '_method', value: 'PUT' });
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        toastr.success(response.success);
                        if (mode === 'create') {
                            notes.push(response.note);
                        } else {
                            const index = notes.findIndex(n => n.id === response.note.id);
                            if (index !== -1) notes[index] = response.note;
                        }
                        updateTagFilter();
                        populateNotes(filterAndSortNotes());
                        $form[0].reset();
                        $form.find('.form-control').removeClass('is-invalid');
                        $form.find('.invalid-feedback').empty();
                        $form.data('mode', 'create');
                        $form.attr('action', '{{ route("mycourses.notes.store", $course->id ?? '') }}');
                        $('#formMethod').val('POST');
                        $('#submitNoteBtn').text('Add Note');
                        $('#noteId').remove();
                        $formContainer.slideUp();
                    },
                    error: function(xhr) {
                        console.error('Form submission error:', xhr.responseJSON);
                        const errors = xhr.responseJSON?.errors || {};
                        $form.find('.form-control').removeClass('is-invalid');
                        $form.find('.invalid-feedback').empty();
                        Object.keys(errors).forEach(field => {
                            $form.find(`[name="${field}"]`).addClass('is-invalid');
                            $form.find(`[name="${field}"]`).next('.invalid-feedback').text(errors[field][0]);
                        });
                        toastr.error('Please correct the errors in the form.');
                    }
                });
            });

            $('.filter-all').on('click', function() {
                currentFilter = 'all';
                $('.filter-buttons .btn-filter').removeClass('active');
                $(this).addClass('active');
                populateNotes(filterAndSortNotes());
                $formContainer.slideUp();
            });

            $('.filter-favorites').on('click', function() {
                currentFilter = 'favorites';
                $('.filter-buttons .btn-filter').removeClass('active');
                $(this).addClass('active');
                populateNotes(filterAndSortNotes());
                $formContainer.slideUp();
            });

            $tagFilter.on('change', function() {
                currentTag = $(this).val();
                populateNotes(filterAndSortNotes());
            });

            $sortSelect.on('change', function() {
                populateNotes(filterAndSortNotes());
            });

            $notesList.on('click', '.edit-note-btn', function() {
                const noteId = $(this).data('note-id');
                const note = notes.find(n => n.id === noteId);
                if (note) {
                    $form.data('mode', 'edit');
                    $form.attr('action', '{{ url("mycourses/notes/update") }}/' + note.id);
                    $('#formMethod').val('PUT');
                    if (!$('#noteId').length) {
                        $form.append(`<input type="hidden" name="note_id" id="noteId" value="${note.id}">`);
                    }
                    $form.find('[name="title"]').val(note.title);
                    $form.find('[name="content"]').val(note.content);
                    $form.find('[name="due_date"]').val(note.due_date || '');
                    $form.find('[name="favorite"]').prop('checked', note.favorite);
                    $form.find('[name="color"]').val(note.color);
                    $form.find('[name="tags"]').val(note.tags || '');
                    $form.find('.form-control').removeClass('is-invalid');
                    $form.find('.invalid-feedback').empty();
                    $('#submitNoteBtn').text('Update Note');
                    $formContainer.slideDown();
                }
            });

            $notesList.on('click', '.favorite-star', function() {
                const noteId = $(this).data('id');
                $.ajax({
                    url: '{{ url("mycourses/notes/toggle-favorite") }}/' + noteId,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        toastr.success(response.success);
                        const note = notes.find(n => n.id === noteId);
                        if (note) note.favorite = response.favorite;
                        populateNotes(filterAndSortNotes());
                    },
                    error: function(xhr) {
                        console.error('Toggle favorite error:', xhr.responseJSON);
                        toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                    }
                });
            });

            $notesList.on('click', '.delete-note-btn', function() {
                const noteId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You want to delete this note?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url("mycourses/notes/destroy") }}/' + noteId,
                            method: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                toastr.success(response.success);
                                notes = notes.filter(n => n.id !== noteId);
                                updateTagFilter();
                                populateNotes(filterAndSortNotes());
                            },
                            error: function(xhr) {
                                console.error('Delete note error:', xhr.responseJSON);
                                toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                            }
                        });
                    }
                });
            });

            function filterAndSortNotes() {
                let filteredNotes = [...notes];
                
                if (currentFilter === 'favorites') {
                    filteredNotes = filteredNotes.filter(note => note.favorite);
                }

                if (currentTag) {
                    filteredNotes = filteredNotes.filter(note => note.tags && note.tags.split(',').map(tag => tag.trim()).includes(currentTag));
                }

                const sortBy = $sortSelect.val();
                filteredNotes.sort((a, b) => {
                    if (sortBy === 'due_date_asc') {
                        return (a.due_date || '9999-12-31') > (b.due_date || '9999-12-31') ? 1 : -1;
                    } else if (sortBy === 'due_date_desc') {
                        return (a.due_date || '0000-01-01') < (b.due_date || '0000-01-01') ? 1 : -1;
                    } else if (sortBy === 'created_at_asc') {
                        return new Date(a.created_at) - new Date(b.created_at);
                    } else if (sortBy === 'created_at_desc') {
                        return new Date(b.created_at) - new Date(a.created_at);
                    } else if (sortBy === 'title_asc') {
                        return a.title.localeCompare(b.title);
                    } else if (sortBy === 'title_desc') {
                        return b.title.localeCompare(a.title);
                    }
                    return 0;
                });

                return filteredNotes;
            }

            function updateTagFilter() {
                const tags = [...new Set(notes.filter(n => n.tags).flatMap(n => n.tags.split(',').map(tag => tag.trim())))];
                $tagFilter.empty().append('<option value="">All Tags</option>');
                tags.forEach(tag => {
                    $tagFilter.append(`<option value="${tag}" ${tag === currentTag ? 'selected' : ''}>${tag}</option>`);
                });
            }

            function populateNotes(notesToDisplay) {
                $notesList.empty();
                if (notesToDisplay.length === 0) {
                    $notesList.append('<p class="text-muted">No notes yet. Start your journey with EdaaLearning!</p>');
                    return;
                }
                notesToDisplay.forEach(note => {
                    const tagsHtml = note.tags ? note.tags.split(',').map(tag => `<span class="tag">${tag.trim()}</span>`).join(' ') : '';
                    const noteHtml = `
                        <div class="note-card ${note.color ?? 'bg-light-blue'} ${note.favorite ? 'favorite' : ''} animate__animated animate__fadeIn" data-note-id="${note.id}">
                            <div class="note-header">
                                ${note.title}
                                <i class="bx ${note.favorite ? 'bxs-star' : 'bx-star'} favorite-star text-${note.favorite ? 'warning' : 'muted'} ms-2" data-id="${note.id}"></i>
                            </div>
                            <p class="mb-2">${note.content}</p>
                            ${note.due_date ? `<small class="text-muted">Due: ${new Date(note.due_date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</small><br>` : ''}
                            ${tagsHtml ? `<div class="tags-container">${tagsHtml}</div>` : ''}
                            <small class="text-muted">Added on ${new Date(note.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' })}</small>
                            <div class="note-actions mt-2">
                                <button class="btn btn-primary btn-sm edit-note-btn" data-note-id="${note.id}">Edit</button>
                                <button class="btn btn-danger btn-sm delete-note-btn" data-id="${note.id}">Delete</button>
                            </div>
                        </div>`;
                    $notesList.append(noteHtml);
                });
            }

            populateNotes(filterAndSortNotes());

            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            // Question Submission
            $('#questionForm').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Question submission successful:', response);
                        toastr.success(response.message);
                        const questionHtml = `
                            <div class="question-card card mb-3 animate__animated animate__fadeIn" data-question-id="${response.question.id}">
                                <div class="card-body">
                                    <p><strong>${response.question.user_name}</strong> <small class="text-muted">${response.question.created_at}</small></p>
                                    <p class="question-text">${response.question.question_text}</p>
                                    <div class="question-actions mt-2">
                                        <button class="btn btn-sm btn-warning edit-question-btn" data-question-id="${response.question.id}"><i class="bi bi-pencil"></i> Edit</button>
                                        <button class="btn btn-sm btn-danger delete-question-btn" data-question-id="${response.question.id}"><i class="bi bi-trash"></i> Delete</button>
                                    </div>
                                    <div class="edit-question-form">
                                        <form class="edit-question-form-inner" data-question-id="${response.question.id}" action="{{ route('course.question.update', $course->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="question_id" value="${response.question.id}">
                                            <div class="mb-3">
                                                <label class="form-label">Edit Your Question <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="question_text" rows="4" placeholder="Type your question here..." required>${response.question.question_text}</textarea>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <button type="submit" class="btn theme-btn">Update Question</button>
                                            <button type="button" class="btn btn-secondary btn-sm cancel-edit-question" data-question-id="${response.question.id}">Cancel</button>
                                        </form>
                                    </div>
                                    <p class="text-muted">Awaiting instructor response...</p>
                                </div>
                            </div>`;
                        $('#questionsList').prepend(questionHtml);
                        $form[0].reset();
                        $form.find('.form-control').removeClass('is-invalid');
                        $form.find('.invalid-feedback').empty();
                        $('.new-question-body').slideUp();
                        $('.question-overview-result-wrap').show();
                        $('.back-to-question-btn').addClass('d-none');
                        $('.ask-new-question-btn').show();
                    },
                    error: function(xhr) {
                        console.error('Question submission error:', xhr.responseJSON);
                        const errors = xhr.responseJSON?.errors || {};
                        $form.find('.form-control').removeClass('is-invalid');
                        $form.find('.invalid-feedback').empty();
                        Object.keys(errors).forEach(field => {
                            $form.find(`[name="${field}"]`).addClass('is-invalid');
                            $form.find(`[name="${field}"]`).next('.invalid-feedback').text(errors[field][0]);
                        });
                        toastr.error('Please correct the errors in the form.');
                    }
                });
            });

            // Toggle Question Form
            $('.ask-new-question-btn').on('click', function() {
                $('.new-question-body').slideDown();
                $('.question-overview-result-wrap').hide();
                $('.back-to-question-btn').removeClass('d-none');
                $(this).hide();
            });

            $('.back-to-question-btn').on('click', function() {
                $('.new-question-body').slideUp();
                $('.question-overview-result-wrap').show();
                $('.back-to-question-btn').addClass('d-none');
                $('.ask-new-question-btn').show();
            });

            // Edit Question
            $(document).on('click', '.edit-question-btn', function(e) {
                e.preventDefault();
                const questionId = $(this).data('question-id');
                console.log('Edit question clicked, question ID:', questionId);
                const $questionCard = $(`.question-card[data-question-id="${questionId}"]`);
                const $editForm = $questionCard.find('.edit-question-form');
                $questionCard.find('.question-text').hide();
                $questionCard.find('.question-actions').hide();
                $editForm.slideDown();
                $editForm.find('textarea').focus();
            });

            // Cancel Edit Question
            $(document).on('click', '.cancel-edit-question', function(e) {
                e.preventDefault();
                const questionId = $(this).data('question-id');
                console.log('Cancel edit question, question ID:', questionId);
                const $questionCard = $(`.question-card[data-question-id="${questionId}"]`);
                const $editForm = $questionCard.find('.edit-question-form');
                $editForm.slideUp();
                $questionCard.find('.question-text').show();
                $questionCard.find('.question-actions').show();
                $editForm.find('.form-control').removeClass('is-invalid');
                $editForm.find('.invalid-feedback').empty();
            });

            // Submit Edited Question
            $(document).on('submit', '.edit-question-form-inner', function(e) {
                e.preventDefault();
                const $form = $(this);
                const questionId = $form.data('question-id');
                console.log('Submitting edited question, question ID:', questionId);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'PUT',
                    data: $form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Question update successful:', response);
                        toastr.success(response.message);
                        const $questionCard = $(`.question-card[data-question-id="${questionId}"]`);
                        $questionCard.find('.question-text').text(response.question.question_text).show();
                        $questionCard.find('.edit-question-form').slideUp();
                        $questionCard.find('.question-actions').show();
                        $form.find('.form-control').removeClass('is-invalid');
                        $form.find('.invalid-feedback').empty();
                    },
                    error: function(xhr) {
                        console.error('Question update error:', xhr.responseJSON);
                        const errors = xhr.responseJSON?.errors || {};
                        $form.find('.form-control').removeClass('is-invalid');
                        $form.find('.invalid-feedback').empty();
                        Object.keys(errors).forEach(field => {
                            $form.find(`[name="${field}"]`).addClass('is-invalid');
                            $form.find(`[name="${field}"]`).next('.invalid-feedback').text(errors[field][0]);
                        });
                        toastr.error('Please correct the errors in the form.');
                    }
                });
            });

            // Delete Question
            $(document).on('click', '.delete-question-btn', function(e) {
                e.preventDefault();
                const questionId = $(this).data('question-id');
                console.log('Delete question clicked, question ID:', questionId);

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This question will be deleted permanently.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("course.question.destroy", $course->id) }}',
                            method: 'DELETE',
                            data: { question_id: questionId },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                console.log('Question deletion successful:', response);
                                toastr.success(response.message);
                                $(`.question-card[data-question-id="${questionId}"]`).remove();
                                if ($('#questionsList').children().length === 0) {
                                    $('#questionsList').html('<p class="text-muted">No questions yet. Be the first to ask!</p>');
                                }
                            },
                            error: function(xhr) {
                                console.error('Question deletion error:', xhr.responseJSON);
                                toastr.error(xhr.responseJSON?.message || 'Failed to delete the question.');
                            }
                        });
                    }
                });
            });
        });
    </script>

    @include('User.mycourses.body.footer')
</body>
</html>