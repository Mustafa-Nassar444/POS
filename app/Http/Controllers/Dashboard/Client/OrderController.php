<?php

namespace App\Http\Controllers\Dashboard\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Client $client)
    {
        //

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Client $client)
    {
        //
        $categories=Category::with('products')->get();
        $orders=$client->orders()->paginate();
        return view('dashboard.clients.orders.create',compact('client','categories','orders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Client $client)
    {

        $this->attach_order($request,$client);
        return redirect()->route('dashboard.orders.index')->with('success',__('site.added_successfully'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client,Order $order)
    {
        //
        $categories=Category::with('products')->get();

        $orders=$client->orders()->paginate(5);
        return view('dashboard.clients.orders.edit',compact('client','categories','order','orders'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client, Order $order)
    {
        //
        $this->detach_order($order);

        $this->attach_order($request,$client);

        return redirect()->route('dashboard.orders.index')->with('success',__('site.updated_successfully'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order, Client $client)
    {
        //
    }

    private function attach_order($request, $client){
        $request->validate([
            'products'=>'required|array'
        ]);

        $order=$client->orders()->create();

        $order->products()->attach($request->products);

        $total_price=0;

        foreach ($request->products as $id=>$quantity) {

            $product=Product::findOrFail($id);

            $total_price+=$product->sale_price*$quantity['quantity'];

            $product->update([
                'stock'=>$product->stock-$quantity['quantity']
            ]);
        }
        //
        $order->update([
            'total_price'=>$total_price,
        ]);
    }

    private function detach_order($order){
        foreach ($order->products as $product) {

            $product->update([
                'stock'=>$product->stock+$product->pivot->quantity,
            ]);

        }
        $order->products()->detach();
        $order->delete();
    }
}
