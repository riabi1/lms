@extends('frontend.master')

@section('title')
    {{ $course->course_name }} | Learn | Easy Learning
@endsection

@section('home')
    <!-- En-tête du Cours -->
    <section class="header-menu-area bg-dark py-3">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="text-white fs-15 mb-0">
                    <a href="{{ url('course/details/'.$course->id.'/'.$course->course_name_slug) }}" class="text-white text-decoration-none">{{ $course->course_name }}</a>
                </h4>
                <span class="text-white">Progression : {{ round($progress->progress, 2) }}%</span>
            </div>
        </div>
    </section>

    <!-- Contenu Principal -->
    <section class="course-dashboard py-5 bg-light">
        <div class="container-fluid">
            <div class="row">
                <!-- Colonne Principale -->
                <div class="col-lg-8">
                    <!-- Visualiseur de Leçon -->
                    <div class="lecture-viewer mb-5 bg-white p-4 rounded shadow-sm">
                        <iframe width="100%" height="500" id="videoContainer" src="" title="Course Lecture" frameborder="0" class="rounded" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        <div id="lectureContent" class="mt-4 text-dark" style="font-size: 16px; line-height: 1.6;"></div>
                        <!-- Débogage : Afficher l'URL actuelle -->
                        <div id="debugUrl" class="mt-2 text-muted">URL actuelle : <span></span></div>
                    </div>

                    <!-- Onglet Overview uniquement -->
                    <div class="lecture-tabs">
                        <ul class="nav nav-tabs border-0 mb-4" id="courseTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active text-dark font-weight-bold px-4 py-2" id="overview-tab" data-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Overview</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="courseTabsContent">
                            <!-- Onglet Aperçu -->
                            <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                                <div class="overview-content bg-white p-4 rounded shadow-sm">
                                    <h3 class="fs-20 font-weight-bold mb-4 text-dark">About this course</h3>
                                    <p class="text-muted mb-4">{{ $course->course_title }}</p>
                                    <hr class="my-4">
                                    <h4 class="fs-16 font-weight-bold mb-3 text-dark">Details</h4>
                                    <ul class="list-unstyled text-dark">
                                        <li class="mb-2"><strong>Skill level:</strong> {{ $course->label ?? 'N/A' }}</li>
                                        <li class="mb-2"><strong>Resources:</strong> {{ $course->resources ?? 'N/A' }}</li>
                                        <li class="mb-2"><strong>Duration:</strong> {{ $course->duration ?? 'N/A' }}</li>
                                        <li class="mb-2"><strong>Certificate:</strong> {{ $course->certificate ?? 'N/A' }}</li>
                                    </ul>
                                    <hr class="my-4">
                                    <h4 class="fs-16 font-weight-bold mb-3 text-dark">Description</h4>
                                    <div class="text-dark">{!! $course->description ?? '<p>No description available.</p>' !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Barre Latérale avec Course Content -->
                <div class="col-lg-4">
                    <div class="course-sidebar sticky-top" style="top: 20px;">
                        <div class="card border-0 shadow-sm rounded">
                            <div class="card-header bg-dark text-white p-3 rounded-top">
                                <h3 class="fs-18 font-weight-bold mb-0">Course Content</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="course-content">
                                    @foreach ($sections as $section)
                                        @php
                                            $lectures = $section->lectures;
                                            $totalLectures = $lectures->count();
                                        @endphp
                                        <div class="card mb-0 border-0">
                                            <div class="card-header bg-white d-flex justify-content-between align-items-center p-3 border-bottom">
                                                <h5 class="mb-0 font-weight-bold text-dark">{{ $section->section_title }}</h5>
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-dark btn-sm dropdown-toggle" 
                                                            type="button" 
                                                            id="dropdownSidebarMenu{{ $section->id }}" 
                                                            data-toggle="dropdown" 
                                                            aria-haspopup="true" 
                                                            aria-expanded="false">
                                                        {{ $totalLectures }} Lectures
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right p-2" aria-labelledby="dropdownSidebarMenu{{ $section->id }}" style="min-width: 300px;">
                                                        @foreach ($lectures as $lecture)
                                                            <div class="dropdown-item d-flex align-items-center py-2">
                                                                <input type="checkbox" 
                                                                       class="lecture-checkbox mr-3" 
                                                                       {{ in_array($lecture->id, $progress->completed_lectures ?? []) ? 'checked' : '' }} 
                                                                       data-lecture-id="{{ $lecture->id }}">
                                                                <span class="lecture-title flex-grow-1 cursor-pointer text-dark" 
                                                                      data-video-url="{{ Storage::url('upload/lectures/' . $lecture->url) }}" 
                                                                      data-content="{!! $lecture->content !!}" 
                                                                      data-lecture-id="{{ $lecture->id }}">
                                                                    {{ $lecture->lecture_title }}
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
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
        </div>
    </section>

    <!-- Script de Gestion des Leçons et Progression -->
    <script type="text/javascript">
        function loadLecture(videoUrl, textContent) {
            const video = document.getElementById('videoContainer');
            const contentDiv = document.getElementById('lectureContent');
            const debugSpan = document.getElementById('debugUrl').querySelector('span');

            // Débogage : Afficher l'URL
            debugSpan.textContent = videoUrl || 'Aucune URL définie';

            // Charger la vidéo
            if (videoUrl && videoUrl.trim() !== '') {
                const fullVideoUrl = videoUrl.startsWith('http') ? videoUrl : window.location.origin + videoUrl;
                video.classList.remove('d-none');
                video.src = fullVideoUrl;
                console.log('Chargement de la vidéo : ' + fullVideoUrl);
            } else {
                video.classList.add('d-none');
                video.src = '';
                console.log('Aucune vidéo à charger');
            }

            // Charger le contenu textuel
            if (textContent && textContent.trim() !== '') {
                contentDiv.innerHTML = textContent;
            } else {
                contentDiv.innerHTML = '<p class="text-muted">Aucun contenu disponible pour cette leçon.</p>';
            }
        }

        // Charger une leçon au clic sur le titre
        document.querySelectorAll('.lecture-title').forEach(lecture => {
            lecture.addEventListener('click', (e) => {
                e.stopPropagation();
                const videoUrl = lecture.getAttribute('data-video-url');
                const textContent = lecture.getAttribute('data-content');
                loadLecture(videoUrl, textContent);
            });
        });

        // Charger la première leçon au démarrage
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const firstLecture = document.querySelector('.lecture-title');
                if (firstLecture) {
                    firstLecture.click();
                }
            }, 500);
        });

        // Gérer les coches pour la progression
        document.querySelectorAll('.lecture-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function(e) {
                e.stopPropagation();
                const lectureId = this.getAttribute('data-lecture-id');
                const completed = this.checked ? 1 : 0;

                $.ajax({
                    url: '{{ route("user.lecture.completed") }}',
                    method: 'POST',
                    data: {
                        lecture_id: lectureId,
                        course_id: {{ $course->id }},
                        completed: completed,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Progression mise à jour pour la leçon ' + lectureId);
                            $('.header-menu-area .text-white').text('Progression : ' + response.progress.toFixed(2) + '%');
                        }
                    },
                    error: function(xhr) {
                        console.error('Erreur lors de la mise à jour de la progression');
                        checkbox.checked = !completed;
                    }
                });
            });
        });
    </script>
@endsection