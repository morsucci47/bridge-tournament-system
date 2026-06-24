<?php 

// Questa procedura è inclusa in CalcClassifica
// 

// DEFINIZIONI
$PUNTIMANI= 0;
$ORIENT= 1;
$N_COPPIA_NS= 0;
$N_COPPIA_EW= 1;
$NS= 0;
$EW= 1;
//$NORD= 0;
//$EST= 1;
$N_COPPIA= 0;
$PUNTI= 1;
$PERC= 2;
$VOTI= 3;

//if($Tipo==2){
	//$Nboards= $Ncoppie/2;
//}
/*
echo"ok calc perc";
echo"<br>";
echo "  Ncoppie ----->".$Ncoppie;
echo "<br>";
echo "  Nboards ----->".$Nboards;
echo "<br>";
echo "  NcoppieBRD ----->".$NcoppieBRD;
echo "<br>";
echo "  MPteorici -->".$MPteorici;
echo "<br>";
echo "  Punteggio -->".$Punteggio;
echo "<br>";

*/	 
//------------------------------------------------------
/*
comincia il calcolo delle percentuali per coppia
per tornei di tipo howell o mitchell.
solo al termine del torneo le mani sono state giocate da tutte le coppie
quindi ha senso calcolare le percentuali
*/
//------------------------------------------------------


    // trova il massimo numero di giocate per uno stesso board
    $MaxRisultatiBRD= $NcoppieBRD;
	if($Tipo==2) {
    	if($NcoppieBRD%2==0) {
	    	$MaxRisultatiBRD= $NcoppieBRD-1;
    	}
	}
	$MPmax= $MPteorici;
/*
echo "  Ncoppie ----->".$Ncoppie;
echo "<br>";
echo "  Nboards ----->".$Nboards;
echo "<br>";
echo "  NcoppieBRD ----->".$NcoppieBRD;
echo "<br>";
echo "  MaxCoppieBRD ----->".$MaxCoppieBRD;
echo "<br>";
*/

	for($Board=1; $Board<=$Nboards ; $Board++)    {
		// inizializzare RisMAni senza board	
		for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
			$RisMani[$PUNTIMANI][$iCoppia]= -1;
			$RisMani[$ORIENT][$iCoppia]= -1;
		}
						
		$sql= "SELECT * FROM brdg_cop_scores WHERE torneoID= $ID_torneo AND board=$Board ORDER BY ID";	               
		$dati = $connessione->query($sql); 
		$row = $dati->fetch_assoc();
		
		$NcoppieBRD=0;   		    
		$NrisultBRD=0; 
		//$MediaRisultati
		while($row)  {
			$CoppiaNS= $row['coppiaNS'];
			$CoppiaEW= $row['coppiaEW'];
			$Score=  $row['score'];
			if(is_null($Score)) {
				$ScoreEW= $Score;			
			}else{
				$ScoreEW= -$Score;
			}
			if($Score==1) $Score=-1;
			if($ScoreEW==1) $ScoreEW=-1;
			
			$RisMani[$PUNTIMANI][$CoppiaNS-1]= $Score;   	   			 	   
			$RisMani[$ORIENT][$CoppiaNS-1]= $NS;
			$RisMani[$PUNTIMANI][$CoppiaEW-1]= $ScoreEW;   	   			 	   
			$RisMani[$ORIENT][$CoppiaEW-1]= $EW;

			$row = $dati->fetch_assoc();
			$NcoppieBRD++;
			if(!is_null($Score) && $Score!=-1 && $Score!=0 && $Score>-10000 && $Score<10000) {
				$NrisultBRD++; 
			}
		}	

/*
if($Board==1||$Board==2)  {	
echo"NrisultBRD= ".$NrisultBRD;				
echo "<br>" ;
for($k=0; $k<$Ncoppie ; $k++) {
$temp= $k+1;						
echo"BOARD-coppia, score---> ".$Board." - ".$temp." - ".$RisMani[$PUNTIMANI][$k] ;
echo "<br>" ;
}
}
*/
	//  assegna i punti MP alle coppie per il turno-board attuale
		// prima calcola gli mp teorici
		for($iOrient=0; $iOrient<2 ; $iOrient++) {
			$i= 0;
			$Ngiocate= 0;
			for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
				if($iOrient==$RisMani[$ORIENT][$iCoppia])  {   			// tempScore può essere -1 !! 0 sta per all-pass
					$tempScore[$i]= $RisMani[$PUNTIMANI][$iCoppia];
					$tempCoppie[$i]= $iCoppia;
//   !!!!!!   ATTENZIONE    MODIFICA IMPORTANTE A ALL PASS
					//if(!is_null($tempScore[$i]) && $tempScore[$i]!=-1  && $tempScore[$i]!=0 && $tempScore[$i]>-10000 && $tempScore[$i]<10000) {
					if(!is_null($tempScore[$i]) && $tempScore[$i]!=-1   && $tempScore[$i]>-10000 && $tempScore[$i]<10000) {
						$Ngiocate++;
					}
					$i++;
				}
			}
			$MPteorici= ($Ngiocate-1)*2;
/*
echo"i= ".$i;				
echo "<br>" ;
echo"Ngiocate= ".$Ngiocate;				
echo "<br>" ;

if($Board==1||$Board==2)  {	
echo"NcoppieBRD= ".$NcoppieBRD;				
echo "<br>" ;
for($k=0; $k<$NcoppieBRD ; $k++) {
$temp= $tempCoppie[$k]+1;						
echo"BOARD, coppia, orient, score---> ".$Board." - ".$temp." - ".$iOrient." - ".$tempScore[$k] ;
echo "<br>" ;
}
echo "<br>" ;
}
*/
/*
echo"BOARD= ".$Board;			
echo "<br>" ;					
echo"tempScore[$i]= ".$tempScore[$i];			
echo "<br>" ;									
echo"coppia,orient, MP---> ".$tempCoppie[$i]." - ".$iOrient." - ".$tempPunti[$i] ;
echo "<br>" ;
*/
			// più uno per i punteggi uguali
			// più due per i punteggi minori		

			for($i=0; $i<$NcoppieBRD ; $i++)  {   // CONTROLLARE NcoppieBRD
				//  CASI PARTICOLARI 
				if($tempScore[$i] > 10000) {	   							// PERC ASSEGNATA DA ARBITRO
				//  > 10000   PUNTEGGIO ARBITRALE	: SI ASSEGNA LA PROPRIA PERC DEL TOP A CIASCUNA LINEA
					$tempPunti[$i]= $MPmax * intval(($tempScore[$i]-10000)/100)/100 ;					
				}else if($tempScore[$i] < -10000) {				 		// PERC ASSEGNATA DA ARBITRO
					$temp_N= intval((-$tempScore[$i]-10000)/100) ;			
					$tempPunti[$i]= $MPmax * (-$tempScore[$i]-10000-$temp_N*100)/100;	
				}else if($tempScore[$i]==-1 && $iOrient==0)  {
				//  = -1	   BYE 					
					//$tempPunti[$i]= $MPmax * 0.6 ;	: SI ASSEGNA 60% DEL TOP ALLA COPPIA NS		 Non usato : si USA LA MEDIA DEI RISULTATI	
					$tempPunti[$i]= -1 ;														
				}else if($tempScore[$i]==-1 && $iOrient==1)  {
				//  = -1	   BYE 					: SI ASSEGNA 0% al BYE
					$tempPunti[$i]= 0 ;							
//   !!!!!!   ATTENZIONE    MODIFICA IMPORTANTE A ALL PASS
				//}else if($tempScore[$i]==0)  {
				//  = 0		  ALL PASS				: SI ASSEGNA 50% DEL TOP A CHI è PASSATO. SE UNA SOLA COPPIA HA GIOCATO, SI ASSEGNA TOP O ZERO A SECONDA	
				//	$tempPunti[$i]= $MPmax * 0.5 ;							
				}else{
					$tempPunti[$i]=0;	
					for($k=0; $k<$NcoppieBRD ; $k++) {
						if($i != $k) {	
		//   !!!!!!   ATTENZIONE    MODIFICA IMPORTANTE A ALL PASS
							//if(!is_null($tempScore[$i])  && !is_null($tempScore[$k]) && $tempScore[$k] != -1 && $tempScore[$k]!=0 && $tempScore[$k]>-10000 && $tempScore[$k]<10000)  { 	
							if( $tempScore[$k] != -1 &&  $tempScore[$k]>-10000 && $tempScore[$k]<10000)  { 	
								if($tempScore[$i] == $tempScore[$k]) $tempPunti[$i]+=1; 
								if($tempScore[$i] >  $tempScore[$k]) $tempPunti[$i]+=2;
							}else{
								// non incrementa
							}
						}
					}
					
					//  AGGIUSTAMENTO DI NEUBERG
					$NEUBERG= true;
					//$NEUBERG= false;
					//$Ngiocate
					$Ngiocabili= $Ncoppie/2;
					if($NEUBERG) {
						if($MPteorici!=0) {
							//$tempPunti[$i]= $tempPunti[$i]*$MPmax/$MPteorici ;    
							//$tempPunti[$i]= ($tempPunti[$i]+1)*$MPmax/$MPteorici -1;  
							$tempPunti[$i]= ($tempPunti[$i]+1)*$Ngiocabili/$Ngiocate -1;  
						}else{
							if($tempScore[$i]>0) {
								$tempPunti[$i]= $MPmax;
							}else{
								$tempPunti[$i]= 0;							
							}
						}
					}  
					
				}
				
			}// fine coppie di un orientamento

/*
if($Board==1)  {	
echo"MPmax= ".$MPmax;				
echo "<br>" ;
echo"MPteorici= ".$MPteorici;				
echo "<br>" ;
echo"NcoppieBRD= ".$NcoppieBRD;				
echo "<br>" ;
for($k=0; $k<$NcoppieBRD ; $k++) {
$temp= $tempCoppie[$k]+1;						
echo"BOARD, coppia, orient, score, MP ---> ".$Board." - ".$temp." - ".$iOrient." - ".$tempScore[$k]." - ".$tempPunti[$k] ;
echo "<br>" ;
}
echo "<br>" ;
}

*/			

			// EVENTUALE AGGIUSTAMENTO DI NEUBERG
			//$NEUBERG= false;
/*		
			$NEUBERG= true;
			if($NEUBERG) {
				for($k=0; $k<$NcoppieBRD ; $k++)  {
					$tempScore[$tempCoppie[$k]]= ($tempScore[$tempCoppie[$k]]+1)* $MaxRisultatiBRD/$NcoppieBRD-1 ;
				}    
			}  

*/		
			// CALCOLA LE PERC
/*				
			for($k=0; $k<$Ncoppie/2 ; $k++)  {
				$PercCop[$Board-1][$k]= -1;		 		
			}
*/


			for($k=0; $k<$NcoppieBRD ; $k++)  {
				if($tempPunti[$k] != -1 && !is_null($tempPunti[$k])) {
					$PercCop[$Board-1][$tempCoppie[$k]]= $tempPunti[$k]*100.0/$MPmax;
				}else{
					$PercCop[$Board-1][$tempCoppie[$k]]= -1;					
				}	
				$MP_Cop[$Board-1][$tempCoppie[$k]]= $tempPunti[$k];	
									
				$PuntiCop[$Board-1][$tempCoppie[$k]]= $RisMani[$PUNTIMANI][$tempCoppie[$k]];
				$OrientCop[$Board-1][$tempCoppie[$k]]= $iOrient;
			}   // fine coppie di un orientamento
		}   	//  fine orientamento
	}       	// fine dei board

    // calcola le percentuali medie di tutte le mani per ogni coppia

    for($iCop=0; $iCop<$Ncoppie ; $iCop++)    {
        $temp=0;
		$NboardsGiocate= 0;
        for($iBoard=0; $iBoard<$Nboards ; $iBoard++)    {
/*
if($iCop==0) {
echo"PercCop[$iBoard][$iCop]= ".$PercCop[$iBoard][$iCop];			
echo "<br>" ;					
}		
*/
			$test= $PuntiCop[$iBoard][$iCop];
            if($test != -1 && $test != 1 && !is_null($test))  {
            //if($PercCop[$iBoard][$iCop] != -1 && !is_null($PercCop[$iBoard][$iCop]))  {
            	$temp += $PercCop[$iBoard][$iCop];
            	$MPtemp += $MP_Cop[$iBoard][$iCop];
            	$NboardsGiocate++;
			}
        }
/*
echo"NboardsGiocate= ".$NboardsGiocate;			
echo "<br>" ;	
*/				
		if($NboardsGiocate !=0 ) {
			$PercMedia[$iCop]= $temp/$NboardsGiocate;
			$MP_Media[$iCop]= $MPtemp/$NboardsGiocate;
		}else{
			$PercMedia[$iCop]= 0;
			$MP_Media[$iCop]= 0;
		}
	}	
	
//var_dump ($PercMedia);
//echo"<br>";	   
// chiusura della connessione
//$connessione->close();

 ?>


