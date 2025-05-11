<?php

namespace App\Listeners;

use App\Events\ReviewUpdated;
use App\Services\CourseTagService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCourseTagsOnReview implements ShouldQueue
{
    protected $courseTagService;

    public function __construct(CourseTagService $courseTagService)
    {
        $this->courseTagService = $courseTagService;
    }

    /**
     * Handle the event.
     */
    public function handle(ReviewUpdated $event)
    {
        $review = $event->review;
        if ($review->reviewable_type === 'App\\Models\\Course') {
            $course = $review->reviewable;
            if ($course) {
                $this->courseTagService->assignTags($course);
            }
        }
    }
}