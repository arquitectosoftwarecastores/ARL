<?php

  if (strpos($strSQL, "UNION"))
	{
	  $query_count = str_replace(" UNION ", ") + (", $strSQL);
	  $query_count = str_replace("*", "count(*) AS allRecs", $query_count);
	  $query_count = "SELECT (".$query_count.") AS allRecs";
	}
  else{
      $query_count = "SELECT count(*) AS allRecs ".substr($strSQL, strpos($strSQL, "FROM"), strlen($strSQL));
  }

  if (strpos($strSQL_2, "UNION"))
  {
    $query_count2 = str_replace(" UNION ", ") + (", $strSQL_2);
    $query_count2 = str_replace("*", "count(*) AS allRecs", $query_count2);
    $query_count2 = "SELECT (".$query_count2.") AS allRecs";
  }
else{
    $query_count2 = "SELECT count(*) AS allRecs ".substr($strSQL_2, strpos($strSQL_2, "FROM"), strlen($strSQL_2));
}

  /* ******** ******** ******** ******** ******** ******** ******** ******** ******** */
  if (isset($_GET['inicia']))
	  $href_inicia = substr($_SERVER['QUERY_STRING'], 0, strpos($_SERVER['QUERY_STRING'], '&inicia='));
  else
	  $href_inicia = $_SERVER['QUERY_STRING'];
  /* ******** ******** ******** ******** ******** ******** ******** ******** ******** */

  $query_count=$strSQL;
  $contador=0;
  $query1 = $conn->prepare($strSQL);
  $query1->execute();  
  while($row_result1 = $query1->fetch())
    $contador++;
  $allRecs=$contador;

  

  $query_count2=$strSQL_2;
  $contador2=0;
  $query2 = $conn->prepare($strSQL_2);
  $query2->execute();  
  while($row_result2 = $query2->fetch())
    $contador2++;

    if($contador > $contador2){
        $dif = $contador - $contador2;
        $allRecs=$contador + $dif ;
    }
    if($contador < $contador2){
        $dif = $contador2 - $contador;
        $allRecs=$contador2 + $dif ;
    }
    if($contador == $contador2){
        
        $allRecs=$contador;
    }
    
    $inicia = 0;										// Initialize star record value
    if (isset($_GET['inicia']))				// Recover start record value
        $inicia = $_GET['inicia'];
    
    $lastRec = $inicia+$rxp;						// Define last record value
    if ($lastRec > $allRecs)								// Compare last record value
        $lastRec = $allRecs;								// Assign last record value
    
    $currentPage = ceil($inicia/$rxp);				// Define current page the value
    $lastPage = ceil($allRecs/$rxp);					// Get and Assign last page value
    
    //echo "<br>Registro(s): ".($inicia+1)." - ".$lastRec." de ".$allRecs;
    //echo "<br>Pagina: ".($currentPage+1)." de ".$lastPage;
    

  /* ******** ******** ******** ******** ******** ******** ******** ******** ******** */
  
  $pagsXscr = 25;										// Pages per screen
  $allPags = $lastPage;									// Get and assign all pages value
  
  $startPage = 0;										// Initialize star page value
  if ($currentPage+1 > $pagsXscr)						// Recover start record value
      $startPage = intval($currentPage/$pagsXscr)*$pagsXscr;
  
  $lastPage = $startPage+$pagsXscr;						// Define last page value
  if ($lastPage > $allPags)								// Compare last page value
	  $lastPage = $allPags;								// Assign last page value
  
  $currentScr = ceil($startPage/$pagsXscr);				// Define current screen the value
  $lastScr = ceil($allPags/$pagsXscr);					// Get and Assign last page value
  
  //echo "<br>Pagina(s): ".($startPage+1)." - ".($lastPage)." de ".$allPags;
  //echo "<br>Pantalla: ".($currentScr+1)." de ".$lastScr;
?>