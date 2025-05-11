<?php

namespace App\Listeners;

use App\Events\OrderUpdated;
use App\Services\CourseTagService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCourseTagsOnOrder implements ShouldQueue
{
    protected $courseTagService;

    public function __construct(CourseTagService $courseTagService)
    {
        $this->courseTagService = $courseTagService;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderUpdated $event)
    {
        $order = $event->order;
        $course = $order->course;
        if ($course) {
            $this->courseTagService->assignTags($course);
        }
    }
}