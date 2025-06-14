<?php

namespace App\Exports;

use App\Models\Items;
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
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Protection;

//class BeginningBalanceExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
class BeginningBalanceExport implements FromView, WithTitle, WithEvents, ShouldAutoSize
{
    public function view(): View
    {
        return view('begbal_format', [
            'begbal' => Items::select('id','item_description')->where('begbal','0')->orderBy('id', 'ASC')->get()
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) { 
                $event->sheet->getStyle('A1:F1')->applyFromArray([
                    'font'=> [
                        'bold'=>true
                    ]
                ]);
                $event->sheet->getProtection()->setSheet(true);
                $event->sheet->getProtection()->setSort(true);
                $event->sheet->getProtection()->setInsertRows(true);
                $event->sheet->getProtection()->setFormatCells(true);
                $event->sheet->getProtection()->setPassword('Inventory2024!');   
                $event->sheet->getParent()->getActiveSheet()->getStyle('D')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED); 
                $event->sheet->setCellValue('F1', 'Instructions:');
                $event->sheet->setCellValue('F2', 'Just fill out quantity column.');
                $event->sheet->setCellValue('F3', "Do not edit other columns.");
            }
        ];
    }

    public function title(): string
    {
        return 'Beggining Balance';
    }
}
