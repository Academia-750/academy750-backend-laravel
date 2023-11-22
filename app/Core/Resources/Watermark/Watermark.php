<?php
namespace App\Core\Resources\Watermark;

use App\Models\User;
use Illuminate\Support\Facades\File;
use setasign\Fpdi\PdfParser\StreamReader;
use App\Core\Resources\Watermark\Parsers\PDFWatermark;
use App\Core\Resources\Watermark\Parsers\ImageWatermark;

class Watermark
{
    public static function get()
    {
        /**
         * To allow mock ups
         */
        return app()->make(Watermark::class);
    }

    private function toUrl($path)
    {
        /**
         * In local we return HTTP, otherwise HTTPS
         */
        return asset($path, app()->environment() !== 'local');
    }

    public function pdf(string $url, string $name, User $user)
    {
        $path = "temp/material-{$name}-{$user->uuid}.pdf";

        if (File::exists(public_path($path))) {
            return self::toUrl($path);
        }

        $fileContent = file_get_contents($url);

        // Initialize FPDI, in this wrapper we will add the watermark
        $pdf = new PDFWatermark($user);

        // Get the total number of pages in the existing PDF
        $pageCount = $pdf->setSourceFile(StreamReader::createByString($fileContent));


        // Iterate through the pages and import them
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            // Import the current page from the existing PDF as a template
            $templateId = $pdf->importPage($pageNo);

            // Get the imported page dimensions
            $size = $pdf->getTemplateSize($templateId);

            // Add a new page with the same size as the imported page
            $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));

            // Use the imported page as a template
            $pdf->useTemplate($templateId);
        }

        // Output the modified PDF

        $pdf->Output('F', $path);

        return self::toUrl($path);
    }

    public function image($url, $name, $user)
    {
        $path = "temp/material-{$name}-{$user->uuid}.jpg";

        if (File::exists(public_path($path))) {
            return self::toUrl($path);
        }

        $imageWatermark = new ImageWatermark($user);

        $image = $imageWatermark->watermark($url);

        $image->save($path); // Replace with the desired output image path

        // Save the final image with the rotated text watermark
        return self::toUrl($path);

    }

}
