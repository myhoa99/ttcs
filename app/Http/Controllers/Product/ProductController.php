<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\Deploy\ProductRequest;
use App\Http\Controllers\Controller;
use App\Deploy\Product;
use App\Category\ProductCategory;
use App\Staff\Staff;
use App\Plan\Topic;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $product = new Product;

        $search_data = array(
            'topic' => $request->topic,
            'leader' => $request->leader,
            'publication' => $request->publication,
            'public_year' => $request->public_year,
            'product' => $request->product,
        );

        $data = array(
            'paginatorContent' => $product->getProduct($search_data),
            'topic_list' => Topic::whereIn('pk_imatt', range(2, 6))
                                 ->select('pk_imadt', 'stendt')
                                 ->get()
                                 ->toArray(),

            'staff_list' => Staff::select('pk_smacanbo', 'shodem')
                                 ->get()
                                 ->toArray(),
            'search_data' => $search_data,
            'page' => $request->page
        );

        return view('deploy.product.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = array(
            'topic_list' => Topic::whereIn('pk_imatt', range(2, 6))
                                 ->select('pk_imadt', 'stendt')
                                 ->get()
                                 ->toArray(),
            'product_category' => ProductCategory::all()
        );
        pr($data['topic_list']);
        return view('deploy.product.create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $data = $request->all();

        Product::create($data);
        return redirect()->route('admin.product.index')
                        ->with([
            'flash_level' => 'success',
            'flash_message' => 'C???p nh???t s???n ph???m th??nh c??ng'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $data = array(
            'topic_list' => Topic::whereIn('pk_imatt', range(2, 6))
                                 ->select('pk_imadt', 'stendt')
                                 ->get()
                                 ->toArray(),
            'product_category' => ProductCategory::all()
        );
        return view('deploy.product.update', compact('product', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        Product::where('pk_imasp', '=', $id)->update($data);
        return redirect()->route('admin.product.index')
                        ->with([
            'flash_level' => 'success',
            'flash_message' => 'C???p nh???t s???n ph???m th??nh c??ng'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete($id);
         return redirect()->route('admin.product.index')
                        ->with([
            'flash_level' => 'success',
            'flash_message' => 'X??a s???n ph???m th??nh c??ng'
        ]);
    }
}
