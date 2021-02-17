#!/bin/php
<?PHP

$steppx  = 25;
$img = 'malobarev.png';

$histostr = `convert $img  -define histogram:unique-colors=true -format %c histogram:info:- `;

function recalcul($histostr) {

    $maxshare = 0;

    $colt1 = explode("\n",trim($histostr));

    $numcolors = count($colt1);

    $rcount = 0;

    foreach($colt1 as $r) {
        $ra = explode(":",$r);

        $rcount = $rcount + trim($ra[0]);
        $colname= trim($ra[1]);
        $coltable[$colname]['count']  = trim($ra[0]);;
    }

    foreach($coltable as $colname=>$rec) {
            $myshare = $rec['count']/$rcount*100;
            if($myshare > $maxshare) {
                $maxshare = $myshare;
                $maxcolor = $colname;
            }
            $coltable[$colname]['share'] = $myshare;
    }

   $sumcols['cols'] = $numcolors;
   $sumcols['maxshare'] = $maxshare;
   $sumcols['maxcolor'] = $maxcolor;
    return array('sumcols'=>$sumcols,'coltable' => $coltable);
}


$colset = recalcul($histostr);
var_dump($colset);
/*
foreach($colset as $col) {
    if($col['share'] > 1)
        var_dump($col);
}

//var_dump($allcolors);

/* */
