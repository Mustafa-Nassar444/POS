<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

trait UploadImageTrait
{

    public function uploadImage(Request $request,$folderName){

        $img=$request->file('image')->getClientOriginalName();
        //$path=$request->file('image')->storeAs('Category',$img,$folderName);
         Image::make($request->image)->resize(300,null,function ($constraint){
            $constraint->aspectRatio();
        })->save(public_path($folderName).$img);
         return $img;
    }
}
