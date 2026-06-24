
<?php 
// QUESTA PROCEDURA VIENE INCLUSA IN CLASSIFICA E IN DETTAGLI 
// E DI QUESTI SFRUTTA LA CONNESSIONE AL DATABASE


// DEFINIZIONI
$PUNTIMANI= 0;
$ORIENT= 1;
$N_COPPIA_NS= 0;
$N_COPPIA_EW= 1;
$NS= 0;
$EW= 1;
//$NORD= 0;
$EST= 1;
$N_COPPIA= 0;
$PUNTI= 1;
$PERC= 2;
$VOTI= 3;

	 
//------------------------------------------------------
/*
comincia il calcolo del VMP (ovvero DMP: Distributional MP) per ogni giocatore
per tornei di tipo howell o mitchell.
solo al termine del torneo le mani sono state giocate da tutte le coppie
quindi ha senso calcolare le percentuali
*/
//------------------------------------------------------
	
		$asseScore[0]=-2000;
   		for($k=1; $k<401; $k++)  {
		    $asseScore[$k]= $asseScore[$k-1]+10;
		}
	    for($Board=1; $Board<=$Nboards ; $Board++)    {
			// inizializza RisMAni e P e asseScore
        	for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
             	$RisMani[$PUNTIMANI][$iCoppia]= -1;
       		}

		    $sql= "SELECT * FROM brdg_cop_scores WHERE torneoID= $ID_torneo 
				  		  		 					  AND board=$Board ORDER BY ID";	               
   		    $dati = $connessione->query($sql); 
   		    $row = $dati->fetch_assoc();
			
			
   		    while($row)  {
   	   	   		$CoppiaNS= $row['coppiaNS'];
       			$CoppiaEW= $row['coppiaEW'];
			    $ScoreNS=  $row['score'];
			    if(is_null($ScoreNS) || $ScoreNS==-1 || $ScoreNS>10000) {
					$ScoreEW= $ScoreNS;			
				}else{
					$ScoreEW= -$ScoreNS;
				}
               	$RisMani[$PUNTIMANI][$CoppiaNS-1]= $ScoreNS;   	   			 	   
				$RisMani[$ORIENT][$CoppiaNS-1]= $NS;
               	$RisMani[$PUNTIMANI][$CoppiaEW-1]= $ScoreEW;   	   			 	   
				$RisMani[$ORIENT][$CoppiaEW-1]= $EW;

       			$row = $dati->fetch_assoc();
   			}	
			

    		for($iOrient=0; $iOrient<2 ; $iOrient++) {
			    $i= 0;
			    $ii= 0;
            	for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
                	//if($iOrient==$RisMani[$ORIENT][$iCoppia] && $RisMani[$PUNTIMANI][$iCoppia] != -1)  {   //mano  giocata
                	if($iOrient==$RisMani[$ORIENT][$iCoppia])  {   								 		 	 // tempScore può essere -1
                    	$tempScore[$i]= $RisMani[$PUNTIMANI][$iCoppia];
                    	$tempCoppie[$i]= $iCoppia;
						$Tag[$i]= 1;
						if($RisMani[$PUNTIMANI][$iCoppia] != -1 && $RisMani[$PUNTIMANI][$iCoppia] < 10000) $ii++;
						if($RisMani[$PUNTIMANI][$iCoppia] > 10000) {
							$temp_N= intval(($RisMani[$PUNTIMANI][$iCoppia]-10000)/100);
							if($iOrient == $NS)  $tempScore[$i]=  $temp_N;
							if($iOrient == $EW)  $tempScore[$i]=  $RisMani[$PUNTIMANI][$iCoppia]-10000-$temp_N*100;
							$Tag[$i]= -1;    // DISTINGUE IL RISULTATO ARBITRALE
						}
                    	$i++;
                	}
            	}
				$NcoppieBRD= $i;
				$NcoppieBRDvere= $ii;
/*				
if($Board==1 && $iOrient==0) {
echo " NcoppieBRDvere = ".$NcoppieBRDvere;
echo "<br>";
for($k=0; $k<$NcoppieBRD; $k++)  {
echo " tempscore = ".$tempScore[$k];
echo " tempCoppie = ".$tempCoppie[$k];
echo "<br>";
}	
}	
*/					

    			//  GENERA LA FUNZIONE densità DI PROB in funzione di DifScore
        		$P[0]= 0;
           		for($k=1; $k<401; $k++)  {
        		    $P[$k]= 0;
        		}
                for($k=0; $k<$NcoppieBRD-1; $k++)  {
               		for($kk=$k+1; $kk<$NcoppieBRD; $kk++)  {
    		      		if($Tag[$k]!=-1 && $Tag[$kk]!=-1) {
    						$DifScore= $tempScore[$k] - $tempScore[$kk];
    						$pos= $DifScore/10 + 200;
    						$P[$pos] += 1;
    						$pos= -$DifScore/10 + 200;
    						$P[$pos] += 1;
    					}
    				}		
               	}   // fine coppie 

				
/*
echo"NcoppieBRD= ".$NcoppieBRD;			
echo "<br>" ;					
echo"BOARD= ".$Board;			
echo "<br>" ;					
echo"tempScore[$i]= ".$tempScore[$i];			
echo "<br>" ;					
					
echo"coppia, gioc, punti---> ".$tempCoppie[$i]." - ".$GiocatoreA[$tempCoppie[$i]]." - ".$GiocatoreB[$tempCoppie[$i]]." - ".$tempPunti[$i] ;
echo "<br>" ;
*/
      	 	 	 // CALCOLA L' INTEGRALE


          		for($k=1; $k<401; $k++)  {
      			 	$P[$k] += $P[$k-1];
      			}
                  //  NORMALIZZA
      			$Pmax=	$P[400]	;
      			for($k=0; $k<401; $k++)  {
      				$P[$k] /= $Pmax;
	 	        }



/*			
for($k=1; $k<401 ; $k++)    {
if($Board<5 && $iOrient==0 && $P[$k]!=0 && $P[$k]!=1 ) {				
	echo $asseScore[$k]."   ,  ".$P[$k].";";
	echo "<br>";
}	
}
	echo "<br>";
*/
//   -------------   GRAFICI  ----------------
/*
if($iOrient==0 && $Board==1  )    {

	 	 	//  REGISTRA NEL DATACASE
	$sql= "DELETE  FROM `brdg_xy` WHERE 1";
	$dati = $connessione->query($sql);
	for($k=0; $k<401; $k++)  {
		$yy= $P[$k];
	 	$xx=$asseScore[$k];

		$sql="INSERT INTO `brdg_xy` (`ID`, `x`, `y1`) VALUES (NULL,$xx,$yy)";
	    $dati= $connessione->query($sql);
 	}

			 //  FA IL GRAFICO

echo"<body>";
echo"<h1 style=\"color:#0000DD\">Grafici con phplot</h1>";
echo "<img src=\"https://bridge.altervista.org/Graph/simpleplot.php\" />";
echo "<br>";
echo"</body>";
}
*/

// --------------------------------------------------


/*
if($Turno==1 && $Board==1) {
for($k=0; $k<$Ncoppie; $k++)  {
echo " coppia = ".$k;
}
}						
*/


			// CALCOLA LA POSIZIONE STATISTICA per ogni coppia
  		
				for($k=0; $k<$NcoppieBRD; $k++)  {
				    if($Tag[$k]!=-1 && $tempScore[$k]!=-1) {	  
						$PercManoMedia= 0;	
						for($kk=0; $kk<$NcoppieBRD; $kk++)  {
							if($kk!=$k && $Tag[$kk]!=-1) {
								$DifScore= $tempScore[$k] - $tempScore[$kk];
								$pos= floor($DifScore/10) + 200; 
								$Perc1= $P[$pos]*100;				
								$Perc2= $P[$pos-1]*100;
								$PercM= ($Perc1+$Perc2)/2;		
								//$PercM= $Perc1;		
								$PercManoMedia += $PercM;							
							}
/*		
if($Board==1 && $k==0) {
echo " dif score = ".$DifScore;
echo " .... ";
echo "    Perc1 = ".$Perc1;

//echo "    Perc2 = ".$Perc2;

//echo "    PercM = ".$PercM;
echo "<br>";
}	
*/					
			
						}    
									
						$PercManoMedia /= ($NcoppieBRDvere-1);
						$PercCop[$Board-1][$tempCoppie[$k]]= $PercManoMedia;
						$PuntiCop[$Board-1][$tempCoppie[$k]]= $tempScore[$k];	
						$OrientCop[$Board-1][$tempCoppie[$k]]= $iOrient ;

						/*
						$OrientCop[$Board-1][$k]= -1;
						$OrientCop[$Board-1][$k]= -1;	
						*/
																
				    }else if($tempScore[$k]==-1){   //  RIPOSO
						$PercCop[$Board-1][$tempCoppie[$k]]= -1;
						$PuntiCop[$Board-1][$tempCoppie[$k]]= -1;			
						$OrientCop[$Board-1][$tempCoppie[$k]]= -1;				
				    }else{  						//  CASO ARBITRALE : TAG = -1
						$PercCop[$Board-1][$tempCoppie[$k]]= $tempScore[$k];
						$PuntiCop[$Board-1][$tempCoppie[$k]]= $RisMani[$PUNTIMANI][$tempCoppie[$k]];	
						$OrientCop[$Board-1][$tempCoppie[$k]]= $iOrient ;				
	echo"passa di qui";			  	  
				    }	//  fine if =-1	
				  
				} //fine coppie

            }   	//  fine orientamento
			 
        }       // fine dei board
			  
			  	
	//}
	

	 	 //CALCOLA LA MEDIA TOTALE
    for($iCop=0; $iCop<$Ncoppie ; $iCop++)    {
        $temp=0;
		$NboardsGiocate= 0;

        	for($iBoard=0; $iBoard<$Nboards ; $iBoard++)    {
				if($PuntiCop[$iBoard][$iCop] != -1)  {		   			
                	$temp += $PercCop[$iBoard][$iCop];
                	$NboardsGiocate++;
				}
        	}
	
	  
		if($NboardsGiocate !=0 ) {
			$PercMedia[$iCop]= $temp/$NboardsGiocate;
		}else{
			$PercMedia[$iCop]= 0;
		}
	  
	  
/*
echo"DifScoreMedia= ".$DifScoreMedia ;			
echo "<br>" ;					
echo"pos= ".$pos ;			
echo "<br>" ;					
echo"valore= ".$asseScore[$pos] ;			
echo "<br>" ;					
echo"prob= ".$P[$Turno-1][$Board-1][$pos] ;			
echo "<br>" ;					
echo"Giocatore1= ".$Giocatore1[$Turno-1][$k] ;			
echo "<br>" ;					
echo"Giocatore2= ".$Giocatore2[$Turno-1][$k] ;			
echo "<br>" ;					
echo "<br>" ;					
*/
	}		   
		   


 ?>


