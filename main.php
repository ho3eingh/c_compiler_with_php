_<?php
$code = file_get_contents('./code.c');
$lines = explode(PHP_EOL, $code);

$block_counter=0;
$block_max=0;
global $block_show;
$block_show=0;
global $i;
$i=0;
global $index;
$index=array();
foreach ($lines as $line_number => $line) {


    echo"\n";
    $empty_finder=1;


    if(preg_match_all('/\W+|\w+/',$line, $matches)){
        echo"\n";
        echo    "******************************************************";
        echo"\n";
        echo 'line: '. ($line_number + 1) . ' => ' . PHP_EOL . $line . PHP_EOL;
        handle($line , $empty_finder,$block_counter,$line_number,$i);
        $block_counter=block_engine($line,$block_counter);




        $p=0;
        $e=0;
        $len=count($index)-1;
        echo "\n";
        echo"~~~~~~~~~~~~~~~~[Row , Column]~~~~~~~~~~~~~~~~~~~~~~~~";
        echo"\n";
        while($p<=$len){
            echo $index[$p];  echo"  ( " . $line_number+1 . " , " . $index[$p+1] . " )"; echo"\n";
            $p=$p+2;


        }
    }

    $i=0;
    $index=array();
}
function operator_column_engine($line){
    $next_ind_test=0;
    $z=0;
    $v=0;
    $spl=str_split($line);
    $len=count($spl)-1;
    global $i;
    global $index;
    while($v<=$len){

        if(($spl[$v]==("+"))||($spl[$v]==("-"))||($spl[$v]==("*"))||($spl[$v]==("("))||($spl[$v]==(")"))||($spl[$v]==("{"))||($spl[$v]==("}"))||($spl[$v]==("%"))||($spl[$v]==("&"))||($spl[$v]==("|"))||($spl[$v]==("="))||($spl[$v]==("!"))||($spl[$v]==("<"))||($spl[$v]==(">"))||($spl[$v]==("#"))){
            $next_ind_test=$spl[$v];
            $z=$v;
            $z++;
            if ($spl[$z]==$spl[$v]){

                $index[$i]=$spl[$z] . $spl[$v];
                $i++;
                $index[$i]=$v+1;
                $v=$v+2;
                $i++;

            }else{

                $index[$i]=$spl[$v];
                $i++;
                $index[$i]=$v+1;
                $i++;
                $v++;
            }


        } else{

            $v++;
        }
    }

}

function block_engine($line,$block_counter){
    global $block_counter;
    global $block_max;
    global $block_show;

    $matches=array();
    preg_match_all('/{/',$line,$matches);
    foreach($matches[0] as $match){

        if($match=="{"){
            echo"\n";
            $block_counter=$block_counter+1;
            $block_max=$block_max+1;
            $block_show=$block_max;
            echo"* Block : "; echo $block_show; echo" Begins";
        }
    }
    $matches=array();
    preg_match_all('/}/',$line,$matches);
    foreach($matches[0] as $match){

        if($match=="}"){
            echo"\n";
            echo"* End OF Block: "; echo $block_show;
            $block_counter=$block_counter-1;
            $block_show=$block_counter;

        }
    }
    return($block_counter);
}

function column_engine($all_together,$line,$line_number){

    global $i;
    $matches=array();
    $a=array();
    $matches_co=array();
    $test1=array();
    $test=array();
    global $index;

    $all_together = implode(' | ',array_unique(explode(' | ', $all_together)));
    preg_match_all('/[^\s\|]+/',$all_together,$matches);
    foreach($matches[0] as $match){

        $matches_co=array();
        $test=array();
        $test1=array();
        if(preg_match_all(" #$match # ", $line, $matches_co, PREG_OFFSET_CAPTURE)) {


            foreach($matches_co as $test){


            }

            if(!empty($test)){

                foreach($test as $test1){

                    $index[$i]=$test1[0];
                    $i++;
                    $index[$i]=$test1[1]+1;
                    $i++;

                }


            }
        }
    }

}
function token_print($tokenss,$token_type,$line,$line_number){

    $column_num=array();
    $all_together = implode(" | ",$tokenss);

    if (preg_match_all('/[^\s\|]/',$all_together,$matches)){
        if($token_type!="Operator"){

            column_engine($all_together,$line,$line_number);
        }else
        {

            operator_column_engine($line);
        }
        echo "-" . $token_type . ": " . $all_together;
        echo"\n";

    }

}
function handle($opart , $empty_finder, $block_counter,$line_number,$i){
    global $block_show;


    echo PHP_EOL;
    $arr1 = str_split($opart);
    $i=0;

    $matches=array();
    preg_match_all(

        '/\".*[a-zA-Z]+\d*.*\"|[a-zA-Z]+\d*|\'[a-zA-Z]+\'/',

        $opart,
        $matches
    );
    $flag_pr_key=0;
    $ii=0;
    $jj=0;
    $identifires=array();
    $keyword=array();
    foreach($matches as $tt){

    }
    // print_r($tt);
    $double_quo=array();
    $matches=array();
    $sus_key=array();
    foreach($tt as $ttt)
    {

        if($ttt=="void"||$ttt=="main"||$ttt=="string"||$ttt=="int"||$ttt=="float"||$ttt=="double"||$ttt=="for"||$ttt=="while"||$ttt=="foreach"||$ttt=="switch"||$ttt=="case"||$ttt=="if"||$ttt=="char"||$ttt=="cout"||$ttt=="cin"||$ttt=="include"||$ttt=="iostream"||$ttt=="conio"){


            $keyword[$ii]=$ttt;

            $ii=$ii+1;
            echo"\n";

        }

        else
        {
            if(preg_match_all('/\"+|\'+/', $ttt, $matches)) {

            }
            else{
                $identifires[$jj]=$ttt;
                $jj=$jj+1;
            }
        }
    }

    token_print($keyword,"Keyword",$opart,$line_number);
    token_print($identifires,"Identifires",$opart,$line_number);

    $matches=array();

    $flag_semi=0;
    $raw_number=array();
    $q=0;
    preg_match_all(

        '/[\s]+\d+[.]*\d*|[+]+\d+[.]*\d*|[(]\d+[.]*\d*|[-]+\d+[.]*\d*|[*]+\d+[.]*\d*|[(]\d+[.]*\d*|[=]\d+[.]*\d*|\"+.*\d+[.]*\d*.*\"+|\'\d+\'+/',

        $opart,
        $matches
    );
    //print_r($matches);
    foreach($matches[0] as $sus_number)
    {
        $arr2 = str_split($sus_number);
        foreach($arr2 as $single_part){
            if($single_part=='"'||$single_part=="'"){
                $flag_semi=1;
            }

        }
        if($flag_semi==1){

        }else{
            $raw_number[$q]=$sus_number;
            $q=$q+1;
        }
        $flag_semi=0;
    }
    $all_together = implode(" | ", $raw_number);

    preg_match_all(

        '/\d+[.]*\d*/',

        $all_together,
        $matches
    );

    foreach($matches as $number)
    {

        token_print($number,"Numbers",$opart,$line_number);

    }


    $matches=array();


    preg_match_all(

        '/[(]|[)]|[{]|[}]|[!][=]|[+][+]*|[-][-]*|[=]|[=][=]*|[|][|]*|[&][&]*|[<][<]*|[>][>]*|[#]/',

        $opart,
        $matches
    );

    foreach($matches as $operators)
    {

        token_print($operators,"Operator",$opart,$line_number);

    }

    $matches=array();

    preg_match_all(

        '/\".*\"|\'.*\'/',

        $opart,
        $matches
    );
    $match0=array();
    $all_together = implode(" | ",$matches[0]);
    preg_match_all('/[^"]+|[^"$]|[^\']+|[^\'$]/',$all_together,$match0);

    foreach($match0 as $match00){


        token_print($match00,"Literals",$opart,$line_number);
    }

    $matches=array();
    preg_match_all(

        '/;|,|:/',

        $opart,
        $matches
    );

    foreach($matches as $delimiter)
    {

        token_print($delimiter,"Delimiters",$opart,$line_number);

    }

    echo "*Block: " . $block_show;

}
