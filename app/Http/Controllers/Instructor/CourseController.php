<?php
namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\CourseGoal;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CourseController extends Controller
{
    public function index()
    {
        $instructorId = Auth::guard('instructor')->id();
        $courses = Course::where('courseable_type', 'App\Models\Instructor')
            ->where('courseable_id', $instructorId)
            ->with('category')
            ->latest('id')
            ->get();
        return view('instructor.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::latest()->get();
        return view('instructor.courses.create', compact('categories'));
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
                'category_id' => 'required|exists:categories,id',
                'subcategory_id' => 'required|exists:sub_categories,id',
                'course_title' => 'required|string|max:255',
                'course_name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'video' => 'nullable|mimes:mp4,avi,mov|max:102400',
                'description' => 'nullable|string',
                'label' => 'nullable|string|in:Beginner,Intermediate,Advanced',
                'duration' => 'nullable|string',
                'certificate' => 'nullable|in:Yes,No',
                'selling_price' => 'nullable|numeric',
                'discount_price' => 'nullable|numeric',
                'prerequisites' => 'nullable|string',
                'bestseller' => 'nullable|boolean',
                'featured' => 'nullable|boolean',
                'highestrated' => 'nullable|boolean',
                'CourseGoals.*' => 'nullable|string',
            ]);
            \Log::info('Validated Data:', $validated);

            $courseImage = $this->uploadFile($request->file('image'), 'upload/course_images/thumbnail');
            $courseVideo = $this->uploadFile($request->file('video'), 'upload/course_images/video');

            $course = Course::create([
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'courseable_type' => 'App\Models\Instructor',
                'courseable_id' => Auth::guard('instructor')->id(),
                'course_title' => $request->course_title,
                'course_name' => $request->course_name,
                'course_name_slug' => strtolower(str_replace(' ', '-', $request->course_name)),
                'description' => $request->description,
                'video' => $courseVideo,
                'label' => $request->label,
                'duration' => $request->duration,
                'certificate' => $request->certificate,
                'selling_price' => $request->selling_price,
                'discount_price' => $request->discount_price,
                'prerequisites' => $request->prerequisites,
                'bestseller' => $request->bestseller ?? 0,
                'featured' => $request->featured ?? 0,
                'highestrated' => $request->highestrated ?? 0,
                'status' => 1,
                'course_image' => $courseImage,
                'created_at' => Carbon::now(),
            ]);

            \Log::info('Course Created:', ['id' => $course->id, 'data' => $course->toArray()]);
            $this->saveGoals($course->id, $request->CourseGoals);

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
    $goals = $course->goals; // Utilise la relation polymorphique
    $categories = Category::latest()->get();
    $subcategories = SubCategory::latest()->get();
    return view('instructor.courses.edit', compact('course', 'categories', 'subcategories', 'goals'));
}

    public function update(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $course->update($request->except('CourseGoals', 'image', 'video', 'course_id') + [
            'course_name_slug' => strtolower(str_replace(' ', '-', $request->course_name)),
        ]);

        if ($request->hasFile('image')) {
            $this->deleteFile($course->course_image, 'upload/course_images/thumbnail');
            $course->update(['course_image' => $this->uploadFile($request->file('image'), 'upload/course_images/thumbnail')]);
        }

        if ($request->hasFile('video')) {
            $this->deleteFile($course->video, 'upload/course_images/video');
            $course->update(['video' => $this->uploadFile($request->file('video'), 'upload/course_images/video')]);
        }

        $this->saveGoals($course->id, $request->CourseGoals);

        return redirect()->route('instructor.courses.index')->with([
            'message' => 'Course Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function destroy(Course $course)
    {
        $this->authorizeCourse($course);
        $this->deleteFile($course->course_image, 'upload/course_images/thumbnail');
        $this->deleteFile($course->video, 'upload/course_images/video');
        CourseGoal::where('course_id', $course->id)->delete();
        $course->delete();

        return redirect()->route('instructor.courses.index')->with([
            'message' => 'Course Deleted Successfully',
            'alert-type' => 'success'
        ]);
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

        // Générer un nom unique 
        $filename = date('YmdHi') . '_' . $file->getClientOriginalName();

        // Stocker dans storage/app/public/<path>/
        if (str_contains($path, 'thumbnail')) {
            // Traitement des images avec Intervention Image
            $manager = new ImageManager(new Driver());
            $img = $manager->read($file)->resize(370, 246)->toJpeg(80);
            Storage::disk('public')->put("$path/$filename", (string) $img);
        } else {
            $file->storeAs($path, $filename, 'public');
        }

        if (!Storage::disk('public')->exists("$path/$filename")) {
            \Log::error('File upload failed:', ['path' => "$path/$filename"]);
            return null;
        }

        \Log::info('File uploaded successfully:', ['path' => "$path/$filename"]);

        return $filename;
    }

    private function deleteFile($filename, $path)
    {
        if ($filename && Storage::disk('public')->exists("$path/$filename")) {
            Storage::disk('public')->delete("$path/$filename");
            \Log::info('Deleted file:', ['path' => "$path/$filename"]);
        }
    }

private function saveGoals($courseId, $goals)
{
    if ($goals) {
        // Supprime les anciens objectifs pour ce cours
        CourseGoal::where('goalable_id', $courseId)
                  ->where('goalable_type', Course::class)
                  ->delete();

        // Ajoute les nouveaux objectifs
        foreach ($goals as $goal) {
            if ($goal) {
                CourseGoal::create([
                    'goalable_id' => $courseId,
                    'goalable_type' => Course::class,
                    'goal_name' => $goal
                ]);
            }
        }
    }
}
}