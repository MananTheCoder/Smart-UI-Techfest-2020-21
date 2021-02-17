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

    echo 'parsed: '.$image."\n";

    $outputdir = ($process_data.basename($image));

    @ mkdir($outputdir);

    $fout = 'hocr_output.txt';

    $parsed_data =  (new TesseractOCR($image))->hocr()->run();

    file_put_contents($outputdir.'/'.$fout, $parsed_data);

}
