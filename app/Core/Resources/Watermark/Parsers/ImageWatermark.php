<?php
namespace App\Core\Resources\Watermark\Parsers;

use App\Models\User;
use Intervention\Image\ImageManager as Image;

/**
 * This Will add a watermark to the Image
 */
class ImageWatermark
{

    private $GRAY_COLOR = "#222222";

    private User $user;

    function __construct(User $user)
    {
        $this->user = $user;
    }

    private function hexToRGB($hex)
    {
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        return array($r, $g, $b);
    }


    function watermark($url)
    {
        /**
         * Add the rectangle and the texts as water mark
         */
        $img = (new Image())->make($url);

        $this->footerLegalNotice($img);
        $this->diagonalNameIDBar($img);

        return $img;
    }


    function diagonalNameIDBar($img)
    {
        /**
         *  Draw a rectangle in a empty canvas ro rotate the canvas
         */
        $width = 550;
        $height = 140;
        $rectangleCanvas = (new Image())->canvas($width, $height);
        $rectangleCanvas->rectangle(0, 0, $width, $height, function ($draw) {
            $draw->background(array(244, 67, 54, 0.3));
            // $draw->background(array(0, 0, 0, 0.3));
        });
        $rectangleCanvas->rotate(45);


        $img->insert($rectangleCanvas, 'bottom-right', -110, -100);

        // Add the text
        // The sizeIs SIZE 40 for 15 chars. Each 5 chars reduce size 2
        $name = $this->user->full_name;

        $size = 40 - 4 * floor((strlen($name) - 15) / 5);

        $img->text($name, $img->width() - 275, $img->height() - 15, function ($font) use ($size) {
            $font->file(storage_path('app/public/fonts/Arial.ttf')); // Use the built-in Arial font
            $font->size($size); // Adjust the font size as needed
            // $font->color('#ffffff'); // Adjust the text color and opacity
            $font->color('#FFD700');
            $font->angle(45); // Rotate the text by 45 degrees
        });

        $img->text($this->user->dni, $img->width() - 175, $img->height() - 25, function ($font) {
            $font->file(storage_path('app/public/fonts/Arial.ttf')); // Use the built-in Arial font
            $font->size(42); // Adjust the font size as needed
            // $font->color('#ffffff'); // Adjust the text color and opacity
            $font->color('#FFD700');
            $font->angle(45); // Rotate the text by 45 degrees
        });
    }

    function footerLegalNotice($img)
    {
        $rectangleCanvas = (new Image())->canvas($img->width(), 50);
        $rectangleCanvas->rectangle(0, 0, $img->width(), 50, function ($draw) {
            $draw->background(array(...$this->hexToRGB($this->GRAY_COLOR), 0.4));
        });

        $img->insert($rectangleCanvas, 'bottom-left', 0, 0);

        /**
         * TEXT: Warning
         */


        $warning1 = "Este documento es personal e intransferible. Academia750 se reserva el derecho de emprender cualquier accion legal";
        $warning2 = "para preservar sus derechos de propiedad intelectual frente a las copias o difusion de su contenido";

        $img->text($warning1, 10, $img->height() - 30, function ($font) {
            $font->file(storage_path('app/public/fonts/Arial.ttf')); // Use the built-in Arial font
            $font->size(16); // Adjust the font size as needed
            $font->color('#FFD700');
        });
        $img->text($warning2, 10, $img->height() - 10, function ($font) {
            $font->file(storage_path('app/public/fonts/Arial.ttf')); // Use the built-in Arial font
            $font->size(16); // Adjust the font size as needed
            $font->color('#FFD700');
        });
    }
}