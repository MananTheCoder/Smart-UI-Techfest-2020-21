#!/usr/bin/php
<?php
/**
 * 1.tesseract_parser.php
 *
 * Smart UI
 *
 *  parse all images in  source_imgs directory
 * call
 * php ./1.tesseract_parser.php
 *
 */
namespace thiagoalessio\TesseractOCR;

require_once('vendor/autoload.php');
require_once('conf/smartui.php');

$source_imgs = glob($sources_repo . "*.png");

foreach ($source_imgs as $image) {

    echo 'parsing: '.$image."\n";

    $outputdir = ($process_data.basename($image));

    @ mkdir($outputdir);

    $fout = 'hocr.txt';

    // parse with TesseractOCR

    $parsed_data =  (new TesseractOCR($image))->hocr()->run();

    file_put_contents($outputdir.'/'.$fout, $parsed_data);

    // encode image / extended  parsing

    $img_filename = basename($image);

    $encoded_dir = $encoded_sources.''.$img_filename.'/';

    @ mkdir($encoded_dir);

    encodeSourceImage($image,$encoded_dir);

    // parse each encoded images  too
    $encoded_imgs = glob($encoded_dir. "*.png");
    foreach ($encoded_imgs as $encoded_image) {
        echo 'parsing: '.$encoded_image."\n";

        $fout = basename($encoded_image).'.txt';

        $parsed_data =  (new TesseractOCR($encoded_image))->hocr()->run();

        file_put_contents($outputdir . '/' . $fout, $parsed_data);

    }

}

function encodeSourceImage($source_image,$encoded_dir) {

    echo "encode versions from $source_image \n";

    // black & white version for better ocr

    $enc[] = 'convert ' . $source_image . ' +dither -colors 3 -colors 2 -colorspace gray -normalize  \
        ' . $encoded_dir . 'ditherize.png';

    $enc[] = 'convert ' . $source_image . ' +dither -colors 16 ' . $encoded_dir . 'colorspace_16.png';

    $enc[] = 'convert ' . $encoded_dir . 'ditherize.png -bordercolor white -border 1x1 -alpha set -channel RGBA -fuzz 20%  -fill none -floodfill +0+0 white  -shave 1x1 \
    ' . $encoded_dir . 'inprocess.png';

    $enc[] = 'convert ' . $encoded_dir. 'inprocess.png -background black -flatten  ' . $encoded_dir . 'invertbuttons.png';
/*
    // negative monochrome for buttons and problematic parts
    $enc[] = 'convert ' . $encoded_dir . '/ditherize.png -monochrome ' . $encoded_dir . '/mono.png';

    //. $source_image . ' +dither -colors 3 -colors 2 -colorspace gray -normalize  \

/*
    $enc[]  = 'convert '.$source_image.' -bordercolor white -border 1x1 \
    -alpha set -channel RGBA -fuzz 20% \
    -fill none -floodfill +0+0 white \
    -shave 1x1 ' .$encoded_dir.'/inprocess.png';


    $enc[]  = 'convert ' . $encoded_dir . '/mono.png \
    -background black -flatten  \
     ' .$encoded_dir.'/invertbuttons.png';
*/
     // dont parse inprocess.png
     $enc[] = 'rm ' . $encoded_dir . '/inprocess.png';


    foreach($enc as $command) {
        `$command`;
    }



}
