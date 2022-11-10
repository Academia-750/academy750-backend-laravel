<?php

namespace App\Exports\Api\Topics\v1;


use App\Models\Topic;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TopicsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private $Topics_id;
    public function __construct($Topics_id) {
        $this->Topics_id = $Topics_id;
    }

    public function query()
    {
        return Topic::query()->whereIn('id', $this->Topics_id);
    }

    public function headings(): array
    {
        return [
            // headers file
        ];
    }

    public function map($row): array
    {
        return [
            // values file by column
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->setSize(18)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN ));
        $sheet->getStyle('A:I')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }
}
