<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\Log;

class CourseTagService
{
    /**
     * Assigns tags to a course based on reviews and orders.
     *
     * @param Course $course
     * @return array
     */
    public function assignTags(Course $course)
    {
        $tags = [
            'bestseller' => 0,
            'highestrated' => 0,
            'featured' => $course->featured ?? 0, // Preserve existing featured status or default to 0
        ];

        // Bestseller: ≥ 3 paid orders
        $orderCount = Order::where('course_id', $course->id)
            ->where('payment_status', 'paid')
            ->count();
        if ($orderCount >= 3) {
            $tags['bestseller'] = 1;
        }

        // Highestrated: Average rating ≥ 4.5 and ≥ 3 reviews
        $reviews = Review::where('reviewable_type', Course::class)
            ->where('reviewable_id', $course->id)
            ->where('status', 1) // Approved reviews
            ->get();
        $averageRating = $reviews->avg('rating') ?? 0;
        $reviewCount = $reviews->count();
        if ($averageRating >= 4.5 && $reviewCount >= 3) {
            $tags['highestrated'] = 1;
        }

        // Featured: Updated in the last 30 days with ≥ 3 orders
        if ($course->updated_at->isAfter(now()->subDays(30)) && $orderCount >= 3) {
            $tags['featured'] = 1;
        }

        // Update the course
        $course->update([
            'bestseller' => $tags['bestseller'],
            'highestrated' => $tags['highestrated'],
            'featured' => $tags['featured'],
        ]);

        Log::info('Tags assigned to course', [
            'course_id' => $course->id,
            'tags' => $tags,
            'order_count' => $orderCount,
            'average_rating' => $averageRating,
            'review_count' => $reviewCount,
        ]);

        return $tags;
    }
}