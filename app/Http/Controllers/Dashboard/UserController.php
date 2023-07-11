<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\Concerns\Has;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    use UploadImageTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['permission:users_read'])->only('index');
        $this->middleware(['permission:users_create'])->only('create');
        $this->middleware(['permission:users_update'])->only('update');
        $this->middleware(['permission:users_delete'])->only('delete');
    }


    public function index(Request $request)
    {
        //
        $users=User::whereRoleIs('admin')->where(function ($query) use($request){
            return $query->when($request->search,function ($query) use($request){
                return $query->where('first_name','like','%'.$request->search.'%')
                    ->orWhere('last_name','like','%'.$request->search.'%');
            });
        })->latest()->paginate();
        return view('dashboard.users.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('dashboard.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        //
        $request_data=$request->except('password','password_confirmation','permissions','image');
        $request_data['password']=Hash::make($request->password);
        if($request->hasFile('image'))
        $request_data['image']=$this->uploadImage($request,'uploads/users/');
        $user=User::create($request_data);
        $user->attachRole('admin');
        $user->syncPermissions($request->permissions);
        return redirect()->route('dashboard.users.index')->with('success',__('site.added_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
        $user=User::findOrFail($user->id);
        return view('dashboard.users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
        return view('dashboard.users.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        //
        $old_image=$user->image;
        $request_data=$request->except('permissions','image');

       if($request->hasFile('image') ){
           $request_data['image']=$this->uploadImage($request,'uploads/users/');

       }
        $user->update(
            $request_data
        );

       if($old_image && isset($request_data['image'])){
           Storage::disk('public_uploads')->delete('/users/'.$old_image);
       }
        $user->syncPermissions($request->permissions);
        return redirect()->route('dashboard.users.index')->with('success',__('site.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //

        $user->delete();
        if($user->image != 'default.jpg'){
            Storage::disk('public_uploads')->delete('/users/'.$user->image);
        }
        return redirect()->route('dashboard.users.index')->with('success',__('site.deleted_successfully'));

    }
}
