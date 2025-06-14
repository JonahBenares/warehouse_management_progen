<?php

namespace App\Exports;

use App\Models\Items;
use App\Models\uom;
use App\Models\ItemSubCategory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class CurrentInventoryExport implements FromQuery, WithMapping, ShouldAutoSize, WithHeadings, WithEvents, WithMultipleSheets, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function query()
    {
        return Items::query()->orderBy('id', 'ASC');
    }
    
    public function map($items): array
    {
        $subcat_prefix=ItemSubCategory::where('id',$items->item_sub_category_id)->value('subcat_prefix');
        //$uom=uom::where('unit_name',$items->uom)->value('id');
        return [
            $items->id,
            $items->item_description,
            $items->item_category_id,
            $items->item_sub_category_id,
            $subcat_prefix,
            $items->pn_no,
            $items->rack_id,
            $items->group_id,
            $items->warehouse_id,
            $items->location_id,
        ];
    }

    public function headings(): array
    {
        return [
            'A1'=>'Item ID',
            'B1'=>'Item Description',
            'C1'=>'Cat ID',
            'D1'=>'Subcat ID',
            'E1'=>'Subcat Prefix',
            'F1'=>'PN No.',
            'G1'=>'Rack ID',
            'H1'=>'Group ID',
            'I1'=>'WH ID',
            'J1'=>'Location ID',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) { 
                $event->sheet->getStyle('A1:L1')->applyFromArray([
                    'font'=> [
                        'bold'=>true
                    ]
                ]);
                $event->sheet->getProtection()->setSheet(true);
                $event->sheet->getProtection()->setSort(true);
                $event->sheet->getProtection()->setInsertRows(true);
                $event->sheet->getProtection()->setFormatCells(true);
                $event->sheet->getProtection()->setPassword('Inventory2024!');
                $event->sheet->getStyle('A:J')->getAlignment()->setHorizontal('center');
                $event->sheet->setCellValue('L1', 'Instructions:');
                $event->sheet->setCellValue('L2', 'Get Cat ID, Subcat Cat ID and Subcat Prefix, UOM ID, Rack ID, Group ID, WH ID and Location ID in the reference sheet');
                $event->sheet->setCellValue('L3', "Leave PN No. column blank if there's none, system will generate if empty");
            }
        ];
    }

    public function sheets(): array
    {
        return [
            new CurrentInventoryExport(),
            new CategoryExport(),
            new RackExport(),
            new GroupsExport(),
            new WarehouseExport(),
            new LocationExport()
        ];
    }

    public function title(): string
    {
        return 'Items';
    }
}
