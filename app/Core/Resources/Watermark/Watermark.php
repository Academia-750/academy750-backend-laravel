<?php
namespace App\Core\Resources\Watermark;

use App\Core\Services\UserPDF;
use App\Models\User;
use Illuminate\Support\Facades\File;
use setasign\Fpdi\PdfParser\StreamReader;
use Intervention\Image\ImageManagerStatic as Image;



class Watermark
{

    private static function toUrl($path)
    {
        return asset($path, app()->environment('local') !== 1);
    }

    public static function pdf(string $url, string $name, User $user)
    {
        $path = "temp/material-{$name}-{$user->uuid}.pdf";

        if (File::exists(public_path($path))) {
            return self::toUrl($path);
        }

        $fileContent = file_get_contents($url);

        // Initialize FPDI
        $pdf = new UserPDF($user);

        // Get the total number of pages in the existing PDF
        $pageCount = $pdf->setSourceFile(StreamReader::createByString($fileContent));


        // Iterate through the pages and import them
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pdf->AddPage();

            // Import the current page from the existing PDF as a template
            $templateId = $pdf->importPage($pageNo);

            // Use the imported page as a template
            $pdf->useTemplate($templateId); // Set position and size of the imported page
        }

        // Output the modified PDF

        $pdf->Output('F', $path);
        // Return https except in localhost
        return self::toUrl($path);
    }

    static public function image($url, $name, $user)
    {
        $path = "temp/material-{$name}-{$user->uuid}.jpg";

        // if (File::exists(public_path($path))) {
        //     return self::toUrl($path);
        // }


        /**
         *  Draw a rectangle in a empty canvas ro rotate the canvas
         */
        $width = 550;
        $height = 140;
        $rectangleCanvas = Image::canvas($width, $height);
        $rectangleCanvas->rectangle(0, 0, $width, $height, function ($draw) {
            $draw->background(array(244, 67, 54, 0.3));
            // $draw->background(array(0, 0, 0, 0.3));
        });
        $rectangleCanvas->rotate(45);

        /**
         * Add the rectangle and the texts as water mark
         */
        $img = Image::make($url);

        $img->insert($rectangleCanvas, 'bottom-right', -110, -100);

        // Add the text
        // The sizeIs SIZE 40 for 15 chars. Each 5 chars reduce size 2
        $name = $user->full_name;

        $size = 40 - 4 * floor((strlen($name) - 15) / 5);

        $img->text($name, $img->width() - 275, $img->height() - 15, function ($font) use ($size) {
            $font->file(storage_path('app/public/fonts/Arial.ttf')); // Use the built-in Arial font
            $font->size($size); // Adjust the font size as needed
            // $font->color('#ffffff'); // Adjust the text color and opacity
            $font->color('#FFD700');
            $font->angle(45); // Rotate the text by 45 degrees
        });

        $img->text($user->dni, $img->width() - 175, $img->height() - 25, function ($font) use ($user) {
            $font->file(storage_path('app/public/fonts/Arial.ttf')); // Use the built-in Arial font
            $font->size(42); // Adjust the font size as needed
            // $font->color('#ffffff'); // Adjust the text color and opacity
            $font->color('#FFD700');
            $font->angle(45); // Rotate the text by 45 degrees


        });

        // Save the final image with the rotated text watermark
        $img->save($path); // Replace with the desired output image path

        return self::toUrl($path);

    }

}