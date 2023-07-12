<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index(){
        $users=User::all();
        $categories=Category::all();
        $products=Product::all();
        return view('dashboard.welcome',compact('users','categories','products'));
    }
}
