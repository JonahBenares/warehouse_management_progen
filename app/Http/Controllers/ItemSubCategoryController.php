<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemSubCategory;

class ItemSubCategoryController extends Controller
{
    public function edit_subcat($id){
        $subcategories = ItemSubCategory::find($id);
        return response()->json([
            'subcategories'=>$subcategories
        ],200);
    }

    public function update_subcat(Request $request, $id){
        $subcategories=ItemSubCategory::where('id',$id)->first();
        $validated=$this->validate($request,[
            'subcat_code'=>['required','string'],
            'subcat_prefix'=>['string'],
            'subcat_name'=>['required','string']
        ]);

        $subcategories->update($validated);
    }

}
