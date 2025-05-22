<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\UsersExport;
use App\Exports\InstructorsExport;
use App\Exports\OrdersExport;
use App\Exports\CoursesExport;
use App\Exports\EnrollmentsExport;
use App\Exports\PaymentsExport;
use App\Exports\AllDataExport;
use App\Exports\BlogPostsExport;
use App\Exports\BlogCategoriesExport;
use App\Exports\CouponsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ExcelReportController extends Controller
{
    public function index()
    {
        return view('admin.excel.index');
    }

    public function exportEnrollments()
    {
        return Excel::download(new EnrollmentsExport, 'enrollments_report.xlsx');
    }

    public function exportPayments()
    {
        return Excel::download(new PaymentsExport, 'payments_report.xlsx');
    }

    public function exportUsers()
    {
        return Excel::download(new UsersExport, 'users_report.xlsx');
    }

    public function exportInstructors()
    {
        return Excel::download(new InstructorsExport, 'instructors_report.xlsx');
    }

    public function exportOrders()
    {
        return Excel::download(new OrdersExport, 'orders_report.xlsx');
    }

    public function exportCourses()
    {
        return Excel::download(new CoursesExport, 'courses_report.xlsx');
    }

    public function exportBlogPosts()
    {
        return Excel::download(new BlogPostsExport, 'blog_posts_report.xlsx');
    }

    public function exportBlogCategories()
    {
        return Excel::download(new BlogCategoriesExport, 'blog_categories_report.xlsx');
    }

    public function exportCoupons()
    {
        return Excel::download(new CouponsExport, 'coupons_report.xlsx');
    }

    public function exportAll()
    {
        return Excel::download(new AllDataExport, 'all_data_report.xlsx');
    }
}