<?php

namespace App\Imports;

use App\Models\Items;
use App\Models\PRItems;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BegbalImport implements ToModel, WithHeadingRow
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
        $begbal=Items::where('id',$row['item_id'])->value('begbal');
        if($begbal==0 && count($row)!=0){
            if($row['quantity']!=0 || $row['quantity']!=''){
                $upload = Items::find($row['item_id']);
                $upload->update([
                    'begbal' => ($row['quantity']!=0) ? $row['quantity'] : 0,
                    'running_balance'=> ($row['quantity']!=0) ? $row['quantity'] : 0
                ]);
                if (!PRItems::where('item_id', $row['item_id'])->exists()) {
                    $pritemsdata['begbal']=($row['quantity']) ? $row['quantity'] : 0;
                    $pritemsdata['balance']=($row['quantity']) ? $row['quantity'] : 0;
                    $pritemsdata['item_id']=$row['item_id'];
                    $pritemsdata['pr_no']="WH STOCKS";
                    PRItems::create($pritemsdata);
                }
            }
            // if (PRItems::where('item_id', $row['item_id'])->exists()) {
            //     $updatec=PRItems::where('item_id',$row['item_id'])->first();
            //     $begbal=PRItems::where('item_id',$row['item_id'])->value('begbal');
            //     $pritemsdata['begbal']=($row['quantity']!='') ? $row['quantity'] + $begbal : 0;
            //     $updatec->update($pritemsdata);
            // }else{
            //     $pritemsdata['begbal']=($row['quantity']!='') ? $row['quantity'] : 0;
            //     $pritemsdata['item_id']=$row['item_id'];
            //     PRItems::create($pritemsdata);
            // }
        }
    }
}
