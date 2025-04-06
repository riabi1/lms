<!DOCTYPE html>
<html lang="en">
<head>
    @include('User.mycourses.body.header')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->course_name }} - EdaaLearning</title>
    
    <!-- External Libraries (Unchanged) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"> <!-- Added for icons -->

   <style>
        /* Global Styles */
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5; /* Light gray for contrast with dark palette */
            color: #09122C; /* Primary dark blue for text */
            margin: 0;
            padding: 0;
        }
        .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Preloader */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .spinner .path {
            stroke: #BE3144; /* Bright red for spinner */
            animation: dash 1.5s ease-in-out infinite;
        }
        @keyframes dash {
            0% { stroke-dasharray: 1, 150; stroke-dashoffset: 0; }
            50% { stroke-dasharray: 90, 150; stroke-dashoffset: -35; }
            100% { stroke-dasharray: 90, 150; stroke-dashoffset: -124; }
        }

        /* Header */
        .header-menu-area {
            background: linear-gradient(90deg, #09122C, #872341); /* Dark blue to deep red */
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .header-menu-content {
            padding: 0 20px;
        }
        .course-dashboard-header-title a {
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            transition: color 0.3s;
        }
        .course-dashboard-header-title a:hover {
            color: #E17564; /* Soft peach on hover */
        }
        .theme-picker-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            padding: 8px;
            border-radius: 50%;
            transition: background 0.3s;
        }
        .theme-picker-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .nav-right-button .btn {
            background: #BE3144; /* Bright red for buttons */
            color: #fff;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 20px;
            transition: all 0.3s;
        }
        .nav-right-button .btn:hover {
            background: #872341; /* Deep red on hover */
            transform: translateY(-2px);
        }
        .certificate-btn {
            background: linear-gradient(90deg, #BE3144, #E17564); /* Bright red to soft peach */
        }

        /* Course Dashboard */
        .course-dashboard-wrap {
            background: #fff;
            border-radius: 15px;
            margin: 30px auto;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }
        .progress {
            height: 25px;
            border-radius: 12px;
            background: #e5e5e5;
            overflow: hidden;
        }
        .progress-bar {
            background:  #118B50; /* Deep red to bright red */
            font-weight: 600;
            font-size: 1rem;
            transition: width 0.5s ease;
        }
        .lecture-video-item {
            background: #D99D81;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        #mediaContainer iframe, #mediaContainer video {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        /* Tabs */
        .lecture-tab-body {
            background: #D99D81;
            border-bottom: 1px solid #D99D81;
        }
        .nav-tabs .nav-link {
            padding: 15px 30px;
            font-weight: 600;
            color: #09122C; /* Dark blue for inactive tabs */
            border: none;
            transition: all 0.3s;
        }
        .nav-tabs .nav-link.active {
            color: #BE3144; /* Bright red for active tab */
            border-bottom: 3px solid #BE3144;
        }

        /* Overview Section */
        .lecture-overview-wrap {
            padding: 30px;
        }
        .lecture-overview-item h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #09122C; /* Dark blue for headings */
            margin-bottom: 15px;
        }
        .lecture-overview-stats-wrap {
            background: #D99D81;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
       
        
        .generic-list-item li {
            font-size: 1.1rem;
            margin-bottom: 12px;
        }
        .generic-list-item li span {
            font-weight: 600;
            color: #872341; /* Deep red for labels */
        }
        .section-block {
            height: 1px;
            background: #fff;
            margin: 25px 0;
        }

        /* Notes Section */
        .note-card {
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }
        .note-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }
        .note-card.favorite {
            border: 2px solid #E17564; /* Soft peach for favorite border */
        }
        .bg-light-blue { background: #09122C; color: #fff; } /* Dark blue */
        .bg-light-green { background: #872341; color: #fff; } /* Deep red */
        .bg-light-yellow { background: #BE3144; color: #fff; } /* Bright red */
        .bg-light-pink { background: #E17564; color: #09122C; } /* Soft peach */
        .note-header {
            font-size: 1.25rem;
            font-weight: 600;
            color: #09122C; /* Dark blue for headers (adjusts on colored backgrounds) */
        }
        .btn-add-note {
            background: #BE3144; /* Bright red for add button */
            color: #fff;
            padding: 10px 25px;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-add-note:hover {
            background: #872341; /* Deep red on hover */
            transform: translateY(-2px);
        }
        .alert-info {
            background: #E17564; /* Soft peach for alert */
            color: #09122C; /* Dark blue text */
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-weight: 500;
        }

        /* Sidebar */
        .course-dashboard-sidebar-wrap {
            background: #D99D81;
            padding: 20px;
            border-left: 1px solid #e5e5e5;
            border-radius: 10px;
        }
        .course-dashboard-side-heading h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #09122C; /* Dark blue for heading */
        }
    
    .course-item-link {
        padding: 20px;
        border-radius: 8px;
        background: #BE3144;
        margin-bottom: 10px;
        transition: background 0.3s;
    }

    .course-item-link.active, .course-item-link:hover {
        background: #BE3144;
        color: #fff;
    }

    .course-item-link.active h4, .course-item-link:hover h4 {
        color: #fff; /* Assure que le titre suit */
    }

        #accordionCourseExample {
        background: #09122C;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
        .accordion .btn-link {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
            color: #09122C; /* Dark blue for text */
            font-weight: 600;
            text-decoration: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s;
        }
        .accordion .btn-link:hover {
            background: #E17564; /* Soft peach on hover */
            color: #09122C;
        }
        .course-item-link {
            padding: 15px;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .course-item-link.active {
            background: #BE3144; /* Bright red for active item */
            color: #fff;
        }
        .course-item-link.active h4 {
            color: #fff;
        }

        /* Modals */
        .modal-content {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        .modal-header {
            background: #09122C; /* Dark blue for modal header */
            color: #fff;
            border-radius: 15px 15px 0 0;
            padding: 20px;
        }
        .modal-body {
            padding: 25px;
        }
        .theme-btn {
            background: #BE3144; /* Bright red for buttons */
            color: #fff;
            padding: 10px 25px;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .theme-btn:hover {
            background: #872341; /* Deep red on hover */
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Global Certificate Variables (Unchanged) -->
    @php
        $hasCertificate = $course->certificate === 'yes'; 
        $allLecturesCompleted = $progressPercentage == 100;
        $allQuizzesPassed = $course->quizzes->isEmpty() || $course->quizzes->every(function ($quiz) use ($quizAttempts) {
            return $quizAttempts->where('quiz_id', $quiz->id)->where('passed', true)->isNotEmpty();
        });
        $certificateAvailable = $hasCertificate && $allLecturesCompleted && $allQuizzesPassed;
    @endphp

    <!-- Preloader (Enhanced Design) -->
    <div class="preloader">
        <div class="loader">
            <svg class="spinner" viewBox="0 0 50 50">
                <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
            </svg>
        </div>
    </div>

    <!-- Header Area (Enhanced Design) -->
    <section class="header-menu-area">
        <div class="header-menu-content bg-dark">
            <div class="container-fluid">
                <div class="main-menu-content d-flex align-items-center">
                    <div class="logo-box logo--box">
                        <div class="theme-picker d-flex align-items-center">
                            <button class="theme-picker-btn dark-mode-btn" title="Dark mode">
                                <svg class="svg-icon-color-white" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                </svg>
                            </button>
                            <button class="theme-picker-btn light-mode-btn" title="Light mode">
                                <svg viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="5"></circle>
                                    <line x1="12" y1="1" x2="12" y2="3"></line>
                                    <line x1="12" y1="21" x2="12" y2="23"></line>
                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                    <line x1="1" y1="12" x2="3" y2="12"></line>
                                    <line x1="21" y1="12" x2="23" y2="12"></line>
                                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="course-dashboard-header-title pl-4">
                        <a href="{{ url('course/details/' . $course->id . '/' . $course->course_name_slug) }}">{{ $course->course_name }}</a>
                    </div>
                    <div class="menu-wrapper ml-auto">
                        <div class="theme-picker d-flex align-items-center mr-3">
                            <button class="theme-picker-btn dark-mode-btn" title="Dark mode">
                                <svg class="svg-icon-color-white" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                </svg>
                            </button>
                            <button class="theme-picker-btn light-mode-btn" title="Light mode">
                                <svg viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="5"></circle>
                                    <line x1="12" y1="1" x2="12" y2="3"></line>
                                    <line x1="12" y1="21" x2="12" y2="23"></line>
                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                    <line x1="1" y1="12" x2="3" y2="12"></line>
                                    <line x1="21" y1="12" x2="23" y2="12"></line>
                                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                </svg>
                            </button>
                        </div>
                        <div class="nav-right-button d-flex align-items-center">
                            @if ($certificateAvailable)
                                <a href="{{ route('course.certificate', $course->id) }}" class="btn theme-btn theme-btn-sm theme-btn-transparent lh-26 text-white mr-2 certificate-btn">
                                    <i class="bx bx-certification mr-1"></i> Download Certificate
                                </a>
                            @endif
                            <a href="#" class="btn theme-btn theme-btn-sm theme-btn-transparent lh-26 text-white mr-2" data-toggle="modal" data-target="#ratingModal">
                                <i class="bx bx-star mr-1"></i> Leave a Rating
                            </a>
                            <a href="#" class="btn theme-btn theme-btn-sm theme-btn-transparent lh-26 text-white mr-2" data-toggle="modal" data-target="#shareModal">
                                <i class="bx bx-share-alt mr-1"></i> Share
                            </a>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Course Dashboard (Enhanced Design) -->
    <section class="course-dashboard">
        <div class="course-dashboard-wrap">
            <div class="course-dashboard-container d-flex">
                <div class="course-dashboard-column">
                    <div class="lecture-viewer-container">
                        <div class="progress mb-4">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercentage }}%;" 
                                 aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $progressPercentage }}% Complete
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
                        @if ($progressPercentage == 100 && $course->quizzes->isNotEmpty())
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
                                        <p>{{ $course->course_title }}</p>
                                    </div>
                                    <div class="section-block"></div>
                                    <div class="lecture-overview-item">
                                        <div class="lecture-overview-stats-wrap d-flex">
                                            <div class="lecture-overview-stats-item">
                                                <h3 class="fs-16 font-weight-semi-bold pb-2">By the numbers</h3>
                                            </div>
                                            <div class="lecture-overview-stats-item">
                                                <ul class="generic-list-item">
                                                    <li><span>Skill level:</span> {{ $course->label }}</li>
                                                </ul>
                                            </div>
                                            <div class="lecture-overview-stats-item">
                                                <ul class="generic-list-item">
                                                    <li><span>Resources:</span> {{ $course->sections->flatMap->lectures->pluck('resources_description')->filter()->count() > 0 ? 'Available' : 'None' }}</li>
                                                    <li><span>Course length:</span> {{ $course->duration }} total hours</li>
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
                                                <p class="pb-3">Get Your Certification By completing the entire course{{ $course->quizzes->isNotEmpty() ? ' and quizzes' : '' }}</p>
                                                @if ($certificateAvailable)
                                                    <a href="{{ route('course.certificate', $course->id) }}" class="btn theme-btn theme-btn-sm theme-btn-transparent lh-26 text-white mr-2 certificate-btn">
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
                                                <p>{!! $course->description !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="question-and-ans">

                                <div class="lecture-overview-wrap lecture-quest-wrap">
                                    <div class="new-question-wrap">
                                        <button class="btn theme-btn theme-btn-transparent back-to-question-btn"><i class="bx bx-arrow-back mr-1"></i>Back to all questions</button>
                                        <div class="new-question-body pt-40px">
                                            <h3 class="fs-20 font-weight-semi-bold">My question relates to</h3>
                                            <form method="POST" action="" class="pt-4">
                                                @csrf
                                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                                <input type="hidden" name="instructor_id" value="{{ $course->instructor_id }}">
                                                <div class="custom-control-wrap">
                                                    <div class="custom-control custom-radio mb-3 pl-0">
                                                        <input type="text" name="subject" class="form-control form--control pl-3" placeholder="Subject" required>
                                                    </div>
                                                    <div class="custom-control custom-radio mb-3 pl-0">
                                                        <textarea class="form-control form--control pl-3" name="question" rows="4" placeholder="Write your response..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="btn-box text-center">
                                                    <button type="submit" class="btn theme-btn w-100">Submit Question <i class="bx bx-arrow-right icon ml-1"></i></button>
                                                </div>
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
                                            <div class="question-btn-box pt-35px text-center">
                                                <button class="btn theme-btn theme-btn-transparent w-100" type="button">See More</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="notes" role="tabpanel">
                                <div class="lecture-overview-wrap pt-4">
                                    <form action="{{ route('mycourses.notes.store', $course->id) }}" method="POST" class="mb-4">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Title</label>
                                                <input type="text" class="form-control" name="title" placeholder="e.g., Key Concept" required>
                                                @error('title')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Due Date (optional)</label>
                                                <input type="date" class="form-control" name="due_date">
                                                @error('due_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Your Note</label>
                                            <textarea class="form-control" name="content" rows="3" placeholder="Write something personal..." required></textarea>
                                            @error('content')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" name="favorite" id="favorite{{ $course->id }}" value="1">
                                                <label class="form-check-label" for="favorite{{ $course->id }}">Mark as Favorite</label>
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
                                        <button type="submit" class="btn btn-add-note"><i class="bx bx-plus"></i> Add Note</button>
                                    </form>
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
                                    @if (!$course->notes || $course->notes->isEmpty())
                                        <p class="text-muted">No notes yet. Start your journey with EdaaLearning!</p>
                                    @else
                                        @foreach ($course->notes as $note)
                                            <div class="note-card {{ $note->color ?? 'bg-light-blue' }} {{ $note->favorite ? 'favorite' : '' }}">
                                                <div class="note-display">
                                                    <div class="note-header">
                                                        {{ $note->title }}
                                                        @if ($note->favorite)
                                                            <i class="bx bx-star text-warning ms-2"></i>
                                                        @endif
                                                    </div>
                                                    <p class="mb-2">{{ $note->content }}</p>
                                                    @if ($note->due_date)
                                                        <small class="text-muted">Due: {{ \Carbon\Carbon::parse($note->due_date)->format('F j, Y') }}</small><br>
                                                    @endif
                                                    <small class="text-muted">Added on {{ $note->created_at->format('F j, Y, H:i') }}</small>
                                                    <div class="note-actions mt-2">
                                                        <button class="btn btn-primary btn-sm edit-note-btn" data-note-id="{{ $note->id }}"><i class="bx bx-edit"></i> Edit</button>
                                                        <form action="{{ route('mycourses.notes.destroy', $note->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this note?')">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="note-edit-form" style="display: none;">
                                                    <form action="{{ route('mycourses.notes.update', $note->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="mb-3">
                                                            <label class="form-label">Title</label>
                                                            <input type="text" class="form-control" name="title" value="{{ $note->title }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Your Note</label>
                                                            <textarea class="form-control" name="content" rows="3" required>{{ $note->content }}</textarea>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Due Date (optional)</label>
                                                                <input type="date" class="form-control" name="due_date" value="{{ $note->due_date ? $note->due_date->format('Y-m-d') : '' }}">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label">Note Color</label>
                                                                <select class="form-control" name="color">
                                                                    <option value="bg-light-blue" {{ $note->color == 'bg-light-blue' ? 'selected' : '' }}>Light Blue</option>
                                                                    <option value="bg-light-green" {{ $note->color == 'bg-light-green' ? 'selected' : '' }}>Light Green</option>
                                                                    <option value="bg-light-yellow" {{ $note->color == 'bg-light-yellow' ? 'selected' : '' }}>Light Yellow</option>
                                                                    <option value="bg-light-pink" {{ $note->color == 'bg-light-pink' ? 'selected' : '' }}>Light Pink</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3 form-check">
                                                            <input type="checkbox" class="form-check-input" name="favorite" id="edit-favorite{{ $note->id }}" value="1" {{ $note->favorite ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="edit-favorite{{ $note->id }}">Mark as Favorite</label>
                                                        </div>
                                                        <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
                                                        <button type="button" class="btn btn-secondary btn-sm cancel-edit" data-note-id="{{ $note->id }}">Cancel</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quiz Modal (Enhanced Design) -->
    @if ($progressPercentage == 100 && $course->quizzes->isNotEmpty())
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
                                        $attemptCount = $quizAttempts->where('quiz_id', $quiz->id)->count();
                                        $lastAttempt = $quizAttempts->where('quiz_id', $quiz->id)->sortByDesc('completed_at')->first();
                                        $hasPassed = $quizAttempts->where('quiz_id', $quiz->id)->where('passed', true)->isNotEmpty();
                                    @endphp
                                    @if (!$hasPassed && $attemptCount < 3)
                                        <form action="{{ route('course.quiz.submit', ['courseId' => $course->id, 'quizId' => $quiz->id]) }}" method="POST">
                                            @csrf
                                            @foreach ($quiz->questions as $question)
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $question->question_text }}</label>
                                                    @php
                                                        $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                                        if (!is_array($options)) { $options = []; }
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
                                            {{ $lastAttempt->completed_at->addMinute()->toTimeString() }} to try again.</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Rating Modal (Enhanced Design) -->
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
                    <form id="ratingForm" method="POST" action="{{ route('course.rate', $course->id) }}">
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

    <!-- Share Modal (Enhanced Design) -->
    <div class="modal fade modal-container" id="shareModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-gray">
                    <h5 class="modal-title fs-19 font-weight-semi-bold">Share this course</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true" class="bx bx-x"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="copy-to-clipboard">
                        <span class="success-message">Copied!</span>
                        <div class="input-group">
                            <input type="text" class="form-control form--control copy-input pl-3" value="{{ url('course/details/' . $course->id . '/' . $course->course_name_slug) }}" readonly>
                            <div class="input-group-append">
                                <button class="btn theme-btn theme-btn-sm copy-btn shadow-none"><i class="bx bx-copy mr-1"></i> Copy</button>
                            </div>
                        </div>
                    </div>
                </div>
              
                </div>
            </div>
        </div>
    </div>

    <!-- Resources Modal (Enhanced Design) -->
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

    <!-- JavaScript (Unchanged Functionality) -->
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
            $('.mark-completed').on('change', function() {
                const lectureId = $(this).data('lecture-id');
                const completed = $(this).is(':checked') ? 1 : 0;
                const $checkbox = $(this);

                $.ajax({
                    url: '{{ route("course.markLectureCompleted", $course->id) }}',
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
                            if (response.progress === 100 && {{ $course->quizzes->isNotEmpty() ? 'true' : 'false' }}) {
                                $('#quizModal').modal('show');
                            }
                        } else {
                            toastr.error(response.message);
                            $checkbox.prop('checked', !completed);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'An error occurred');
                        $checkbox.prop('checked', !completed);
                    }
                });
            });

            $('.edit-note-btn').on('click', function() {
                const noteId = $(this).data('note-id');
                $('#note-' + noteId + ' .note-display').hide();
                $('#note-' + noteId + ' .note-edit-form').show();
            });

            $('.cancel-edit').on('click', function() {
                const noteId = $(this).data('note-id');
                $('#note-' + noteId + ' .note-edit-form').hide();
                $('#note-' + noteId + ' .note-display').show();
            });

            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

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
        });
        
    </script>

    @include('User.mycourses.body.footer')
</body>
</html>