
<?php  

include "dbConnessione.php";

	  $Punt=0;	// serve nei files inclusi
      $azione = $_POST['action'];
      if($azione=="MP") {
	     $Punt= 1;
      }
      if($azione=="POM") {
	     $Punt= 2;
      }   
      if($azione=="IMP") {
	     $Punt= 3;
      }   
      if($azione=="DMP") {
	     $Punt= 4;
      }

	$ZonaNS= [false,true,false,true,true,false,true,false,false,true,false,true,true,false,true,false];
	$ZonaEW= [false,false,true,true,false,true,true,false,true,true,false,false,true,false,false,true];
	     
//  RICEVE TORNEO E IL PARAMETRO TURNO IN ESAME
	$NomeTorneo= $_GET['torneo'];
	$NumTurno= $_GET['turno'];
	$Origine= $_GET['orig'];

	if (!is_numeric($NumTurno)) {
	    exit("Manca il numero del turno");
	}	
	
//  CONTROLLA LA PRESENZA DEL TORNEO INSERITO NELLA TABELLA brdg_cop_tornei
	//  E RICAVA IL SUO ID
	$sql= "SELECT * FROM `brdg_cop_tornei` WHERE `NomeTorneo`='".$NomeTorneo."'";
//echo "  sql ----->".$sql;
//echo "<br>";
	$dati = $connessione->query($sql);
	$row = $dati->fetch_assoc(); 
	$ID_torneo= $row['ID_torneo'];
	$NboardsXturno= $row['BoardsXturno'];	
	$Punteggio= $row['Punteggio'];
	$Nturni= $row['Turni'];
	$Tipo= $row['Tipo'];
    if (!is_numeric($ID_torneo)) {
		// chiusura della connessione
		$connessione->close();
		exit("<body bgcolor=\"#a0eea0\"><b><center>Il torneo \"". $torneo."\" non esiste</center></b></body>");
	 }
	// -------- LEGGE IL NUMERO DELLE COPPIE ----------
	$sql="SELECT COUNT(*) FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo;
	$dati= $connessione->query($sql); 
	$row = $dati->fetch_array();
	$Ncoppie= $row[0];

	 //$Nboards= 4;
	 // Calcola tutto CalcClassifica
	 //$NcoppieBRD= $Ncoppie/2;
     //$MPteorici= ($Ncoppie/2-1)*2;
	 //$Nboards= $NboardsXturno * $Nturni;
	 
/*	 
echo "  Ncoppie ----->".$Ncoppie;
echo "<br>";
echo "  MPteorici -->".$MPteorici;
echo "<br>";
*/
//***************************************************************************************
	// ##############################################
	// MOSTRA LA LISTA DEGLI ISCRITTI 
	// ##############################################
	
	$Niscritti= $Ncoppie;
	
echo "
<body bgcolor=#55ee55>


<table width=\"100\" align=\"center\" border=\"1\">
	<tr align=\"center\">
	<td bgcolor=#f0e090> 
		<br>
		<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"450\">
			<tr>
			<td colspan=\"2\" align=\"center\"><b> ELENCO DEI PARTECIPANTI </b></td>
			</tr>
			<tr>
			<td align=\"center\"><b> Torneo:  $NomeTorneo</b></td>
			<td align=\"center\"><b>Coppie iscritte:  $Niscritti</b></td>
			</tr>
		</table>
		
		<br>
		<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"460\">
			<tr bgcolor= \"#FF9933\" >
			<!-- <td width=\"4%\" align=\"center\"><strong>N.</strong></td> -->
			<td width=\"12%\" align=\"center\"><strong>Copp.n.</strong></td>
			<td width=\"44%\" align=\"center\"><strong>Nome 1</strong></td>
			<td width=\"44%\" align=\"center\"><strong>Nome 2</strong></td>
			</tr>
";

				$sql="SELECT * FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo." ORDER BY `coppiaID`" ;			   
			//echo "sql=  ".$sql;			   
			//echo "<br>";
			$dati= $connessione->query($sql); 
			$row = $dati->fetch_assoc();		
			echo"<table style=\"HEIGHT: 10px\" width=\"460\" align=\"center\" border=\"1\>";
			$k=0;
			while($row) {
				$k++;
				$coppiaID= $row['coppiaID'];
				//  CASO DI TORNEO MITCHELL
				$coppiaID_= $coppiaID;		
				if($coppiaID > $Niscritti/2 && $Tipo==2) $coppiaID_= $coppiaID-$Niscritti/2 + 100;
				
				$dati1= $connessione->query("SELECT * FROM `brdg_cop_coppie` WHERE `coppiaID`=".$coppiaID." and `torneoID`=".$ID_torneo); 
				$row1 = $dati1->fetch_assoc();
				$nome1ID= $row1['nome1ID'];
				$nome2ID= $row1['nome2ID'];
			
				$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE `ID`=".$nome1ID); 
				$row1 = $dati1->fetch_assoc();
				$Nome1= $row1['nome'];
				
				$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE `ID`=".$nome2ID); 
				$row1 = $dati1->fetch_assoc();
				$Nome2= $row1['nome'];

				$row = $dati->fetch_assoc();
				echo"<tr align=\"center\">";
				echo"<td width=\"12%\" align=\"center\"><b>".$coppiaID_."</b></td>";
				echo"<td width=\"44%\" align=\"center\"><b>".$Nome1."</b></td>";
				echo"<td width=\"44%\" align=\"center\"><b>".$Nome2."</b></td>";
				echo"</tr>";
			}	
	echo"
		</table>"	
	;
	
echo "</td>";
echo "</tr>";
echo "
</table>"
; 

 
echo "<br>";
echo "<br>";

//***************************************************************************************

//  CONTROLLA LA COMPLETEZZA DEI RISULTATI 
	
	$sql="SELECT * FROM `brdg_cop_scores` WHERE  torneoID= ".$ID_torneo."  AND score IS NULL" ;
	$dati= $connessione->query($sql); 
	$row = $dati->fetch_assoc();
	if($row) {
		$completa= false;
	}else{
		$completa= true;
	}	
	
	// ##############################################
	// Mostra le tabelle
	
	include "TorneoCopCalcClassifica.php";

	
	//************************************************
	//  MOSTRA I PUNTI PER OGNI MANO E PER  COPPIA

//echo"<body bgcolor=#55ee55>";

	if($completa==false) {
  	    echo "<center><b><font color=\"#FF0000\">I RISULTATI NON SONO COMPLETI</font></b></center>";
		echo "<br>";
	}
//************************************************************************
//*************        
//  ************************************************************
echo"
<table width=\"100\" align=\"center\" border=\"1\">
<tr align=\"center\">
<td bgcolor=#f0e090> 
	
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"350\">
	 <tr>
	 
      <td style=\"background-color:LightSkyBlue;\" align=\"center\"><b>NS</b></td>
      <td align=\"center\"><b> RISULTATI DEL TORNEO: </b>$NomeTorneo</td>
      <td align=\"center\"><b>Turno </b>$NumTurno</td>
      <td style=\"background-color:LightPink;\" align=\"center\"><b>EW</b></td>
     
	 </tr>
    </table>

	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"300\">
	 <tr>
       
       <td align=\"center\"><strong>Board</strong></td>";
	   for($Cop=1; $Cop<=$Ncoppie ; $Cop++) {
    		if($Cop > $Ncoppie/2 && $Tipo==2) {
				$coppia= $Cop-$Ncoppie/2 + 100;
       			echo"<td align=\"center\"><strong>Cp:".$coppia."</strong></td>";
			}else{
       			echo"<td align=\"center\"><strong>Copp".$Cop."</strong></td>";
			}
	   }			
    echo"</tr>";
	
//echo"Nord ------>".$NORD;
//echo"<br>";	
//echo"Est ------>".$EST;
//echo"<br>";	

    //for($iTurno=1; $iTurno<=$NumTurno ; $iTurno++)    {		
        for($iBoard=1; $iBoard<=$Nboards ; $iBoard++)    {
    		echo"<tr>
			    
       			<td align=\"center\">".$iBoard."</td>
			";
/*	
for($k=1; $k<=$Ncoppie ; $k++)  {	
//echo"orient-cop $k --->".$OrientCop[0][$k-1];
echo $OrientCop[$iBoard-1][$k-1]." , ";
}
echo"<br>";
*/	
	
			for($iCop=0; $iCop<$Ncoppie ; $iCop++)    {
				$temp= $PuntiCop[$iBoard-1][$iCop];
            	if($temp != -1 && $temp != 1 && !is_null($temp))  {
					if($OrientCop[$iBoard-1][$iCop] == 0) {
						echo"<td style=\"background-color:LightSkyBlue;\" align=\"center\">".$temp."</td>";  
					}else if($OrientCop[$iBoard-1][$iCop] == 1){
						echo"<td style=\"background-color:LightPink;\" align=\"center\">".$temp."</td>";    
					}else{					
			    		echo"<td align=\"center\">".$PuntiCop[$iBoard-1][$iCop]."</td>";
					}
                }else{
			    	echo"<td align=\"center\">---</td>";
                }
        	}
			echo"</tr>";
		}
	//}	
	echo "</table>
	
</td> 
</tr>
</table>";
//*************************************************** 
//*********************************************
	
echo "<br>";
echo "<br>";
echo "<br>";
		
	//************************************************
	//  MOSTRA LE PERC. PER OGNI MANO E PER  COPPIA

echo"
<table width=\"100\" align=\"center\" border=\"1\">
<tr align=\"center\">
<td bgcolor=#f0e090> 
	
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"300\">
	 <tr>";
      //if($Punteggio==3) {echo"<td style=\"background-color:LightSkyBlue;\" align=\"center\"><b>NS</b></td>";}
	   if($Punteggio==1)  {
           echo"<td align=\"center\"><strong>PERCENTUALI DEL TORNEO: $NomeTorneo</strong></td>";
	   }else if($Punteggio==2)  {
           echo"<td align=\"center\"><strong>VOTI (POM) DEL TORNEO: $NomeTorneo</strong></td>";
	   }else if($Punteggio==3)  {
           echo"<td align=\"center\"><strong>IMP DEL TORNEO: $NomeTorneo</strong></td>";
	   }else if($Punteggio==4)  {
           echo"<td align=\"center\"><strong>DMP DEL TORNEO: $NomeTorneo</strong></td>";
	   }
	  echo"<td align=\"center\"><b>Turno </b>$NumTurno</td>";
	  //if($Punteggio==3) {echo"<td style=\"background-color:LightPink;\" align=\"center\"><b>EW</b></td>";}
	  echo"
     </tr>
    </table>

	<table align=\"center\" border=\"1\" cellspacing=\"1\">
	 <tr>
 
       <td align=\"center\"><strong>Board</strong></td>";
	   for($Cop=1; $Cop<=$Ncoppie ; $Cop++) {
    		if($Cop > $Ncoppie/2 && $Tipo==2) {
				$coppia= $Cop-$Ncoppie/2 + 100;
       			echo"<td align=\"center\"><strong>Cp:".$coppia."</strong></td>";
			}else{
       			echo"<td align=\"center\"><strong>Copp".$Cop."</strong></td>";
			}
	   }			
    echo"</tr>";

	
	if($Punteggio==3) {
   		$Precis= 2;
	}else if($Punteggio==4){
   	   	$Precis= 2;				
	}else{
   	   	$Precis= 0;				
	}					
        for($iBoard=1; $iBoard<=$Nboards ; $iBoard++)    {
    		echo"<tr>
			    
       			<td align=\"center\">".$iBoard."</td>
			";
	
			for($iCop=0; $iCop<$Ncoppie ; $iCop++)    {

				$temp= $PuntiCop[$iBoard-1][$iCop];
            	if($temp != -1 && $temp != 1 && !is_null($temp))  {
            	//if(!is_null($temp))  {  // ci sono sempre valori di MP
		
				  $temp= round($PercCop[$iBoard-1][$iCop],$Precis);						
				  if($Punteggio!=3)  {
				  
					if($temp == 0) {
						echo"<td style=\"background-color:#FF0000;\" align=\"center\">$temp</td>"; 
					}else if($temp>0 && $temp<25){
						echo"<td style=\"background-color:#FF6464;\" align=\"center\">$temp</td>"; 
					}else if($temp>=25 && $temp<50){
						echo"<td style=\"background-color:#FFC8C8;\" align=\"center\">$temp</td>"; 
					}else if($temp==50){
						echo"<td style=\"background-color:#FFFF00;\" align=\"center\">$temp</td>"; 
					}else if($temp>=50 && $temp<75){
						echo"<td style=\"background-color:#C8FFC8;\" align=\"center\">$temp</td>"; 
					}else if($temp>=75 && $temp<100){
						echo"<td style=\"background-color:#96FF96;\" align=\"center\">$temp</td>"; 
					}else if($temp==100){
						echo"<td style=\"background-color:#00FF00;\" align=\"center\">$temp</td>"; 
					}else{					
			    		echo"<td align=\"center\">$temp</td>";
					}

				  }else{	
										
					if($temp<-10) {
						echo"<td style=\"background-color:#FF0000;\" align=\"center\">$temp</td>"; 
					}else if($temp>=-10 && $temp<-5){
						echo"<td style=\"background-color:#FF6464;\" align=\"center\">$temp</td>"; 
					}else if($temp>=-5 && $temp<=-1){
						echo"<td style=\"background-color:#FFC8C8;\" align=\"center\">$temp</td>"; 
					}else if($temp>-1 && $temp<1){
						echo"<td style=\"background-color:#FFFF00;\" align=\"center\">$temp</td>"; 
					}else if($temp>=1 && $temp<=5){
						echo"<td style=\"background-color:#C8FFC8;\" align=\"center\">$temp</td>"; 
					}else if($temp>5 && $temp<=10){
						echo"<td style=\"background-color:#96FF96;\" align=\"center\">$temp</td>"; 
					}else if($temp>10){
						echo"<td style=\"background-color:#00FF00;\" align=\"center\">$temp</td>"; 
					}else{					
			    		echo"<td align=\"center\">$temp</td>";
					}


/*
					if($OrientCop[$iBoard-1][$iCop] == 0) {
						echo"<td style=\"background-color:LightSkyBlue;\" align=\"center\">$temp</td>";  
					}else if($OrientCop[$iBoard-1][$iCop] == 1){
						echo"<td style=\"background-color:LightPink;\" align=\"center\">$temp</td>";    
					}else{					
			    		echo"<td align=\"center\">$temp</td>";
					}
*/
				  }
									
                }else{
			    	echo"<td align=\"center\">---</td>";
                }			
        	}
		}
echo "</table>

</td> 
</tr>
</table>";
echo "<br>";
echo "<br>";
echo "<br>";

//   RICAVA LA MATRICE DEI CONTRATTI
//*************************************
    for($iBoard=1; $iBoard<=$Nboards ; $iBoard++)    {
   				  		
	        $sql= "SELECT * FROM brdg_cop_scores WHERE torneoID= $ID_torneo 
			  		  		 				 AND board=$iBoard ORDER BY ID";	               
  		    $dati = $connessione->query($sql); 
  		    $row = $dati->fetch_assoc();
	    
  		    while($row)  {
  	   	   		$CoppiaNS= $row['coppiaNS'];
      			$CoppiaEW= $row['coppiaEW'];
                $Contratto=  $row['Contratto'];
                $Prese=  $row['Prese'];
                $Da=  $row['GiocatoDa'];
                $Attacco=  $row['Attacco'];
         	    if($Prese==0) {
         			$Prese= "=";			
         		}else if($Prese>0){
         			$Prese= "+".$Prese;
         		}
				if($Da=="N" || $Da=="S")  {
             	    $Contrat[$iBoard-1][$CoppiaNS-1]= $Contratto.$Prese;   	   			 	   
             	    $Contrat[$iBoard-1][$CoppiaEW-1]= "vs.".$CoppiaNS;   	   			 	   
             	    $Attac[$iBoard-1][$CoppiaEW-1]= $Attacco;   	   			 	   
             	    $Attac[$iBoard-1][$CoppiaNS-1]= "vs.".$CoppiaEW;   	   			 	   
				}else{
					if($Tipo==2) {
						$CoppiaEW_= $CoppiaEW - $Ncoppie/2 + 100;
					}else{
						$CoppiaEW_= $CoppiaEW ;
					}				
             	    $Contrat[$iBoard-1][$CoppiaEW-1]= $Contratto.$Prese;   	   			 	   
             	    $Contrat[$iBoard-1][$CoppiaNS-1]= "vs.".$CoppiaEW_;   	   			 	   		
             	    $Attac[$iBoard-1][$CoppiaNS-1]= $Attacco;   	   			 	   			
             	    $Attac[$iBoard-1][$CoppiaEW-1]= "vs.".$CoppiaNS;   	   			 	   
				}
      			$row = $dati->fetch_assoc();
  			}	
	}	
	//************************************************
	//  MOSTRA I CONTRATTI PER OGNI MANO E PER  COPPIA

echo"
<table width=\"100\" align=\"center\" border=\"1\">
<tr align=\"center\">
<td bgcolor=#f0e090> 
	
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"300\">
	 <tr>
      <td style=\"background-color:LightSkyBlue;\" align=\"center\"><b>NS</b></td>
	  <td align=\"center\"><strong>CONTRATTI DEL TORNEO: $NomeTorneo</strong></td>
      <td align=\"center\"><b>Turno </b>$NumTurno</td>
      <td style=\"background-color:LightPink;\" align=\"center\"><b>EW</b></td>
     </tr>
    </table>
";
	echo"<table align=\"center\" border=\"1\" cellspacing=\"1\">";
	 echo"<tr>";
       echo"<td align=\"center\"><strong>Board</strong></td>";
	   for($Cop=1; $Cop<=$Ncoppie ; $Cop++) {
    		if($Cop > $Ncoppie/2 && $Tipo==2) {
				$coppia= $Cop-$Ncoppie/2 + 100;
       			echo"<td align=\"center\"><strong>Cp:".$coppia."</strong></td>";
			}else{
       			echo"<td align=\"center\"><strong>Copp".$Cop."</strong></td>";
			}
	   }			
       echo"</tr>";

       for($iBoard=1; $iBoard<=$Nboards ; $iBoard++)    {
   		echo"<tr>
  			  <td align=\"center\">".$iBoard."</td>";

		for($iCop=0; $iCop<$Ncoppie ; $iCop++)    {

			$temp= $PuntiCop[$iBoard-1][$iCop];
            if($temp != -1 && $temp != 1 && !is_null($temp))  {
           	//if($PuntiCop[$iBoard-1][$iCop] != -1 && $PuntiCop[$iBoard-1][$iCop] != 1)  {
									
				if($OrientCop[$iBoard-1][$iCop] == 0) {
					echo"<td style=\"background-color:LightSkyBlue;\" align=\"center\">".$Contrat[$iBoard-1][$iCop]."</td>"; 
				}else if($OrientCop[$iBoard-1][$iCop] == 1){
					echo"<td style=\"background-color:LightPink;\" align=\"center\">".$Contrat[$iBoard-1][$iCop]."</td>";   
				}else{					
		    		echo"<td align=\"center\">".$Contrat[$iBoard-1][$iCop]."</td>";
				}
											
            }else{
   		 	   echo"<td align=\"center\">---</td>";
            }			
       	}
	}
echo "
	</table>
</td> 
</tr>
</table>";
	//************************************************
	//  MOSTRA GLI ATTACCHI PER OGNI MANO E PER  COPPIA

echo"
<br>
<br>
<table width=\"100\" align=\"center\" border=\"1\">
<tr align=\"center\">
<td bgcolor=#f0e090> 
	
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"300\">
	 <tr>
      <td style=\"background-color:Yellow;\" align=\"center\"><b>None</b></td>
      <td style=\"background-color:Green;\" align=\"center\"><b>No Vuln</b></td>
	  <td align=\"center\"><strong>ATTACCHI DEL TORNEO: $NomeTorneo</strong></td>
      <td align=\"center\"><b>Turno </b>$NumTurno</td>
      <td style=\"background-color:Red;\" align=\"center\"><b>Vuln</b></td>
      <td style=\"background-color:Orange;\" align=\"center\"><b>All</b></td>
     </tr>
    </table>
";
	echo"<table align=\"center\" border=\"1\" cellspacing=\"1\">";
	 echo"<tr>";
       echo"<td align=\"center\"><strong>Board</strong></td>";
	   for($Cop=1; $Cop<=$Ncoppie ; $Cop++) {
    		if($Cop > $Ncoppie/2 && $Tipo==2) {
				$coppia= $Cop-$Ncoppie/2 + 100;
       			echo"<td align=\"center\"><strong>Cp:".$coppia."</strong></td>";
			}else{
       			echo"<td align=\"center\"><strong>Copp".$Cop."</strong></td>";
			}
	   }			
       echo"</tr>";

       for($iBoard=1; $iBoard<=$Nboards ; $iBoard++)    {
 		// -- CONTROLLA LA VULNERABILITA --
		
		$kBoard= ($iBoard-1)%16;
		$VulnNS= $ZonaNS[$kBoard];
		$VulnEW= $ZonaEW[$kBoard];

		if($VulnNS==false && $VulnEW==false) {
			$Vulnerability= "None";
		}else if($VulnNS==true && $VulnEW==true) {
			$Vulnerability= "All";			
		}else if($VulnNS==true && $VulnEW==false) {
			$Vulnerability= "NS";			
		}else if($VulnNS==false && $VulnEW==true) {
			$Vulnerability= "EW";			
		}
		//-------------------------------------	

  		echo"<tr>
  			  <td align=\"center\">".$iBoard."</td>";

		for($iCop=0; $iCop<$Ncoppie ; $iCop++)    {
			// COLORE DELLA VULNERABILITA
			$StileCol= "background-color:Yellow;";
			if($Vulnerability == "All") $StileCol= "background-color:Orange;";
			
			if($OrientCop[$iBoard-1][$iCop] == 0 && $Vulnerability == "NS") {  //NS
				$StileCol= "background-color:Red;";
			}
			if($OrientCop[$iBoard-1][$iCop] == 1 && $Vulnerability == "EW") {  //EW
				$StileCol= "background-color:Red;";
			}
			if($OrientCop[$iBoard-1][$iCop] == 0 && $Vulnerability == "EW") {  //NS
				$StileCol= "background-color:Green;";
			}
			if($OrientCop[$iBoard-1][$iCop] == 1 && $Vulnerability == "NS") {  //EW
				$StileCol= "background-color:Green;";
			}
					
			$temp= $PuntiCop[$iBoard-1][$iCop];
            if($temp != -1 && $temp != 1 && !is_null($temp))  {
           	//if($PuntiCop[$iBoard-1][$iCop] != -1 && $PuntiCop[$iBoard-1][$iCop] != 1)  {
				echo"<td style=$StileCol align=\"center\">".$Attac[$iBoard-1][$iCop]."</td>"; 
	/*								
				if($OrientCop[$iBoard-1][$iCop] == 0) {
					echo"<td style=\"background-color:LightSkyBlue;\" align=\"center\">".$Attac[$iBoard-1][$iCop]."</td>"; 
				}else if($OrientCop[$iBoard-1][$iCop] == 1){
					echo"<td style=\"background-color:LightPink;\" align=\"center\">".$Attac[$iBoard-1][$iCop]."</td>";   
				}else{					
		    		echo"<td align=\"center\">".$Attac[$iBoard-1][$iCop]."</td>";
				}
	*/										
            }else{
   		 	   echo"<td style=$StileCol align=\"center\">---</td>";
            }			
       	}
	}
echo "
	  </tr>
	</table>
";

	if($Origine=="admin") {
		// RICAVA LA PSWD
		$dati = $connessione->query("SELECT * FROM brdg_cop_pswd WHERE 1");
		$row = $dati->fetch_assoc(); 
		$PSWD= $row['password'];		
		//$PSWDcri=~$PSWD;
		
		//$URL= "TorneoCopAdmin.php?torneo=$NomeTorneo&password=$PSWDcri";
		//$AzioneBottone="\"window.location.href ='$URL'\"";	
		$AzioneBottone="\"document.forms[0].submit()\"";	
	}else{
		//$AzioneBottone="\"window.history.go(-1)\"";			
		$AzioneBottone="\"window.location.href ='TorneoCopScoresContrattoTav.php?NomeTorneo=$NomeTorneo&NumTurno=$NumTurno&tavolo=1&orig=risultati'";	
	}

//**********************************************************************************
// chiusura della connessione
$connessione->close();
         // <td ><input style="background-color:LightGray;" type="submit" value="RITORNA" name="action"></td>
?>

	<form action="TorneoCopAdmin.php?torneo=<?php echo $NomeTorneo ?>" method="POST">
		<input type="hidden" name="password" value="<?php echo $PSWD ?>"/>
	</form>
<?php

 
echo "<form action=\"./TorneoCopDettagli_1.php?torneo=".$NomeTorneo."&turno=".$NumTurno."\" method=\"post\">"; 
?>
    <table style="HEIGHT: 10px" width="10" align="center" border="1">
      <tbody>
        <tr align="center">
          <td ><input style="background-color:powderblue;" type="submit" value="MP" name="action"></td>
          <td>&nbsp;</td>
          <td ><input style="background-color:powderblue;" type="submit" value="IMP" name="action"></td>
          <td>&nbsp;</td>
          <td ><input style="background-color:powderblue;" type="submit" value="POM" name="action"></td>
          <td>&nbsp;</td>
          <td ><input style="background-color:powderblue;" type="submit" value="DMP" name="action"></td>
     
        </tr>
      </tbody>
    </table>
</form>	 

    <table style="HEIGHT: 10px" width="10" align="left" border="1">
      <tbody>
        <tr align="center">
          <td ><button style="background-color:LightGray;align:center; font-size: 36px;" onclick=<?php echo $AzioneBottone; ?>; return false;">Indietro</button></td>
        </tr>
      </tbody>
    </table>
	
</td> 
</tr>

</table> 
	
</body>




