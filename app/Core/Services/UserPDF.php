<?php
namespace App\Core\Services;

use App\Models\User;
use setasign\Fpdi\Fpdi;

/**
 * This PDF will add a watermark with the user name and DNI
 * Alpha: http://www.fpdf.org/en/script/script74.php
 * Rotation: https://www.studentstutorial.com/fpdf/watermark.php
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
     * ALPHA
     */
    // alpha: real value from 0 (transparent) to 1 (opaque)
    // bm:    blend mode, one of the following:
    //          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
    //          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
    protected $extgstates = array(); // For the Alpha

    function SetAlpha($alpha, $bm = 'Normal')
    {
        // set alpha for stroking (CA) and non-stroking (ca) operations
        $gs = $this->AddExtGState(array('ca' => $alpha, 'CA' => $alpha, 'BM' => '/' . $bm));
        $this->SetExtGState($gs);
    }

    function AddExtGState($parms)
    {
        $n = count($this->extgstates) + 1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    function SetExtGState($gs)
    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    function _enddoc()
    {
        if (!empty($this->extgstates) && $this->PDFVersion < '1.4')
            $this->PDFVersion = '1.4';
        parent::_enddoc();
    }

    function _putextgstates()
    {
        for ($i = 1; $i <= count($this->extgstates); $i++) {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_put('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_put(sprintf('/ca %.3F', $parms['ca']));
            $this->_put(sprintf('/CA %.3F', $parms['CA']));
            $this->_put('/BM ' . $parms['BM']);
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    function _putresourcedict()
    {
        parent::_putresourcedict();
        $this->_put('/ExtGState <<');
        foreach ($this->extgstates as $k => $extgstate)
            $this->_put('/GS' . $k . ' ' . $extgstate['n'] . ' 0 R');
        $this->_put('>>');
    }

    function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }

    /**
     * ROTATION
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
     * OUR CUSTOMIZATION
     */
    function Footer()
    {
        $sourcePageWidth = $this->getPageWidth();
        $sourcePageHeight = $this->getPageHeight();
        /**
         * RECTANGLE
         */
        $this->SetAlpha(0.2);
        $this->SetFillColor(244, 67, 54);
        $recX = $sourcePageWidth + 27;
        $recY = $sourcePageHeight - 50;
        $this->Rotate(225, $recX, $recY);
        $this->Rect($recX, $recY, 100, 20, 'F');
        $this->Rotate(0);

        /**
         * TEXT: Name + DNI
         */
        $this->SetAlpha(0.7);

        $this->SetTextColor(255, 255, 255);

        $name = $this->user->full_name;
        // Is SIZE 16 for 15 chars. Each 5 chars reduce size 2
        $size = 16 - 2 * floor((strlen($name) - 15) / 5);
        $this->SetFont('Arial', 'B', $size);

        $this->RotatedText($sourcePageWidth - 38, $sourcePageHeight - 2, $name, 45);

        // DNI is fix (always same size)
        $this->SetFont('Arial', 'B', 18);

        $this->RotatedText($sourcePageWidth - 26, $sourcePageHeight - 2, $this->user->dni, 45);
    }
}