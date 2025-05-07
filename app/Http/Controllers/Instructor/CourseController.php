<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\CourseGoal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CourseController extends Controller
{
    public function index()
    {
        $instructorId = Auth::guard('instructor')->id();
        $courses = Course::where('courseable_type', 'App\Models\Instructor')
            ->where('courseable_id', $instructorId)
            ->with('subcategory')
            ->latest('id')
            ->get();
        return view('instructor.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::orderBy('category_name')->get();
        $subcategories = SubCategory::orderBy('subcategory_name')->get();
        return view('instructor.courses.create', compact('categories', 'subcategories'));
    }

    public function store(Request $request)
    {
        \Log::info('StoreCourse Request Data:', $request->all());

        if (!Auth::guard('instructor')->check()) {
            \Log::error('Instructor not authenticated');
            return redirect()->back()->with(['message' => 'You must be logged in as an instructor', 'alert-type' => 'error']);
        }

        try {
            $validated = $request->validate([
                'subcategory_id' => 'required|exists:sub_categories,id',
                'course_title' => 'required|string|max:65535',
                'course_name' => 'required|string|max:65535',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'video' => 'nullable|mimes:mp4,avi,mov|max:102400',
                'description' => 'nullable|string|max:4294967295',
                'label' => 'nullable|in:Beginner,Intermediate,Advanced',
                'resources' => 'nullable|string|max:255',
                'certificate' => 'nullable|in:yes,no',
                'selling_price' => 'nullable|integer|min:0',
                'discount_price' => 'nullable|integer|min:0|lt:selling_price',
                'prerequisites' => 'nullable|string|max:65535',
                'CourseGoals.*' => 'nullable|string|max:255',
            ]);
            \Log::info('Validated Data:', $validated);

            $instructorId = Auth::guard('instructor')->id();
            $courseImage = $this->uploadFile($request->file('image'), 'upload/course_images/thumbnail');
            $courseVideo = $this->uploadFile($request->file('video'), 'upload/course_images/video');

            $courseData = [
                'subcategory_id' => $validated['subcategory_id'],
                'courseable_type' => 'App\Models\Instructor',
                'courseable_id' => $instructorId,
                'course_title' => $validated['course_title'],
                'course_name' => $validated['course_name'],
                'course_name_slug' => Str::slug($validated['course_name']),
                'course_image' => $courseImage,
                'description' => $validated['description'] ?? null,
                'video' => $courseVideo,
                'label' => $validated['label'] ?? null,
                'resources' => $validated['resources'] ?? null,
                'certificate' => $validated['certificate'] ?? null,
                'selling_price' => $validated['selling_price'] ?? null,
                'discount_price' => $validated['discount_price'] ?? null,
                'prerequisites' => $validated['prerequisites'] ?? null,
                'status' => 1,
                'created_at' => Carbon::now(),
            ];

            \Log::info('Course Data to Insert:', $courseData);

            $course = Course::create($courseData);

            \Log::info('Course Created:', ['id' => $course->id, 'data' => $course->toArray()]);

            if (!empty($validated['CourseGoals'])) {
                $this->saveGoals($course->id, $validated['CourseGoals']);
            }

            return redirect()->route('instructor.courses.index')->with([
                'message' => 'Course Inserted Successfully',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            \Log::error('Course Creation Failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with([
                'message' => 'Failed to create course: ' . $e->getMessage(),
                'alert-type' => 'error'
            ])->withInput();
        }
    }

    public function show(Course $course)
    {
        $this->authorizeCourse($course);
        $course->load('goals');
        return view('instructor.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $this->authorizeCourse($course);
        $goals = $course->goals;
        $categories = Category::orderBy('category_name')->get();
        $subcategories = SubCategory::orderBy('subcategory_name')->get();
        return view('instructor.courses.edit', compact('course', 'categories', 'subcategories', 'goals'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        try {
            $validated = $request->validate([
                'subcategory_id' => 'required|exists:sub_categories,id',
                'course_title' => 'required|string|max:65535',
                'course_name' => 'required|string|max:65535',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'video' => 'nullable|mimes:mp4,avi,mov|max:102400',
                'description' => 'nullable|string|max:4294967295',
                'label' => 'nullable|in:Beginner,Intermediate,Advanced',
                'resources' => 'nullable|string|max:255',
                'certificate' => 'nullable|in:yes,no',
                'selling_price' => 'nullable|integer|min:0',
                'discount_price' => 'nullable|integer|min:0|lt:selling_price',
                'prerequisites' => 'nullable|string|max:65535',
                'bestseller' => 'nullable|in:0,1',
                'featured' => 'nullable|in:0,1',
                'highestrated' => 'nullable|in:0,1',
                'CourseGoals.*' => 'nullable|string|max:255',
            ]);

            if ($request->hasFile('image')) {
                $this->deleteFile($course->course_image, 'upload/course_images/thumbnail');
                $course->course_image = $this->uploadFile($request->file('image'), 'upload/course_images/thumbnail');
            }
            if ($request->hasFile('video')) {
                $this->deleteFile($course->video, 'upload/course_images/video');
                $course->video = $this->uploadFile($request->file('video'), 'upload/course_images/video');
            }

            $course->update([
                'subcategory_id' => $validated['subcategory_id'],
                'course_title' => $validated['course_title'],
                'course_name' => $validated['course_name'],
                'course_name_slug' => Str::slug($validated['course_name']),
                'course_image' => $course->course_image ?? null,
                'description' => $validated['description'] ?? null,
                'video' => $course->video ?? null,
                'label' => $validated['label'] ?? null,
                'resources' => $validated['resources'] ?? null,
                'certificate' => $validated['certificate'] ?? null,
                'selling_price' => $validated['selling_price'] ?? null,
                'discount_price' => $validated['discount_price'] ?? null,
                'prerequisites' => $validated['prerequisites'] ?? null,
            ]);

            if (!empty($validated['CourseGoals'])) {
                $this->saveGoals($course->id, $validated['CourseGoals']);
            }

            return redirect()->route('instructor.courses.index')->with([
                'message' => 'Course Updated Successfully',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            \Log::error('Course Update Failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->with([
                'message' => 'Failed to update course: ' . $e->getMessage(),
                'alert-type' => 'error'
            ])->withInput();
        }
    }

    public function getSubCategory($category_id)
    {
        $subcategories = SubCategory::where('category_id', $category_id)
            ->orderBy('subcategory_name')
            ->get(['id', 'subcategory_name']);
        \Log::info("Fetching subcategories for category_id: $category_id, Result: " . $subcategories->toJson());
        return response()->json($subcategories);
    }

    // Helper Methods
    private function authorizeCourse(Course $course)
    {
        if ($course->courseable_type !== 'App\Models\Instructor' || $course->courseable_id !== Auth::guard('instructor')->id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function uploadFile($file, $path)
    {
        if (!$file) {
            return null;
        }

        // Generate a unique filename
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Define the public path
        $publicPath = public_path($path);

        // Ensure directory exists
        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        // Save the file to public/upload
        $file->move($publicPath, $fileName);

        \Log::info('File uploaded:', ['path' => $publicPath . '/' . $fileName]);

        return $fileName; // Return only the filename for DB storage
    }

    private function deleteFile($filename, $path)
    {
        if ($filename) {
            $publicFile = public_path($path . '/' . $filename);

            if (file_exists($publicFile)) {
                unlink($publicFile);
                \Log::info('Deleted file:', ['path' => $publicFile]);
            }
        }
    }

    private function saveGoals($courseId, $goals)
    {
        CourseGoal::where('goalable_id', $courseId)
            ->where('goalable_type', Course::class)
            ->delete();

        foreach (array_filter($goals) as $goal) {
            CourseGoal::create([
                'goalable_id' => $courseId,
                'goalable_type' => Course::class,
                'goal_name' => $goal,
            ]);
        }
        \Log::info('Course goals saved:', ['course_id' => $courseId, 'goals' => $goals]);
    }
}