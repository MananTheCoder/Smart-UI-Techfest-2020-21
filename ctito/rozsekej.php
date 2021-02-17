#!/bin/php
<?PHP

$steppx  = 25;
$img = 'malobarev.png';

$ident = explode(' ',`identify $img`);
$size = explode('x',trim($ident[2]));
$w = $size[0];
$h = $size[1];

$i = 0;

$tlx = 0;
$tly = 0;
while ($tly < $h && $tlx < $w) {
while ($tlx < $w) {
    echo "$steppx x $steppx + $tlx + $tly \n";
    $arn = $tlx.'_'.$tly;
    $i++;
    $tast = "convert $img -crop ".$steppx."x".$steppx. "+$tlx+$tly   -define histogram:unique-colors=true -format %c histogram:info:- ";
    $histnow = `$tast`;
    $parsed_histo[$arn] = recalcul($histnow);
    $tlx = $tlx + $steppx;

}
    $tly = $tly + $steppx;
    $tlx = 0;
}
var_dump($parsed_histo);


function recalcul($histostr)
{

    $maxshare = 0;

    $colt1 = explode("\n", trim($histostr));

    $numcolors = count($colt1);

    $rcount = 0;

    foreach ($colt1 as $r) {
        $ra = explode(":", $r);

        $rcount = $rcount + trim($ra[0]);
        $colname = trim($ra[1]);
        $coltable[$colname]['count']  = trim($ra[0]);;
    }

    foreach ($coltable as $colname => $rec) {
        $myshare = $rec['count'] / $rcount * 100;
        if ($myshare > $maxshare) {
            $maxshare = $myshare;
            $maxcolor = $colname;
        }
        $coltable[$colname]['share'] = $myshare;
    }

    $sumcols['cols'] = $numcolors;
    $sumcols['maxshare'] = $maxshare;
    $sumcols['maxcolor'] = $maxcolor;
    return array('sumcols' => $sumcols, 'coltable' => $coltable);
}

