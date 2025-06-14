<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Items;
use App\Exports\BeginningBalanceExport;
use App\Exports\InventoryExport;
use App\Exports\CurrentInventoryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BegbalImport;
use App\Imports\InventoryImport;
use App\Imports\CurrentInventoryImport;
class ImportItemsController extends Controller
{
    public function exportBegbal(){
        //Excel::store(new BeginningBalanceExport, 'begbal.xlsx');
        return Excel::download(new BeginningBalanceExport, 'begbal_format.xlsx');
    }

    public function begbal_format(){
        $items=Items::where('item_description','!=','')->orderBy('id','ASC')->get();
        return response()->json([
            'items'=>$items
        ],200);
    }

    public function add_begbal(Request $request){
        if($request->file('upload_begbal')){
            $begbalname=$request->file('upload_begbal')->getClientOriginalName();
            $request->file('upload_begbal')->storeAs('imports',$begbalname);
            Excel::import(new BegbalImport, request()->file('upload_begbal'));
        }
    }

    public function add_inventory(Request $request){
        if($request->file('upload_inventory')){
            $inventoryname=$request->file('upload_inventory')->getClientOriginalName();
            $request->file('upload_inventory')->storeAs('imports',$inventoryname);
            Excel::import(new InventoryImport, request()->file('upload_inventory'));
        }
    }

    public function exportInventory(){
        //Excel::store(new BeginningBalanceExport, 'begbal.xlsx');
        return Excel::download(new InventoryExport, 'inventory_format.xlsx');
    }

    public function inventory_format(){
        $items=Items::where('item_description','!=','')->orderBy('id','ASC')->get();
        return response()->json([
            'items'=>$items
        ],200);
    }

    public function exportCurrentInventory(){
        //Excel::store(new BeginningBalanceExport, 'begbal.xlsx');
        return Excel::download(new CurrentInventoryExport, 'inventory_current_format.xlsx');
    }

    public function add_currentinventory(Request $request){
        if($request->file('upload_currentinventory')){
            $currentinventoryname=$request->file('upload_currentinventory')->getClientOriginalName();
            $request->file('upload_currentinventory')->storeAs('imports',$currentinventoryname);
            Excel::import(new CurrentInventoryImport, request()->file('upload_currentinventory'));
        }
    }
}
