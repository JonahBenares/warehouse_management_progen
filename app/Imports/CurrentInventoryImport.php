<?php

namespace App\Imports;

use App\Models\Items;
use App\Models\Location;
use App\Models\group;
use App\Models\uom;
use App\Models\Rack;
use App\Models\Warehouse;
use App\Models\PRItems;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CurrentInventoryImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        if(count($row)!=0){
            $item_id=$row['item_id'] ?? '0';
            $item_description=$row['item_description'] ?? '';
            $cat_id=$row['cat_id'] ?? '0';
            $subcat_id=$row['subcat_id'] ?? '0';
            $subcat_prefix=$row['subcat_prefix'] ?? '';
            //$unit=$row['uom_id'] ?? '';
            $input_pn=$row['pn_no'] ?? '';
            $rack_id=$row['rack_id'] ?? '0';
            $group_id=$row['group_id'] ?? '0';
            $warehouse_id=$row['wh_id'] ?? '0';
            $location_id=$row['location_id'] ?? '0';
            $counter=Items::where('pn_no','LIKE',"%$subcat_prefix%")->get();
            $count=$counter->count();
            if($count==0){
                $pn_no= $subcat_prefix."_1001";
            }else{
                $maxsubcat=Items::where('item_sub_category_id',$subcat_id)->max('pn_no');
                $pn_noexp=explode('_',$maxsubcat);
                $series = $pn_noexp[1] ?? '';
                $add=1;
                $next=(int) $series+ (int) $add;
                $pn_no = $subcat_prefix."_".$next;
            }
            if(!empty($item_description)){
                $upload = Items::find($item_id);
                $items['item_description']=$item_description;
                $items['item_category_id']=(int) $cat_id;
                $items['item_sub_category_id']=(int) $subcat_id;
                //$items['uom']=uom::where('id',$unit)->value('unit_name');
                $items['pn_no']= (empty($input_pn)) ? $pn_no : $input_pn;
                $items['rack_id']=(int) $rack_id;
                $items['rack_description']=Rack::where('id',$rack_id)->value('rack_name');
                $items['group_id']=(int) $group_id;
                $items['group_description']=group::where('id',$group_id)->value('group_name');
                $items['warehouse_id']=(int) $warehouse_id;
                $items['warehouse_description']=Warehouse::where('id',$warehouse_id)->value('warehouse_name');
                $items['location_id']=(int) $location_id;
                $items['location_description']=Location::where('id',$location_id)->value('location_name');
                $upload->update($items);
            }
        }
    }
}
