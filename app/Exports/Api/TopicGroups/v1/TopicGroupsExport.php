<?php

namespace App\Exports\Api\TopicGroups\v1;


use App\Models\TopicGroup;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TopicGroupsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private $TopicGroups_id;
    public function __construct($TopicGroups_id) {
        $this->TopicGroups_id = $TopicGroups_id;
    }

    public function query()
    {
        return TopicGroup::query()->whereIn('id', $this->TopicGroups_id);
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