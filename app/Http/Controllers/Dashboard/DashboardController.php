<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function index(){
        $users=User::whereRoleIs('admin')->count();
        $categories=Category::count();
        $products=Product::count();
        $clients=Client::count();
        $sales_data=DB::table('orders')->select(
            DB::raw('year(created_at) as year'),
            DB::raw('month(created_at) as month'),
            DB::raw('sum(total_price) as sum'),
        )->groupBy('created_at')->get();
        return view('dashboard.welcome',compact('users','categories','products','clients','sales_data'));
    }
}
