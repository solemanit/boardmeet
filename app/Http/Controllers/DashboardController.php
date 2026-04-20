<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
  /**
   * Display the dashboard index page.
   *
   * @return View
   */
  public function index(): View
  {
    return view('dashboard.index');
  }
}
