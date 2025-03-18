<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Course;
use App\Models\Course_goal;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CourseController extends Controller
{
    public function AllCourse()
    {
        $courses = Course::where('instructor_id', Auth::guard('instructor')->id())
            ->with('category')
            ->latest('id')
            ->get();
        return view('instructor.courses.all_course', compact('courses'));
    }

    public function AddCourse()
    {
        $categories = Category::latest()->get();
        return view('instructor.courses.add_course', compact('categories'));
    }

    public function getSubCategory($category_id)
    {
        $subcategories = SubCategory::where('category_id', $category_id)
            ->orderBy('subcategory_name')
            ->get(['id', 'subcategory_name']);
        \Log::info("Fetching subcategories for category_id: $category_id, Result: " . $subcategories->toJson());
        return response()->json($subcategories);
    }

    public function StoreCourse(Request $request)
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Failed:', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        try {
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

            return redirect()->route('instructor.all.course')->with([
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

    public function EditCourse($id)
    {
        $course = $this->authorizeCourse($id);
        $goals = Course_goal::where('course_id', $id)->get();
        $categories = Category::latest()->get();
        $subcategories = SubCategory::latest()->get();
        return view('instructor.courses.edit_course', compact('course', 'categories', 'subcategories', 'goals'));
    }

    public function UpdateCourse(Request $request)
    {
        $course = $this->authorizeCourse($request->course_id);
        $course->update($request->except('course_goals') + [
            'course_name_slug' => strtolower(str_replace(' ', '-', $request->course_name)),
        ]);

        return redirect()->route('instructor.all.course')->with([
            'message' => 'Course Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function UpdateCourseImage(Request $request)
    {
        $course = $this->authorizeCourse($request->id);

        if ($request->hasFile('course_image')) {
            $this->deleteFile($course->course_image);
            $course->update(['course_image' => $this->uploadFile($request->file('course_image'), 'upload/course/thumbnail')]);
            $notification = ['message' => 'Course Image Updated Successfully', 'alert-type' => 'success'];
        } else {
            $notification = ['message' => 'No Image Uploaded', 'alert-type' => 'error'];
        }

        return redirect()->back()->with($notification);
    }

    public function UpdateCourseVideo(Request $request)
    {
        $course = $this->authorizeCourse($request->vid);

        if ($request->hasFile('video')) {
            $this->deleteFile($course->video);
            $course->update(['video' => $this->uploadFile($request->file('video'), 'upload/course/video', false)]);
            $notification = ['message' => 'Course Video Updated Successfully', 'alert-type' => 'success'];
        } else {
            $notification = ['message' => 'No Video Uploaded', 'alert-type' => 'error'];
        }

        return redirect()->back()->with($notification);
    }

    public function UpdateCourseGoal(Request $request)
    {
        $course = $this->authorizeCourse($request->id);
        $this->saveGoals($course->id, $request->course_goals);
        return redirect()->back()->with([
            'message' => 'Course Goals Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function DeleteCourse($id)
    {
        $course = $this->authorizeCourse($id);
        $this->deleteFile($course->course_image);
        $this->deleteFile($course->video);
        Course_goal::where('course_id', $id)->delete();
        $course->delete();

        return redirect()->back()->with([
            'message' => 'Course Deleted Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function AddCourseLecture($id)
    {
        $course = $this->authorizeCourse($id);
        $sections = CourseSection::where('course_id', $id)->latest()->get();
        return view('instructor.courses.section.add_course_lecture', compact('course', 'sections'));
    }

    public function AddCourseSection(Request $request)
    {
        $course = $this->authorizeCourse($request->id);
        CourseSection::create(['course_id' => $course->id, 'section_title' => $request->section_title]);
        return redirect()->back()->with([
            'message' => 'Course Section Added Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function SaveLecture(Request $request)
    {
        $course = $this->authorizeCourse($request->course_id);
        CourseLecture::create([
            'course_id' => $course->id,
            'section_id' => $request->section_id,
            'lecture_title' => $request->lecture_title,
            'url' => $request->lecture_url,
            'content' => $request->content,
        ]);
        return response()->json(['success' => 'Lecture Saved Successfully']);
    }

    public function EditLecture($id)
    {
        $lecture = CourseLecture::findOrFail($id);
        $this->authorizeCourse($lecture->course_id);
        return view('instructor.courses.lecture.edit_course_lecture', compact('lecture'));
    }

    public function UpdateCourseLecture(Request $request)
    {
        $lecture = CourseLecture::findOrFail($request->id);
        $this->authorizeCourse($lecture->course_id);
        $lecture->update([
            'lecture_title' => $request->lecture_title,
            'url' => $request->url,
            'content' => $request->content,
        ]);
        return redirect()->back()->with([
            'message' => 'Course Lecture Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function DeleteLecture($id)
    {
        $lecture = CourseLecture::findOrFail($id);
        $this->authorizeCourse($lecture->course_id);
        $lecture->delete();
        return redirect()->back()->with([
            'message' => 'Course Lecture Deleted Successfully',
            'alert-type' => 'success'
        ]);
    }

    public function DeleteSection($id)
    {
        $section = CourseSection::findOrFail($id);
        $this->authorizeCourse($section->course_id);
        $section->lectures()->delete();
        $section->delete();
        return redirect()->back()->with([
            'message' => 'Course Section Deleted Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Helper Methods
    private function authorizeCourse($id)
    {
        $course = Course::findOrFail($id);
        if ($course->instructor_id !== Auth::guard('instructor')->id()) {
            abort(403, 'Unauthorized action.');
        }
        return $course;
    }

    private function uploadFile($file, $path, $resize = true)
    {
        if (!$file) {
            return $resize ? 'upload/course/thumbnail/default.jpg' : null;
        }

        $name = ($resize ? hexdec(uniqid()) : time()) . '.' . $file->getClientOriginalExtension();
        $relativePath = "$path/$name";
        $fullPath = public_path($relativePath);

        // Ensure the directory exists
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