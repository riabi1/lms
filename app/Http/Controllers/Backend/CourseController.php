<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\Course_goal;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('instructor_id', Auth::guard('instructor')->id())
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
                'resources' => 'nullable|string',
                'certificate' => 'nullable|in:Yes,No',
                'selling_price' => 'nullable|numeric',
                'discount_price' => 'nullable|numeric',
                'prerequisites' => 'nullable|string',
                'bestseller' => 'nullable|boolean',
                'featured' => 'nullable|boolean',
                'highestrated' => 'nullable|boolean',
                'course_goals.*' => 'nullable|string',
            ]);
            \Log::info('Validated Data:', $validated);

            $courseImage = $this->uploadFile($request->file('image'), 'upload/course/thumbnail');
            $courseVideo = $this->uploadFile($request->file('video'), 'upload/course/video', false);

            $course = Course::create([
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'instructor_id' => Auth::guard('instructor')->id(),
                'course_title' => $request->course_title,
                'course_name' => $request->course_name,
                'course_name_slug' => strtolower(str_replace(' ', '-', $request->course_name)),
                'description' => $request->description,
                'video' => $courseVideo,
                'label' => $request->label,
                'duration' => $request->duration,
                'resources' => $request->resources,
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
            $this->saveGoals($course->id, $request->course_goals);

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
        $goals = Course_goal::where('course_id', $course->id)->get();
        $categories = Category::latest()->get();
        $subcategories = SubCategory::latest()->get();
        return view('instructor.courses.edit', compact('course', 'categories', 'subcategories', 'goals'));
    }

   public function update(Request $request, Course $course)
{
    $this->authorizeCourse($course);

    $course->update($request->except('course_goals', 'image', 'video', 'course_id') + [
        'course_name_slug' => strtolower(str_replace(' ', '-', $request->course_name)),
    ]);

    if ($request->hasFile('image')) {
        $this->deleteFile($course->course_image);
        $course->update(['course_image' => $this->uploadFile($request->file('image'), 'upload/course/thumbnail')]);
    }

    if ($request->hasFile('video')) {
        $this->deleteFile($course->video);
        $course->update(['video' => $this->uploadFile($request->file('video'), 'upload/course/video', false)]);
    }

    $this->saveGoals($course->id, $request->course_goals);

    return redirect()->route('instructor.courses.index')->with([
        'message' => 'Course Updated Successfully',
        'alert-type' => 'success'
    ]);
}

    public function destroy(Course $course)
    {
        $this->authorizeCourse($course);
        $this->deleteFile($course->course_image);
        $this->deleteFile($course->video);
        Course_goal::where('course_id', $course->id)->delete();
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
        if ($course->instructor_id !== Auth::guard('instructor')->id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function uploadFile($file, $path, $resize = true)
    {
        if (!$file) {
            return $resize ? 'upload/course/thumbnail/default.jpg' : null;
        }

        $name = ($resize ? hexdec(uniqid()) : time()) . '.' . $file->getClientOriginalExtension();
        $relativePath = "$path/$name";
        $fullPath = public_path($relativePath);

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        if ($resize) {
            $manager = new ImageManager(new Driver());
            $img = $manager->read($file)->resize(370, 246)->toJpeg(80);
            \Log::info('Uploading image to:', ['path' => $fullPath]);
            file_put_contents($fullPath, $img);
            if (!file_exists($fullPath)) {
                \Log::error('Image upload failed:', ['path' => $fullPath]);
                return 'upload/no_image.jpg';
            }
        } else {
            $file->move(public_path($path), $name);
        }

        return $relativePath;
    }

    private function deleteFile($path)
    {
        $fullPath = public_path($path);
        if ($path && file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    private function saveGoals($courseId, $goals)
    {
        if ($goals) {
            Course_goal::where('course_id', $courseId)->delete();
            foreach ($goals as $goal) {
                if ($goal) {
                    Course_goal::create(['course_id' => $courseId, 'goal_name' => $goal]);
                }
            }
        }
    }
}