<?php
namespace App\Core\Services;

use App\Models\User;
use setasign\Fpdi\Fpdi;

/**
 * This PDF will add a watermark with the user name and DNI
 */
class UserPDF extends Fpdi
{
    private User $user;

    function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    /**
     * Inspired in https://www.studentstutorial.com/fpdf/watermark.php
     * But modified the header for our own scenario
     */
    var $angle = 0;

    function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1)
            $x = $this->x;
        if ($y == -1)
            $y = $this->y;
        if ($this->angle != 0)
            $this->_out('Q');
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    function _endpage()
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }


    function RotatedText($x, $y, $txt, $angle)
    {
        /* Text rotated around its origin */
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }

    /**
     * Our own water mark on the header (behind)
     */
    function Header()
    {
        $sourcePageWidth = $this->getPageWidth();
        $sourcePageHeight = $this->getPageHeight();

        /* Put the watermark */
        $this->SetTextColor(255, 192, 203);

        $name = "Abel Bordonado Lillo MuyLargo"; // $this->user->full_name;
        // Is SIZE 16 for 15 chars. Each 5 chars reduce size 2
        $size = 16 - 2 * floor((strlen($name) - 15) / 5);
        $this->SetFont('Arial', 'B', $size);

        $this->RotatedText($sourcePageWidth - 38, $sourcePageHeight - 2, $name, 45);

        // DNI is fix (always same size)
        $this->SetFont('Arial', 'B', 18);

        $this->RotatedText($sourcePageWidth - 26, $sourcePageHeight - 2, $this->user->dni, 45);

    }
}