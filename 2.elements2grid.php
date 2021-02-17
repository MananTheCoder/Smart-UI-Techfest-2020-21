#!/usr/bin/php
<?PHP

/**
 * 2.elements2grig.php
 *
 * Smart UI
 *
 *  parse tesseract output to "file database" / folder with serialized arrays
 * organized by position in folder - filename is x-y-x2-y2 organized
 * file-title & array params see:
 * http://kba.cloud/hocr-spec/1.2/
 *
 * call * php ./2.elements2grid.php
 *
 */
ini_set('display_errors',1);

require_once('conf/smartui.php');

$preoutput_arr = glob($process_data. "*/*.txt");

foreach ($preoutput_arr as $ocr_html) {
    echo "
    ---------- ";
    echo $ocr_html."\n";
    $fc = explode("\n",trim(file_get_contents($ocr_html)));
    $ia = explode('/',$ocr_html);
    $imgname = $ia[1];
    $methodname = $ia[2];
    @ mkdir($grid_recognized.$imgname);

foreach($fc as $l) {
    $l = trim($l);
    if(strpos($l,'class')) {
        $class = getParam($l,'class');
        $id = getParam($l,'id');
        $params = getTitleParams($l);
        $text = trim(getSpanText($l));
        // simple cleaning
        $is_mature = true; // not 'word' elemens are matured for now
        $past_word_part_select = false;
        if(strpos($class,'_word') > 0) {
        $text = str_replace(array('+','~','(',')','{','}'),'',$text);
            $is_mature = false;
            // element with only nonalfanumeric text are needs reparse / erros
           preg_match('/[a-z0-9]{1,}/i',$text, $notonlyspecialchars);
            if(count($notonlyspecialchars)) {
                $is_mature = true;
                }

        }

        if(strtoupper($text) === 'V' || strtoupper($text) === 'vV') {
            $is_mature = false;
            $past_word_part_select= true;

        }

        if($is_mature === true) {
            $par_in_fname = '';
            if(@ $params['x_wconf']) {
              $par_in_fname .= '-wconf_' . $params['x_wconf'];
              }
          if($text) {
              $par_in_fname .= '-strlen_' . strlen($text);
              $par_in_fname .= '-text__'. mb_ereg_replace("([\.]{2,})", '', mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $text)).'__';

            }

            $fnamea = [] ;
            foreach($params['bbox'] as $inum ) {
                $fnamea[] = sprintf("%05d", $inum,4);
            }
            $grid_file = implode('_',$fnamea).'-'.$id.$par_in_fname.'-'.$methodname;
            if($text)
                $serialized = serialize(array_merge($params,array('text'=>$text)));
            else
                $serialized = serialize($params);

            $lastfn = $grid_recognized.$imgname.'/'.$grid_file;

            $fo = fopen($grid_recognized.$imgname.'/'.$grid_file,'w');
            fputs($fo,$serialized);
        }

        if ($past_word_part_select === true) {
                $grid_file = implode('_', $fnamea);
                rename($lastfn,$lastfn.'__SELECT__'.$grid_file);
       }



    }
}

}

function getSpanText($l) {
    $regex_inspan = '#<\s*?span\b[^>]*>(.*?)</span\b[^>]*>#s';
    preg_match($regex_inspan,$l,$matches);
    if(!count($matches)) return ;
   // var_dump($matches);
    if(@!$matches[1])
        return;
    $text = trim($matches[1]);
    if($text) return $text;
    return ;
}

function getParam($l,$p) {
    if(strpos($l,"$p=")) {
        $param = preg_replace('/\'.*/','',preg_replace("/.*$p=\'/",'',$l));
        return $param;
    }
}
function getTitleParams($l) {
    if(strpos($l,'title="')) {
        $title_params= explode(';',trim(preg_replace('/".*/','',preg_replace("/.*title=\"/",'',$l))));
    } elseif(strpos($l,'title=\'')) {
        $title_params= explode(';',trim(preg_replace('/\'.*/','',preg_replace("/.*title='/",'',$l))));
    } else {
        return ;
    }
    $params = [];

    foreach($title_params as $p) {
        $p = trim($p);
        $p_tok = explode(' ',$p);
        $parn = array_shift($p_tok);
        if(count($p_tok) == 1)
        $params[$parn] = $p_tok[0];
        else
        $params[$parn] = $p_tok;
    }

    return $params;
}
