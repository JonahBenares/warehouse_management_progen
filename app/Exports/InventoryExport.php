<?php

namespace App\Exports;
use App\Models\Items;
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

class InventoryExport implements ShouldAutoSize, WithHeadings, WithEvents, WithMultipleSheets, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    //use Exportable;

    // public function query()
    // {
    //     return Items::query()->with('sub_category')->where('begbal','0')->orderBy('id', 'ASC');
    // }
    
    // public function map($items): array
    // {
    //     return [
    //         $items->item_description,
    //         $items->item_category_id,
    //         $items->item_sub_category_id,
    //         $items->subcat_prefix,
    //         $items->uom,
    //         $items->pn_no,
    //     ];
    // }

    public function headings(): array
    {
        return [
            'A1'=>'Item Description',
            'B1'=>'Cat ID',
            'C1'=>'Subcat ID',
            'D1'=>'Subcat Prefix',
            'E1'=>'PN No.',
            'F1'=>'Rack ID',
            'G1'=>'Group ID',
            'H1'=>'WH ID',
            'I1'=>'Location ID',
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
                $event->sheet->getStyle('A:I')->getAlignment()->setHorizontal('center');
                $event->sheet->setCellValue('L1', 'Instructions:');
                $event->sheet->setCellValue('L2', 'Get Cat ID, Subcat Cat ID and Subcat Prefix, UOM ID, Rack ID, Group ID, WH ID and Location ID in the reference sheet');
                $event->sheet->setCellValue('L3', "Leave PN No. column blank if there's none, system will generate if empty");
            }
        ];
    }

    public function sheets(): array
    {
        return [
            new InventoryExport(),
            new CategoryExport(),
            new RackExport(),
            new GroupsExport(),
            new WarehouseExport(),
            new LocationExport(),
        ];
    }

    public function title(): string
    {
        return 'Items';
    }
}
