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
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Carbon\Carbon;

class CourseController extends Controller
{
    public function AllCourse()
    {
        $id = Auth::guard('instructor')->user()->id; // Use 'instructor' guard
        $courses = Course::where('instructor_id', $id)->orderBy('id', 'desc')->get();
        return view('instructor.courses.all_course', compact('courses'));
    }

    public function AddCourse()
    {
        $categories = Category::latest()->get();
        return view('instructor.courses.add_course', compact('categories'));
    }

  public function getSubCategory($category_id)
    {
        $subcat = SubCategory::where('category_id', $category_id)->orderBy('subcategory_name', 'ASC')->get(['id', 'subcategory_name']);
        \Log::info("Fetching subcategories for category_id: $category_id, Result: " . $subcat->toJson());
        return response()->json($subcat);
    }

    public function StoreCourse(Request $request)
    {
        if ($request->file('image')) {
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $request->file('image')->getClientOriginalExtension();
            $img = $manager->read($request->file('image'));
            $img = $img->resize(370, 246);
            $img->toJpeg(80)->save(base_path('public/upload/course/thambnail/' . $name_gen));
            $save_url = 'upload/course/thambnail/' . $name_gen;
        } else {
            $save_url = "upload/category/1824328589204901.jpg"; // Default image
        }

        if ($request->file('video')) {
            $video = $request->file('video');
            $videoName = time() . '.' . $video->getClientOriginalExtension();
            $video->move(base_path('public/upload/course/video/'), $videoName);
            $save_video = 'upload/course/video/' . $videoName;
        } else {
            $save_video = null; // Default or null
        }

        $course_id = Course::insertGetId([
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'instructor_id' => Auth::guard('instructor')->user()->id, // Use 'instructor' guard
            'course_title' => $request->course_title,
            'course_name' => $request->course_name,
            'course_name_slug' => strtolower(str_replace(' ', '-', $request->course_name)),
            'description' => $request->description,
            'video' => $save_video,
            'label' => $request->label,
            'duration' => $request->duration,
            'resources' => $request->resources,
            'certificate' => $request->certificate,
            'selling_price' => $request->selling_price,
            'discount_price' => $request->discount_price,
            'prerequisites' => $request->prerequisites,
            'bestseller' => $request->request->bestseller ?? 0,
            'featured' => $request->featured ?? 0,
            'highestrated' => $request->highestrated ?? 0,
            'status' => 1,
            'course_image' => $save_url,
            'created_at' => Carbon::now(),
        ]);

        // Course Goals
        $goals = $request->course_goals;
        if (!empty($goals)) {
            foreach ($goals as $goal) {
                Course_goal::create([
                    'course_id' => $course_id,
                    'goal_name' => $goal,
                ]);
            }
        }

        $notification = [
            'message' => 'Course Inserted Successfully',
            'alert-type' => 'success'
        ];
        return redirect()->route('instructor.all.course')->with($notification);
    }

    public function EditCourse($id)
    {
        $course = Course::findOrFail($id);
        // Ensure the course belongs to the authenticated instructor
        if ($course->instructor_id !== Auth::guard('instructor')->user()->id) {
            abort(403, 'Unauthorized action.');
        }
        $goals = Course_goal::where('course_id', $id)->get();
        $categories = Category::latest()->get();
        $subcategories = SubCategory::latest()->get();
        return view('instructor.courses.edit_course', compact('course', 'categories', 'subcategories', 'goals'));
    }

    public function UpdateCourse(Request $request)
    {
        $cid = $request->course_id;
        $course = Course::findOrFail($cid);
        // Ensure the course belongs to the authenticated instructor
        if ($course->instructor_id !== Auth::guard('instructor')->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $course->update([
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'instructor_id' => Auth::guard('instructor')->user()->id, // Use 'instructor' guard
            'course_title' => $request->course_title,
            'course_name' => $request->course_name,
            'course_name_slug' => strtolower(str_replace(' ', '-', $request->course_name)),
            'description' => $request->description,
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
        ]);

        $notification = [
            'message' => 'Course Updated Successfully',
            'alert-type' => 'success'
        ];
        return redirect()->route('instructor.all.course')->with($notification);
    }

    public function UpdateCourseImage(Request $request)
    {
        $course_id = $request->id;
        $course = Course::findOrFail($course_id);
        if ($course->instructor_id !== Auth::guard('instructor')->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $oldImage = $request->old_img;
        if ($request->file('course_image')) {
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $request->file('course_image')->getClientOriginalExtension();
            $img = $manager->read($request->file('course_image'));
            $img = $img->resize(370, 246);
            $img->toJpeg(80)->save(base_path('public/upload/course/thambnail/' . $name_gen));
            $save_url = 'upload/course/thambnail/' . $name_gen;

            if (file_exists(base_path('public/' . $oldImage))) {
                unlink(base_path('public/' . $oldImage));
            }

            $course->update([
                'course_image' => $save_url,
                'updated_at' => Carbon::now(),
            ]);

            $notification = [
                'message' => 'Course Image Updated Successfully',
                'alert-type' => 'success'
            ];
        } else {
            $notification = [
                'message' => 'No Image Uploaded',
                'alert-type' => 'error'
            ];
        }

        return redirect()->back()->with($notification);
    }

    public function UpdateCourseVideo(Request $request)
    {
        $course_id = $request->vid;
        $course = Course::findOrFail($course_id);
        if ($course->instructor_id !== Auth::guard('instructor')->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $oldVideo = $request->old_vid;
        if ($request->file('video')) {
            $video = $request->file('video');
            $videoName = time() . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('upload/course/video/'), $videoName);
            $save_video = 'upload/course/video/' . $videoName;

            if (file_exists($oldVideo)) {
                unlink($oldVideo);
            }

            $course->update([
                'video' => $save_video,
                'updated_at' => Carbon::now(),
            ]);

            $notification = [
                'message' => 'Course Video Updated Successfully',
                'alert-type' => 'success'
            ];
        } else {
            $notification = [
                'message' => 'No Video Uploaded',
                'alert-type' => 'error'
            ];
        }

        return redirect()->back()->with($notification);
    }

    public function UpdateCourseGoal(Request $request)
    {
        $cid = $request->id;
        $course = Course::findOrFail($cid);
        if ($course->instructor_id !== Auth::guard('instructor')->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        if (empty($request->course_goals)) {
            return redirect()->back();
        }

        Course_goal::where('course_id', $cid)->delete();
        foreach ($request->course_goals as $goal) {
            Course_goal::create([
                'course_id' => $cid,
                'goal_name' => $goal,
            ]);
        }

        $notification = [
            'message' => 'Course Goals Updated Successfully',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }

    public function DeleteCourse($id)
    {
        $course = Course::findOrFail($id);
        if ($course->instructor_id !== Auth::guard('instructor')->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        if (file_exists(base_path('public/' . $course->course_image))) {
            unlink(base_path('public/' . $course->course_image));
        }
        if (file_exists(base_path('public/' . $course->video))) {
            unlink(base_path('public/' . $course->video));
        }

        Course_goal::where('course_id', $id)->delete();
        $course->delete();

        $notification = [
            'message' => 'Course Deleted Successfully',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }

    public function AddCourseLecture($id)
    {
        $course = Course::findOrFail($id);
        if ($course->instructor_id !== Auth::guard('instructor')->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $section = CourseSection::where('course_id', $id)->latest()->get();
        return view('instructor.courses.section.add_course_lecture', compact('course', 'section'));
    }

    public function AddCourseSection(Request $request)
    {
        $cid = $request->id;
        $course = Course::findOrFail($cid);
        if ($course->instructor_id !== Auth::guard('instructor')->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        CourseSection::create([
            'course_id' => $cid,
            'section_title' => $request->section_title,
        ]);

        $notification = [
            'message' => 'Course Section Added Successfully',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }

   public function SaveLecture(Request $request)
{
    $course = Course::findOrFail($request->course_id);
    if ($course->instructor_id !== Auth::guard('instructor')->user()->id) {
        abort(403, 'Unauthorized action.');
    }

    $lecture = CourseLecture::create([
        'course_id' => $request->course_id,
        'section_id' => $request->section_id,
        'lecture_title' => $request->lecture_title,
        'url' => $request->lecture_url,
        'content' => $request->content,
    ]);

    return response()->json(['success' => 'Lecture Saved Successfully']);
}

    public function EditLecture($id)
    {
        $clecture = CourseLecture::findOrFail($id);
        $course = Course::findOrFail($clecture->course_id);
        if ($course->instructor_id !== Auth::guard('instructor')->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('instructor.courses.lecture.edit_course_lecture', compact('clecture'));
    }

    public function UpdateCourseLecture(Request $request)
    {
        $lid = $request->id;
        $lecture = CourseLecture::findOrFail($lid);
        $course = Course::findOrFail($lecture->course_id);
        if ($course->instructor_id !== Auth::guard('instructor')->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $lecture->update([
            'lecture_title' => $request->lecture_title,
            'url' => $request->url,
            'content' => $request->content,
        ]);

        $notification = [
            'message' => 'Course Lecture Updated Successfully',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }

    public function DeleteLecture($id)
    {
        $lecture = CourseLecture::findOrFail($id);
        $course = Course::findOrFail($lecture->course_id);
        if ($course->instructor_id !== Auth::guard('instructor')->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $lecture->delete();

        $notification = [
            'message' => 'Course Lecture Deleted Successfully',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }

    public function DeleteSection($id)
    {
        $section = CourseSection::findOrFail($id);
        $course = Course::findOrFail($section->course_id);
        if ($course->instructor_id !== Auth::guard('instructor')->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $section->lectures()->delete();
        $section->delete();

        $notification = [
            'message' => 'Course Section Deleted Successfully',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }
}