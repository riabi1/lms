<!DOCTYPE html>
<html lang="en">
<head>
    @include('User.mycourses.body.header')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>
<body>
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
                        <a href="{{ url('course/details/' . $course->id . '/' . $course->course_name_slug) }}" class="text-white fs-15">{{ $course->course_name }}</a>
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
                            @php
                                $totalLectures = $course->sections->flatMap->lectures->count();
                                $completedLectures = array_filter($progress, fn($completed) => $completed == 1);
                                $progressPercentage = $totalLectures > 0 ? round((count($completedLectures) / $totalLectures) * 100) : 0;
                                $allQuizzesPassed = $course->quizzes->isEmpty() || $course->quizzes->every(function ($quiz) use ($quizAttempts) {
                                    return $quizAttempts->where('quiz_id', $quiz->id)->where('passed', true)->isNotEmpty();
                                });
                                $hasCertificate = $course->certificate === 'yes';
                            @endphp
                            @if ($hasCertificate && $progressPercentage == 100 && $allQuizzesPassed)
                                <a href="{{ route('course.certificate.download', $course->id) }}" class="btn theme-btn theme-btn-sm theme-btn-transparent lh-26 text-white mr-2 certificate-btn">
                                    <i class="la la-certificate mr-1"></i> Download Certificate
                                </a>
                            @endif
                            <a href="#" class="btn theme-btn theme-btn-sm theme-btn-transparent lh-26 text-white mr-2" data-toggle="modal" data-target="#ratingModal"><i class="la la-star mr-1"></i> Leave a Rating</a>
                            <a href="#" class="btn theme-btn theme-btn-sm theme-btn-transparent lh-26 text-white mr-2" data-toggle="modal" data-target="#shareModal"><i class="la la-share mr-1"></i> Share</a>
                            <div class="generic-action-wrap generic--action-wrap">
                                <div class="dropdown">
                                    <a class="action-btn" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="la la-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                                        <a class="dropdown-item" href="#">Favorite this course</a>
                                    </div>
                                </div>
                            </div>
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
                            <div class="mt-4">
                                <button type="button" class="btn theme-btn" data-toggle="modal" data-target="#quizModal">
                                    Take Quizzes
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="lecture-video-detail">
                        <div class="lecture-tab-body bg-gray p-4">
                            <ul class="nav nav-tabs generic-tab" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link" id="search-tab" data-toggle="tab" href="#search" role="tab" aria-controls="search" aria-selected="false">
                                        <i class="la la-search"></i>
                                    </a>
                                </li>
                                <li class="nav-item mobile-menu-nav-item">
                                    <a class="nav-link" id="course-content-tab" data-toggle="tab" href="#course-content" role="tab" aria-controls="course-content" aria-selected="false">
                                        Course Content
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">
                                        Overview
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="question-and-ans-tab" data-toggle="tab" href="#question-and-ans" role="tab" aria-controls="question-and-ans" aria-selected="false">
                                        Question & Ans
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="announcements-tab" data-toggle="tab" href="#announcements" role="tab" aria-controls="announcements" aria-selected="false">
                                        Announcements
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="lecture-video-detail-body">
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade" id="search" role="tabpanel" aria-labelledby="search-tab">
                                    <div class="search-course-wrap pt-40px">
                                        <form action="#" class="pb-5">
                                            <div class="input-group">
                                                <input class="form-control form--control form--control-gray pl-3" type="text" name="search" placeholder="Search course content">
                                                <div class="input-group-append">
                                                    <button class="btn theme-btn"><span class="la la-search"></span></button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="search-results-message text-center">
                                            <h3 class="fs-24 font-weight-semi-bold pb-1">Start a new search</h3>
                                            <p>To find captions, lectures or resources</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="course-content" role="tabpanel" aria-labelledby="course-content-tab">
                                    <div class="mobile-course-menu pt-4">
                                        <div class="accordion generic-accordion generic--accordion" id="mobileCourseAccordionCourseExample">
                                            @foreach ($course->sections as $section)
                                                <div class="card">
                                                    <div class="card-header" id="mobileCourseHeading{{ $section->id }}">
                                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#mobileCourseCollapse{{ $section->id }}" aria-expanded="true" aria-controls="mobileCourseCollapse{{ $section->id }}">
                                                            <i class="la la-angle-down"></i>
                                                            <i class="la la-angle-up"></i>
                                                            <span class="fs-15">{{ $section->section_title }}</span>
                                                            <span class="course-duration">
                                                                <span>{{ count($section->lectures) }}</span>
                                                                <span>{{ $section->total_duration }}min</span>
                                                            </span>
                                                        </button>
                                                    </div>
                                                    <div id="mobileCourseCollapse{{ $section->id }}" class="collapse" aria-labelledby="mobileCourseHeading{{ $section->id }}" data-parent="#mobileCourseAccordionCourseExample">
                                                        <div class="card-body p-0">
                                                            <ul class="curriculum-sidebar-list">
                                                                @foreach ($section->lectures as $lecture)
                                                                    <li class="course-item-link {{ $loop->first ? 'active' : '' }}">
                                                                        <div class="course-item-content-wrap">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" 
                                                                                       class="custom-control-input mark-completed" 
                                                                                       id="mobileCourseCheckbox{{ $lecture->id }}" 
                                                                                       data-lecture-id="{{ $lecture->id }}" 
                                                                                       {{ isset($progress[$lecture->id]) && $progress[$lecture->id] ? 'checked' : '' }}>
                                                                                <label class="custom-control-label custom--control-label" for="mobileCourseCheckbox{{ $lecture->id }}"></label>
                                                                            </div>
                                                                            <div class="course-item-content">
                                                                                <h4 class="fs-15 lecture-title" 
                                                                                    data-video-local="{{ $lecture->video ? Storage::url($lecture->video) : '' }}" 
                                                                                    data-video-url="{{ $lecture->url }}" 
                                                                                    data-content="{!! $lecture->content !!}">
                                                                                    {{ $lecture->lecture_title }}
                                                                                </h4>
                                                                                <div class="courser-item-meta-wrap">
                                                                                    <p class="course-item-meta"><i class="la la-play-circle"></i>{{ $lecture->duration }}min</p>
                                                                                </div>
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
                                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
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
                                                        <li><span>Resources:</span> {{ $course->resources ?? 'None' }}</li>
                                                        <li><span>Video length:</span> {{ $course->duration }} total hours</li>
                                                        <li><span>Certificate:</span> {{ $course->certificate === 'yes' ? 'Yes' : 'No' }}</li>
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
                                                    <p class="pb-3">Get Your Certification By completing the entire course and quizzes</p>
                                                    @if ($hasCertificate && $progressPercentage == 100 && $allQuizzesPassed)
                                                        <a href="{{ route('course.certificate.download', $course->id) }}" class="btn theme-btn theme-btn-sm theme-btn-transparent lh-26 text-white mr-2 certificate-btn">
                                                            <i class="la la-certificate mr-1"></i> Download Certificate
                                                        </a>
                                                    @elseif ($hasCertificate && $progressPercentage < 100)
                                                        <p class="text-muted">Complete all lectures to unlock the certificate.</p>
                                                    @elseif ($hasCertificate && !$allQuizzesPassed)
                                                        <p class="text-muted">Pass all quizzes to unlock the certificate.</p>
                                                    @else
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
                                                <div class="lecture-overview-stats-item lecture-overview-stats-wide-item lecture-description">
                                                    <h3 class="fs-16 font-weight-semi-bold pb-2">From {{ $course->instructor->name ?? 'the Author' }}</h3>
                                                    <p>{!! $course->description !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="question-and-ans" role="tabpanel" aria-labelledby="question-and-ans-tab">
                                    <div class="lecture-overview-wrap lecture-quest-wrap">
                                        <div class="new-question-wrap">
                                            <button class="btn theme-btn theme-btn-transparent back-to-question-btn"><i class="la la-reply mr-1"></i>Back to all questions</button>
                                            <div class="new-question-body pt-40px">
                                                <h3 class="fs-20 font-weight-semi-bold">My question relates to</h3>
                                                <form method="post" action="" class="pt-4">
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
                                                        <button type="submit" class="btn theme-btn w-100">Submit Question <i class="la la-arrow-right icon ml-1"></i></button>
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
                                <div class="tab-pane fade" id="announcements" role="tabpanel" aria-labelledby="announcements-tab">
                                    <div class="lecture-overview-wrap">
                                        <div class="lecture-overview-item">
                                            <h3 class="fs-24 font-weight-semi-bold pb-2">Announcements</h3>
                                            <p>No announcements available yet.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="course-dashboard-sidebar-column">
                    <button class="sidebar-open" type="button"><i class="la la-angle-left"></i> Course content</button>
                    <div class="course-dashboard-sidebar-wrap custom-scrollbar-styled">
                        <div class="course-dashboard-side-heading d-flex align-items-center justify-content-between">
                            <h3 class="fs-18 font-weight-semi-bold">Course content</h3>
                            <button class="sidebar-close" type="button"><i class="la la-times"></i></button>
                        </div>
                        <div class="course-dashboard-side-content">
                            <div class="accordion generic-accordion generic--accordion" id="accordionCourseExample">
                                @foreach ($course->sections as $section)
                                    <div class="card">
                                        <div class="card-header" id="headingOne{{ $section->id }}">
                                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne{{ $section->id }}" aria-expanded="true" aria-controls="collapseOne{{ $section->id }}">
                                                <i class="la la-angle-down"></i>
                                                <i class="la la-angle-up"></i>
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

    <!-- Quiz Modal -->
    @if ($progressPercentage == 100 && $course->quizzes->isNotEmpty())
        <div class="modal fade modal-container" id="quizModal" tabindex="-1" role="dialog" aria-labelledby="quizModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom-gray">
                        <h5 class="modal-title fs-19 font-weight-semi-bold" id="quizModalTitle">Course Quizzes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-times"></span>
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
                                                    @foreach ($question->options as $option)
                                                        <div class="form-check">
                                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" class="form-check-input" required>
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

    <!-- Scroll Top -->
    <div id="scroll-top">
        <i class="la la-arrow-up" title="Go top"></i>
    </div>

    <!-- Modals -->
    <div class="modal fade modal-container" id="ratingModal" tabindex="-1" role="dialog" aria-labelledby="ratingModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-gray">
                    <div class="pr-2">
                        <h5 class="modal-title fs-19 font-weight-semi-bold lh-24" id="ratingModalTitle">How would you rate this course?</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="la la-times"></span>
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

    <div class="modal fade modal-container" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-gray">
                    <h5 class="modal-title fs-19 font-weight-semi-bold" id="shareModalTitle">Share this course</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="la la-times"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="copy-to-clipboard">
                        <span class="success-message">Copied!</span>
                        <div class="input-group">
                            <input type="text" class="form-control form--control copy-input pl-3" value="{{ url('course/details/' . $course->id . '/' . $course->course_name_slug) }}">
                            <div class="input-group-append">
                                <button class="btn theme-btn theme-btn-sm copy-btn shadow-none"><i class="la la-copy mr-1"></i> Copy</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-top-gray">
                    <ul class="social-icons social-icons-styled">
                        <li><a href="#" class="facebook-bg"><i class="la la-facebook"></i></a></li>
                        <li><a href="#" class="twitter-bg"><i class="la la-twitter"></i></a></li>
                        <li><a href="#" class="instagram-bg"><i class="la la-instagram"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-container" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-gray">
                    <div class="pr-2">
                        <h5 class="modal-title fs-19 font-weight-semi-bold lh-24" id="reportModalTitle">Report Abuse</h5>
                        <p class="pt-1 fs-14 lh-24">Flagged content is reviewed by Aduca staff to determine whether it violates Terms of Service or Community Guidelines. If you have a question or technical issue, please contact our
                            <a href="contact.html" class="text-color hover-underline">Support team here</a>.
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="la la-times"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="">
                        @csrf
                        <div class="input-box">
                            <label class="label-text">Select Report Type</label>
                            <div class="form-group">
                                <div class="select-container w-auto">
                                    <select class="select-container-select" name="report_type" required>
                                        <option value="">-- Select One --</option>
                                        <option value="1">Inappropriate Course Content</option>
                                        <option value="2">Inappropriate Behavior</option>
                                        <option value="3">Aduca Policy Violation</option>
                                        <option value="4">Spammy Content</option>
                                        <option value="5">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="input-box">
                            <label class="label-text">Write Message</label>
                            <div class="form-group">
                                <textarea class="form-control form--control pl-3" name="message" placeholder="Provide additional details here..." rows="5" required></textarea>
                            </div>
                        </div>
                        <div class="btn-box text-right pt-2">
                            <button type="button" class="btn font-weight-medium mr-3" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn theme-btn theme-btn-sm lh-30">Submit <i class="la la-arrow-right icon ml-1"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
                if (videoId) {
                    return `https://www.youtube.com/embed/${videoId}`;
                }
            } else if (url && url.includes('youtu.be/')) {
                const videoId = url.split('youtu.be/')[1]?.split('?')[0];
                if (videoId) {
                    return `https://www.youtube.com/embed/${videoId}`;
                }
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

            if (textContent && textContent.trim() !== "") {
                contentDiv.innerHTML = textContent;
            } else {
                contentDiv.innerHTML = "<p>No additional content available for this lecture.</p>";
            }
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
                const courseId = {{ $course->id }};

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
                            $('.progress-bar').css('width', response.progress + '%')
                                             .attr('aria-valuenow', response.progress)
                                             .text(response.progress + '% Complete');
                            location.reload(); // Recharger pour mettre à jour les conditions
                        } else {
                            alert(response.message);
                            $(this).prop('checked', !completed);
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred: ' + (xhr.responseJSON?.message || 'Unknown error'));
                        $(this).prop('checked', !completed);
                    }
                });
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
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
                const formData = $(this).serialize();
                const url = $(this).attr('action');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            $('#ratingModal').modal('hide');
                            alert('Thank you for your rating!');
                        }
                    },
                    error: function (xhr) {
                        const error = xhr.responseJSON?.message || 'An error occurred while submitting your rating.';
                        $('#ratingText').text(error).css('color', 'red');
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