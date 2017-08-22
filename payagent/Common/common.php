<?php
function fDate($l1,$l2=0){
    if (strlen($l1)==0) { return ; }
    switch ($l2) {
        case '0':
        	$I1 = date('Y-m-d H:i:s',$l1);
            break;
        case '1':
            $I1 = date('Y-n-j G:i:s',$l1);
            break;
        case '2':
        	$I1 = date('Y-m-d',$l1);
            break;
        case '3':
            $I1 = date('Y-n-j',$l1);
            break;
		case '4':
			$I1 = date('Y年m月d日',$l1);
            break;
		case '5':
			$I1 = date('m月 Y',$l1);
            break;
		case '6':
			$I1 = date('Y-m',$l1);
            break;
        default:
            $I1 = date($l2,$l1);
            break;
    }
    return $I1;
}


?>