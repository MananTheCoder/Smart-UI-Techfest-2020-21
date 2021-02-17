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

    parseElements($gridpath);

}

function parseElements($img_grid) {


    global $grid_recognized, $output_json;
    $oa = [];
    $ia = explode('/',$img_grid);
    $img = $ia[1];

    // main 'page' div
    $words = glob($img_grid.'/*page_1-hocr*');

    if(@ count($word[0])) {
        $a = unserialize(file_get_contents($word[0]));
        $oa[] = getDivJson($a);
    }


    // traverse in different image parsing
    $words = glob($img_grid.'/*text*invertbuttons*');
    foreach ($words as $word) {
        $method = preg_replace('/.*-/', '', $word);
        $a = unserialize(file_get_contents($word));
                $oa[] = getButtonJson($a);

    }


    $words = glob($img_grid.'/*text*ditherize*');

    $last_method  = '';
    $last_text = '';
    foreach($words as $word) {
        $method = preg_replace('/.*-/','',$word);
        $a = unserialize(file_get_contents($word));
            if(strpos($word,'txt__SELECT')) {
                $oa[] = getSelectJson($a);
            } else {
                $oa[] = getWordJson($a);
            }
            $last_method = $method;
            $last_text= $a['text'];
    }

    if(!count($oa)) return;

    // output json generated
    $output_file = $output_json.$img.'-output.json';
    @ unlink($output_file);
    $fop = fopen($output_file,'w');
    fputs($fop, "\n".json_encode($oa, JSON_PRETTY_PRINT)."\n");
}

function getWordJson($a) {

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

function getDivJson($a) {
    $elem  = array(
        'element'=>'div',
        'x'=>0+$a['bbox'][0],
        'y'=>0+$a['bbox'][1],
        'width'=>$a['bbox'][2] - $a['bbox'][0],
        'height'=>$a['bbox'][3] - $a['bbox'][1]
    );
    return $elem;
}

function getButtonJson($a) {
    $elem  = array(
        'element'=>'button',
        'x'=>0+$a['bbox'][0],
        'y'=>0+$a['bbox'][1],
        'width'=>$a['bbox'][2] - $a['bbox'][0],
        'height'=>$a['bbox'][3] - $a['bbox'][1],
        'properties'=>array('text'=>$a['text'])
    );
    return $elem;
}


function getSelectJson($a)
{
    $elem  = array(
        'element' => 'select',
        'x' => 0 + $a['bbox'][0],
        'y' => 0 + $a['bbox'][1],
        'width' => $a['bbox'][2] - $a['bbox'][0],
        'height' => $a['bbox'][3] - $a['bbox'][1],
        'properties' => array('text' => $a['text'])
    );
    return $elem;
}

function getFormElementJson($a) {
    $elem  = array(
        'element'=>'button',
        'x'=>0+$a['bbox'][0],
        'y'=>0+$a['bbox'][1],
        'width'=>$a['bbox'][2] - $a['bbox'][0],
        'height'=>$a['bbox'][3] - $a['bbox'][1],
        'properties'=>array('text'=>$a['text'])
    );
    return $elem;
}

