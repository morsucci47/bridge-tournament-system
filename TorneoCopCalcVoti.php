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
//$EST= 1;
$N_COPPIA= 0;
$PUNTI= 1;
$PERC= 2;
$VOTI= 3;

	 
//------------------------------------------------------
/*
comincia il calcolo del voto per ogni giocatore
per tornei di tipo howell o mitchell.
solo al termine del torneo le mani sono state giocate da tutte le coppie
quindi ha senso calcolare le percentuali
*/
//------------------------------------------------------


	    for($Board=1; $Board<=$Nboards ; $Board++)    {
			// inizializzare RisMAni senza board	
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


    		//  assegna i voti alle coppie per il board attuale
//###########################################################################

  			   // CALCOLA LE MEDIE DEI PUNTEGGI PER ORIENTAMENTO E la MEDIA MINIMA
    
            $Media[$NS]=$Media[$EW]= 0;
			$i= 0;
            for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
                if($RisMani[$PUNTIMANI][$iCoppia] != -1 )  {
					if($RisMani[$PUNTIMANI][$iCoppia] < 10000) {
						$iOrient=$RisMani[$ORIENT][$iCoppia] ;
						$tempScore[$iCoppia]= $RisMani[$PUNTIMANI][$iCoppia] ;
						$Tag[$iCoppia]= $iOrient;
						$Media[$iOrient] += $RisMani[$PUNTIMANI][$iCoppia];
						$i++;
					}else{  //  ASSEGNA A TEMPSCORE IL PUNTEGGIO ARBITRALE
						$iOrient=$RisMani[$ORIENT][$iCoppia] ;
						$temp_N= intval(($RisMani[$PUNTIMANI][$iCoppia]-10000)/100);
						if($iOrient == $NS)  $tempScore[$iCoppia]=  $temp_N;
						if($iOrient == $EW)  $tempScore[$iCoppia]=  $RisMani[$PUNTIMANI][$iCoppia]-10000-$temp_N*100;
						$Tag[$iCoppia]= -1;		// QUESTO DISTINGUE IL 	PUNTEGGIO ARBITRALE			
					}
                }else{
                    $tempScore[$iCoppia]= 0 ;
                    $Tag[$iCoppia]= -1;
                }
            }
            $NcoppieAttive= $i;
            $i/=2;
            if($i==0) return;
            $Media[0] /= $i;
            $Media[1] /= $i;
            ($Media[0]<$Media[1]? $OrientMinimo=0:$OrientMinimo=1);  // orientamento della media minore
/*
echo"-------------media1 e media2= ".$Media[0]."   ".$Media[1] ;			
echo "<br>" ;					
echo"------orientmin= ".$OrientMinimo ;			
echo "<br>" ;					

if( $Board==2) {
echo"--score= ";			
echo "<br>" ;					
for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
echo $tempScore[$iCoppia]."  " ;			
}
echo "<br>" ;					
}
*/			
				// PORTA ALLO STESSO VALORE MEDIO

            for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
                if($Tag[$iCoppia]==$OrientMinimo)  {
                    $tempScore[$iCoppia] += -$Media[$OrientMinimo]+$Media[1-$OrientMinimo];
                }
            }
			
/*
if( $Board==2) {
echo"--score= ";			
echo "<br>" ;					
for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
echo $tempScore[$iCoppia]."  " ;			
}
echo "<br>" ;					
}
*/			
			    // TROVA IL MINIMO E PORTA TUTTO POSITIVO CON MINIMO A ZERO

            $Minimo=10000;
            $Massimo=-10000;
            for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
                if($Tag[$iCoppia]!=-1) {
                    if($tempScore[$iCoppia] < $Minimo) {
                        $Minimo= $tempScore[$iCoppia];
                    }
                    if($tempScore[$iCoppia] > $Massimo) {
                        $Massimo= $tempScore[$iCoppia];
                    }
                }
            }
			
			   // GESTISCE L'ECCEZIONE DEI PUNTRGGI TUTTI UGUALI

            if($Minimo==$Massimo) $Minimo = -1;    //  /=2;
               //  sottrae  il minimo --> minimo=0 salvo eccezione  e calcola la somma
            $temp= 0;
            for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
                if($Tag[$iCoppia] != -1) {
                    $tempScore[$iCoppia] -= $Minimo;
                    $temp += $tempScore[$iCoppia] ;
                }
            }
//echo "<br>" ;				
//if($Turno==1 && $Board==1) {
//for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
//echo"-------------score= ".$tempScore[$iCoppia] ;			
//echo "<br>" ;					
//}
//}
//echo"--------coppie attive= ".$NcoppieAttive ;
//echo "<br>" ;				
		
			 	// NORMALIZZA  (SOMMA =  100* NumTavoli

            if($temp>0) {
                for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
                    if($Tag[$iCoppia] != -1) {
                        $Voti[$iCoppia]= $tempScore[$iCoppia] * 50*$NcoppieAttive/$temp;
                    }else{
						if($tempScore[$iCoppia]==0) {
							$Voti[$iCoppia]= -999 ;
						}else{
							$Voti[$iCoppia]= $tempScore[$iCoppia] ;  //ASSEGNA IL PUNTEGGIO ARBITRALE
						}
                        
                    }
                }
            }
/*
if( $Board==2) {
echo"--voti= ";			
echo "<br>" ;					
for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
echo $Voti[$iCoppia]."  " ;			
}
echo "<br>" ;					
}
*/
/*			
echo "<br>" ;				
if($Turno==1 && $Board==1) {
for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
echo"---gioA gioB= ".$GiocatoreA[$iCoppia]."  ".$GiocatoreB[$iCoppia] ;			
echo"-----BOARD---score= ".$Board."  ".$Voti[$iCoppia] ;			
echo "<br>" ;					
}
}
echo "<br>" ;				
if($Turno==1 && $Board==2) {
for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
echo"---gioA gioB= ".$GiocatoreA[$iCoppia]."  ".$GiocatoreB[$iCoppia] ;			
echo"-----BOARD---score= ".$Board."  ".$Voti[$iCoppia] ;			
echo "<br>" ;					
}
}
*/
			 	// GESTIONE DEL PUNTEGGIO ARBITRALE
			/*
            for($iCoppia=0; $iCoppia<$Ncoppie ; $iCoppia++)    {
              // GESTIONE DEL punteggio arbitrale
                if($RisMani[$PUNTIMANI][$iBoard][$iCoppia] == -1
                          && $PercMP[$iBoard][$iCoppia] !=  0) {
              
                    $Voti[$iBoard][$iCoppia]= $PercMP[$iBoard][$iCoppia] ;
                }
            }
			*/
					
            for($k=0; $k<$Ncoppie; $k++)  {
				$PercCop[$Board-1][$k]= $Voti[$k];		
				//$PercCop[$Board-1][$k]= $Voti[$k];		
				$PuntiCop[$Board-1][$k]= $RisMani[$PUNTIMANI][$k];
				//$PuntiCop[$Board-1][$k]= $RisMani[$PUNTIMANI][$k];	
					
					if($RisMani[$ORIENT][$k]==$NS) {
						$OrientCop[$Board-1][$k]= $NS;
					}else if($RisMani[$ORIENT][$k]==$EW){
						$OrientCop[$Board-1][$k]= $EW;
					}
					
           	}   // fine coppie 
        }       // fine dei board


	//  CALCOLA LE PERCENTUALI MEDIE DI TUTTE LE MANI PER OGNI COPPIA

    for($iCop=0; $iCop<$Ncoppie ; $iCop++)    {
        $temp=0;
		$NboardsGiocate= 0;
        //for($iTurno=0; $iTurno<$NumTurno ; $iTurno++)    {
        	for($iBoard=0; $iBoard<$Nboards ; $iBoard++)    {
            	if($PercCop[$iBoard][$iCop] != -999)  {
                     $temp += $PercCop[$iBoard][$iCop];
                	 $NboardsGiocate++;
                }
        	}
		//}
/*
echo"-------------NboardGiocate= ".$iCop."....".$NboardsGiocate ;			
echo "<br>" ;					
*/
		if($NboardsGiocate !=0 ) {
			$PercMedia[$iCop]= $temp/$NboardsGiocate;
		}else{
			$PercMedia[$iCop]= 0;
		}
			
	}	


 ?>


