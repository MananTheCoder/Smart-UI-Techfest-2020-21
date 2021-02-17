#!/usr/bin/php
<?PHP
/**
 * 3.grid2json.php
 *
 * Smart UI
 *
 *  and finaly / order grid files and combine to elements with parameters
 * this version generate only text elements
 *
 * call * php ./3.elements2grid.php
 *
 */
ini_set('display_errors',1);

require_once('conf/smartui.php');

$generated = glob($grid_recognized. "*.png");


foreach ($generated as $gridpath) {

    echo " json for $gridpath \n";

    genTextElement($gridpath);

}

function genTextElement($img_grid) {

    global $grid_recognized, $output_json;
    $oa = [];
    $ia = explode('/',$img_grid);
    $img = $ia[1];

    $words = glob($img_grid.'/*word*strlen*');
    foreach($words as $word) {
        $oa[] = getWordJson($word);
    }
    if(!count($oa)) return;

    $output_file = $output_json.$img.'-output.json';
    $fop = fopen($output_file,'w');
    fputs($fop, "\n".json_encode($oa, JSON_PRETTY_PRINT)."\n");
}

function getWordJson($word) {
    $a = unserialize(file_get_contents($word));
    $elem  = array(
        'element'=>'text',
        'x'=>0+$a['bbox'][0],
        'y'=>0+$a['bbox'][1],
        'width'=>$a['bbox'][2] - $a['bbox'][0],
        'height'=>$a['bbox'][3] - $a['bbox'][1],
        'properties'=>array('text'=>$a['text'])
    );
    return $elem;
}

