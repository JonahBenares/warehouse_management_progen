<?php

namespace App\Http\Controllers;
use App\Models\ItemCategory;
use App\Models\ItemSubCategory;
use Illuminate\Http\Request;
use App\Http\Requests\ItemCategoryRequest;


class ItemCategoryController extends Controller
{
    public function get_all_category(Request $request){
        // $categories = ItemCategory::with(['sub_categories'])->paginate(10);
        // return response()->json($categories);
        $filter=$request->get('filter');
        if($filter!=null){
            $categories = ItemCategory::with(['sub_categories'])
                ->whereHas('sub_categories',function($query) use ($filter){
                    return $query->where('subcat_name', 'LIKE','%'.$filter.'%')
                    ->orWhere('subcat_code', 'LIKE','%'.$filter.'%')
                    ->orWhere('subcat_prefix', 'LIKE','%'.$filter.'%') ;
                })
                    ->orWhere('category_name','LIKE','%'.$filter.'%')
                    ->orWhere('category_code','LIKE','%'.$filter.'%')
                    ->orWhere('prefix','LIKE','%'.$filter.'%')
                    ->paginate(3);

                    
            return response()->json($categories);
        }else{
            $categories = ItemCategory::with(['sub_categories'])->paginate(3);
            return response()->json($categories);
        }
    } 

    public function search_category(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){

            $categories = ItemCategory::with(['sub_categories'])
                ->whereHas('sub_categories',function($query) use ($filter){
                    return $query->where('subcat_name', 'LIKE','%'.$filter.'%')
                    ->orWhere('subcat_code', 'LIKE','%'.$filter.'%')
                    ->orWhere('subcat_prefix', 'LIKE','%'.$filter.'%') ;
                })
                    ->orWhere('category_name','LIKE','%'.$filter.'%')
                    ->orWhere('category_code','LIKE','%'.$filter.'%')
                    ->orWhere('prefix','LIKE','%'.$filter.'%')
                    ->paginate(10);

                    
            return response()->json($categories);
        }else{
            $categories = ItemCategory::with(['sub_categories'])->paginate(10);
            return response()->json($categories);
        }
    }

    public function create_category(Request $request){
     

        $formData = [
            'category_code'=>null,
            'category_name'=> null,
            'prefix'=>null,
            'subcategories'=> [
                [
                    'subcat_code' => null,
                    'subcat_prefix' => null,
                    'subcat_name' => null,
                ]
            ]
        ];

        return response()->json($formData);
    }


    public function add_category(ItemCategoryRequest $request){

        $sub_categories = $request->input("subcategories");
     
        // $validated=$this->validate($request,[
        //     'category_code'=>['unique:item_categories','required','string'],
        //     'prefix'=>['unique:item_categories','string'],
        //     'category_name'=>['unique:item_categories','required','string']

        // ]);

        $validated = $request->validated();
        

        $category=ItemCategory::create($validated);
        
       
         foreach(json_decode($sub_categories) as $sc){
             $subcat['item_category_id'] = $category->id;
             $subcat['subcat_code'] = $sc->subcat_code;
             $subcat['subcat_prefix'] = $sc->subcat_prefix;
             $subcat['subcat_name'] = $sc->subcat_name;

             $sub = ItemSubCategory::create($subcat);
            
         }
    }

    public function edit_category($id){

        $categories = ItemCategory::with(['sub_categories'])->find($id);
        return response()->json([
            'categories'=>$categories
        ],200);

    }

    public function update_category(ItemCategoryRequest $request, $id){

      
        $categories=ItemCategory::where('id',$id)->first();

        $validated = $request->validated();
        
        $categories->update($validated);

       $sub_categories = $request->input("subcategories");
       
         foreach(json_decode($sub_categories) as $sc){
             $subcat['item_category_id'] = $id;
             $subcat['subcat_code'] = $sc->subcat_code;
             $subcat['subcat_prefix'] = $sc->subcat_prefix;
             $subcat['subcat_name'] = $sc->subcat_name;

             $sub = ItemSubCategory::create($subcat);
            
         }
    }
}
