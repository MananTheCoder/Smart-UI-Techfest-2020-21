<?php
/**
 * parse_image_cli
 *
 * Smart UI
 *
 * call with path to specified file
 * php ./parse_image_cli.php source_imgs/XY.png
 *
*/
namespace thiagoalessio\TesseractOCR;

require('vendor/autoload.php');
require('conf/smartui.php');

$image = $argv[1];



    echo $image."\n";
    $outputdir = ($process_data.basename($image));
    @  mkdir($outputdir);
    $fout = 'indiv_output.txt';

    $parsed_data =  (new TesseractOCR($image))->hocr()->run();

    file_put_contents($outputdir.'/'.$fout, $parsed_data);

