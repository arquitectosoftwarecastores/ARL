<?php

	function orientacion($x1,$y1,$x2,$y2)
	{

	    $angle = 0;
	    $dif_x = abs($x2)-abs($x1);
	    $dif_y = $y2-$y1;

	    if ($dif_x < 0 and $dif_y >0 ){
	        //echo "I";
	        $x3=$x2;
	        $y3=$y1;
	        $cat_op  = distancia($y2,$x2,$y3,$x3);
	        $hip = distancia($y1,$x1,$y2,$x2);
	        $sen = $cat_op/$hip;
	        $angle = rad2deg(asin($sen));
	        $angle = $angle + 0;
	    }elseif ($dif_x > 0 and $dif_y >0){
	        //echo "II";
	        $x3=$x1;
	        $y3=$y2;

	        $cat_op  = distancia($y2,$x2,$y3,$x3);
	        $hip = distancia($y1,$x1,$y2,$x2);
	        $sen = $cat_op/$hip;
	        $angle = rad2deg(asin($sen));
	        $angle = $angle + 90;
	    }elseif ($dif_x > 0 and $dif_y <0 ){
	        $x3=$x2;
	        $y3=$y1;

	        $cat_op  = distancia($y2,$x2,$y3,$x3);
	        $hip = distancia($y1,$x1,$y2,$x2);
	        $sen = $cat_op/$hip;
	        $angle = rad2deg(asin($sen));
	        $angle = $angle + 180;
	       // echo "III";
	    }elseif ($dif_x<0 and $dif_y < 0){
	        $x3=$x1;
	        $y3=$y2;
	        $cat_op  = distancia($y2,$x2,$y3,$x3);
	        $hip = distancia($y1,$x1,$y2,$x2);
	        $sen = $cat_op/$hip;
	        $angle = rad2deg(asin($sen));
	        $angle = $angle + 270;
	       // echo "IV";
	    }

	    if ($dif_x < 0 and $dif_y == 0 ){
	        $angle =0;
	    }elseif ($dif_x == 0 and $dif_y > 0 ){
	        $angle =90;
	    }elseif ($dif_x > 0 and $dif_y == 0 ){
	        $angle =0;
	    }elseif ($dif_x == 0 and $dif_y < 0 ){
	        $angle =90;
	    }

	    if ($dif_x < 0 and $dif_y == 0 and $angle == 0){
	        $angle =0;
	      //  echo "ESTE";
	    }elseif ($dif_x == 0 and $dif_y > 0 and $angle == 90){
	        $angle = $angle + 180;
	       // echo "NORTE";
	    }elseif ($dif_x == 0 and $dif_y < 0 and $angle == 90){
	        $angle = $angle + 180;
	      //  echo "SUR";
	    }elseif($dif_x > 0 and $dif_y == 0 and $angle == 0){
	        $angle = $angle + 180;
	        //echo "OESTE";
	    }

	    $i_dir = ceil($angle/22.5);
	    if ($i_dir==16 ) $i_dir = 15;

	    return $i_dir;
	}

?>