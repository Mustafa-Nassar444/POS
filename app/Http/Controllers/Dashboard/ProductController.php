<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use UploadImageTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $categories=Category::all();

        $products=Product::when($request->search,function ($query) use($request){
            return $query->whereTranslationLike('name','%'.$request->search.'%');
        })->when($request->category_id,function ($query) use($request){
            return $query->where('category_id',$request->category_id);
        })->latest()->paginate();

        return view('dashboard.products.index',compact('products','categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $categories=Category::all();
        return view('dashboard.products.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        //
        $request_data=$request->except('image');
        if($request->hasFile('image')) {
            $request_data['image'] = $this->uploadImage($request, 'uploads/products/');
        }
        $product=Product::create($request_data);
        return redirect()->route('dashboard.products.index')->with(__('site.added_successfully'));

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
        $categories=Category::all();
        return view('dashboard.products.edit',compact('product','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        //
        $old_image=$product->image;
        $request_data=$request->except('image');
        if($request->hasFile('image'))
            $request_data['image']=$this->uploadImage($request,'uploads/products/');
        $product->update($request_data);
        if($old_image && isset($request_data['image']))
            Storage::disk('public_uploads')->delete('/products/'.$old_image);

        return redirect()->route('dashboard.products.index')->with(__('site.added_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
        $product->delete();
        $product->deleteTranslations();
        if($product->image != 'default.jpg'){
            Storage::disk('public_uploads')->delete('/users/'.$product->image);
        }
        return redirect()->route('dashboard.products.index')->with('success',__('site.deleted_successfully'));
    }
}
