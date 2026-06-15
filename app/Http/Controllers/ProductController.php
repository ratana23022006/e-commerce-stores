<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function addProduct(Request $req){
        $product=$req->validate([
            'pro_name'=>'required',
            'description'=>'nullable',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);
       $product['user_id'] = $req->user_id;
        if($req->hasFile('image')){
            $file=$req->file('image');
            $filename=time().'-'.$file->getClientOriginalName();
            $file->move('image/',$filename);
            $product['image']=url('image/'.$filename);
        }
        Product::create($product);
        return apiResponse($product,201,'Add product successfully...');
    }
}
