<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Models\ProductionProduct;
use App\Http\Requests\StoreProductionProductRequest;
use App\Http\Requests\UpdateProductionProductRequest;

class ProductionProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Manage Production Product';
        $productionProducts = ProductionProduct::paginate(10);
        return view('master.production-product.index', compact('productionProducts', 'pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pageTitle = 'Create Production Product';
        return view('master.production-product.create', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductionProductRequest $request)
    {
        ProductionProduct::create($request->validated());
        return redirect()->route('master.production-product.index')->with('success', 'Production product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductionProduct $productionProduct)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductionProduct $productionProduct)
    {
        $pageTitle = 'Edit Production Product';
        return view('master.production-product.edit', compact('productionProduct', 'pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductionProductRequest $request, ProductionProduct $productionProduct)
    {
        $productionProduct->update($request->validated());
        return redirect()->route('master.production-product.index')->with('success', 'Production product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductionProduct $productionProduct)
    {
        $productionProduct->delete();
        return redirect()->route('master.production-product.index')->with('success', 'Production product deleted successfully.');
    }
}
