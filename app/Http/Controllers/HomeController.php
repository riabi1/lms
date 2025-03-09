<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
  /**
   * Display the home page.
   */
  public function index(): View
  {
    return view('frontend.index');
  }
}
