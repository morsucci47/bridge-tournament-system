<?php 
/*
Questa procedura effettua gli abbinamenti per tornei a coppie
Le strutture dati su cui opera sono:

	turni[][] ----->  che contiene gli abbinamenti  e 
	che si crea leggendo il file txt relativo al num coppie 			  
				  
 il contenuto di turni[][] viene copiato in brdg_cop_turni
tenendo conto del numero di boards per turno

*/
  
 function strcompatta($Str) {
	$out= str_replace("\0", "", $Str);  
/*
 	$lung= strlen($Str);	
	$out=""; 
	for ($k=0; $k<$lung ;$k++) {
		if ($k%2==1)  {
		 	$out= $out.$Str[$k];
		}
	}
*/	
    return $out;
}

// inclusione del file di connessione
include "dbConnessione.php";


// Definisce le variaobili
$Niscritti= 0;
$turnoAttuale= 0;
 

//$turni=[[]];   // numero-incontro, GIOCATORI
//$turniPrec=[[]]; //turni precedenti

	
  //  CATTURA TORNEO DA ADMIN-->NuovoTurno
  //******************************************
  	//$azione = $_POST['action'];

    $NomeTorneo= $_GET['torneo'];
	$dati= $connessione->query("SELECT * FROM `brdg_cop_tornei` WHERE NomeTorneo=\"".$NomeTorneo."\""); 
	if(!$dati)  {
		exit( "<body bgcolor=\"#f0e090\"><b><center>Il torneo non esiste</center></b></body>");
    }
	$row = $dati->fetch_assoc();
	$ID_torneo= $row['ID_torneo'] ?? NULL;
	$TurnoAttuale= $row['TurnoAttuale'] ?? 0;		
	$Nturni= $row['Turni'] ?? 0;		
	$NboardsXturno= $row['BoardsXturno'] ?? 0;		
	$Punteggio= $row['Punteggio'] ?? 0;		
	$Tipo= $row['Tipo'] ?? 0;		
	$Ntavoli= $row['Tavoli'] ?? 0;	
/*	
echo"TurnoAttuale= ".$TurnoAttuale;
echo"<br>";	   
echo"Nturni= ".$Nturni;
echo"<br>";	   
echo"NboardsXturno= ".$NboardsXturno;
echo"<br>";	   
*/	
   	if (!is_numeric($ID_torneo)) {
		// chiusura della connessione
		$connessione->close();
		exit("<body bgcolor=\"#80ff80\"><b><center>Il torneo ". $NomeTorneo." non esiste</center></b></body>");
   	}
	// -------- LEGGE IL NUMERO DI ISCRITTI ----------
	//**********************************************
	$sql="SELECT COUNT(*) FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo;
	$dati= $connessione->query($sql); 
	$row = $dati->fetch_array();
	$Niscritti= $row[0] ?? NULL;
   	if ($Niscritti%2 !=0) {
		// AGGIUNGIAMO BYE1 E BYE2	
/*		
	   $Niscritti++;
	   $coppiaID= $Niscritti;
	   $sql="INSERT INTO `brdg_cop_coppie`(`ID`,`torneoID`,`coppiaID`,`nome1ID`,`nome2ID`) VALUES (NULL,".$ID_torneo.",".$coppiaID.",".$nome1ID.",".$nome2ID.")";
//echo "sql 6=  ".$sql;			   
//echo "<br>";
	   $dati= $connessione->query($sql);
	   if ($dati==NULL) {
	       exit ("Errore di inserimento in iscritti");
	   }
		
*/		
		
		$connessione->close();
		exit("<body bgcolor=\"#80ff80\"><b><center>N.ISCRITTI= \"". $Niscritti."\" NON E' UN NUMERO PARI</center></b></body>");
   	}
   	if ($turnoAttuale+1>=$Niscritti) {
		$connessione->close();
		exit("<body bgcolor=\"#80ff80\"><b><center>N. DI TURNI INCOMPATIBILE COL N. DI ISCRITTI</center></b></body>");
   	}

	// SE IL TURNO PRECEDENTE E' = 0 LEGGE IL FILE CON GLI ABBINAMENTI
	// E GENERA TUTTI I TURNI
	//**********************************************************************************
/*
$prova=2;
$prova1="3";
$prova2=(int)$prova1;
var_dump ($prova);
echo"<br>";	   
var_dump ($prova1);
echo"<br>";	   
var_dump ($prova2);
echo"<br>";	   
*/
	if ($TurnoAttuale == 0) {
	   //crea il nome del file e lo legge

	   $Ntavoli=  $Niscritti/2;
	   //TIPO 1 = HOWELL      TIPO 2 = MITCHELL
	   if($Tipo==1)  {
	   	  $NomeFile=sprintf("%d_%d.txt","$Ntavoli","$Nturni");
	   }else{
	   	  $NomeFile=sprintf("%d_%d_m.txt","$Ntavoli","$Nturni");
	   }
	   //$ptfile= fopen($NomeFile,'r');
	   // Percorso da cui prelevare il file
	   $path = "Template/";
	   // File completo di percorso
	   $file = $path . $NomeFile;
echo"file= ".$file;
echo"<br>";	   
	   // CONTROLLA se il file � leggibile
	   if ( ! is_readable( $file ) ) {
		  exit("Il file $file non � leggibile oppure non esiste!");
	   }
	   // LEGGE il contenuto del file
	   $righe = file( $file );
	   $Turno= 0;
	   $i=0;

//echo"primo car= ".$righe[0][0];
//echo"<br>";	
				
	   while(!is_numeric($righe[0][0]))  {	
 	        $righe[0]= substr($righe[0],1);  // toglie inizio file
   	   }
	   
	   foreach ( $righe as $riga ) {
	 	   	//$riga= substr($riga,0);
  			//$riga=	$righe[$i];
//echo"riga= ".$riga;
//echo"<br>";	
			
			$columns= explode(",",$riga);
			//sscanf($riga, "%i,%i,%i,%i",$columns[0],$columns[1],$columns[2],$columns[3]);

			$i++;
/*
var_dump ($columns[1]);
echo"<br>";	
*/			
			$Turno++;
		
			$Tavolo= strcompatta($columns[0]) ?? NULL;
		  	$Boa= strcompatta($columns[1]) ?? NULL;
		  	$CoppiaNS= strcompatta($columns[2]) ?? NULL;
		  	$CoppiaEW= strcompatta($columns[3]) ?? NULL;
		
/*	
echo"Tavolo= ".$Tavolo;
echo"  ,Boa= ".$Boa;
echo"  ,CoppiaNS= ".$CoppiaNS;
echo"  ,CoppiaEW= ".$CoppiaEW;
echo"<br>";	
		
var_dump ($Boa);
echo"<br>";	

var_dump ($columns[0]);
echo"<br>";	
var_dump ($columns[1]);
echo"<br>";	
var_dump ($columns[2]);
echo "--".strlen($columns[2]);
echo"<br>";	
var_dump ($columns[3]);
echo "--".strlen($columns[3]);
echo"<br>";	
*/		
			if($CoppiaEW==NULL) break;
			if($CoppiaEW > 100) $CoppiaEW	= $CoppiaEW - 100 + $Niscritti/2;
			
			$ini=($Boa-1)*$NboardsXturno+1 ;
			$fin=$Boa*$NboardsXturno ;
/*			
echo"<br>";	

echo"   ini= ".$ini;
echo"   fin= ".$fin;
echo"<br>";	
*/

	   	  	for($board=$ini ; $board<=$fin ; $board++) {
				
				//==================================================================================
				//  QUESTA SEZIONE VALE SOLO NEL CASO DI 3 TAVOLI GIOCATI CON 5 Turni
				//=================================================================================0
				if($NomeFile=="3_5.txt" && $Turno==5) {
					if ($NboardsXturno<3) {
					   $connessione->close();
					   exit("<body bgcolor=\"#80ff80\"><b><center>IL N. DI BOARDS PER TURNO DEVE ESSERE ALMENO 3</center></b></body>");
					}
					$boardTav= ($board + $Tavolo  -4*$NboardsXturno-2 ) % $NboardsXturno  + 4*$NboardsXturno+1  ;
					$sql= "INSERT INTO `brdg_cop_scores`(`id`, `torneoID`, `tavolo`, `turno`, `board`, `coppiaNS`, `coppiaEW`) 
										  VALUES (NULL,$ID_torneo,$Tavolo,$Turno,$boardTav,$CoppiaNS,$CoppiaEW)";		

					$dati= $connessione->query($sql); 

					if (!$dati) {
					   $connessione->close();
					   exit("<body bgcolor=\"#80ff80\"><b><center>ERRORE NELLA REGISTRAZIONE DEI TURNI</center></b></body>");
					}
				//==================================================================================
				//  FINE  SEZIONE  3 TAVOLI GIOCATI CON 5 Turni
				//=================================================================================0
					
				}else{ 				
				
					$sql= "INSERT INTO `brdg_cop_scores`(`id`, `torneoID`, `tavolo`, `turno`, `board`, `coppiaNS`, `coppiaEW`) 
				       		  		  VALUES (NULL,$ID_torneo,$Tavolo,$Turno,$board,$CoppiaNS,$CoppiaEW)";		
				       		  		  //VALUES (NULL,".$ID_torneo.",".$Tavolo.",".$Turno.",".$board.",".$CoppiaNS.",".$CoppiaEW.",NULL,NULL)";	, `licitaID`	, `score`
//var_dump ($sql);
//echo"<br>";	
//echo"sql= ".$sql;
//echo"<br>";	
					$dati= $connessione->query($sql); 

					if (!$dati) {
						$connessione->close();
						exit("<body bgcolor=\"#80ff80\"><b><center>ERRORE NELLA REGISTRAZIONE DEI TURNI</center></b></body>");
					}
				}
			}
		  	if($Turno==$Nturni)  $Turno= 0;
	
	   }
	   //  SCRIVE I TURNI NEL DATABSE
	   // AGGIORNA IL TURNO ATTUALE
	   $SecondiInizio= time();
	   $TurnoAttuale++;
	   $sql= "UPDATE `brdg_cop_tornei` SET `TurnoAttuale`=".$TurnoAttuale.",`Tavoli`=".$Ntavoli.",`Inizio`=".$SecondiInizio."  WHERE `ID_torneo`=".$ID_torneo;  
//echo"sql= ".$sql;
//echo"<br>";	
	   $dati = $connessione->query($sql);	

	}else{
	   // AGGIORNA IL TURNO ATTUALE
	   $TurnoAttuale++;
	   $dati = $connessione->query("UPDATE `brdg_cop_tornei` SET `TurnoAttuale`=".$TurnoAttuale." WHERE `ID_torneo`=".$ID_torneo);	
	}

mostra:
	// ##############################################
	// Mostra la tabella degli abbinamenti
	
echo "	
	<br>
	<br>
<body bgcolor=#80ff80>
<table width=\"100\" align=\"center\" border=\"1\">
<tr align=\"center\">
<td bgcolor=#eeeeaa> 	
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"500\">
	 <tr>
      <td align=\"center\"><b>TORNEO: </b>$NomeTorneo</td>
     </tr>
	 <tr>
      <td align=\"center\"><b> ABBINAMENTI DEL TURNO: $TurnoAttuale  DI  $Nturni </b></td>
     </tr>
    </table>
    "	;

echo "
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"700\">
	 <tr>
       <td width=\"5%\" align=\"center\"><strong>Tav.</strong></td>
       <td width=\"5%\" align=\"center\"><strong>Brd</strong></td>
       <td width=\"2%\" align=\"center\"><strong>-</strong></td>
       <td width=\"7%\" align=\"center\"><strong>IdNS</strong></td>
       <td width=\"37%\" align=\"center\"><strong>Coppia NS</strong></td>
       <td width=\"7%\" align=\"center\"><strong>IdEW</strong></td>
       <td width=\"37%\" align=\"center\"><strong>Coppia EW</strong></td>
     </tr>
    </table>
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"700\">
	 ";
	 
   	$sql= "SELECT * FROM `brdg_cop_scores` WHERE `turno` = ".$TurnoAttuale." AND torneoID=$ID_torneo ORDER BY `tavolo` ASC,`board` ASC";
//echo "sql : ".$sql;
//echo  "<br>";
   	$dati = $connessione->query($sql);
   	$row = $dati->fetch_assoc(); 
	while($row)  { 
		    
		$Tavolo= $row['tavolo'];
		$Board= $row['board'];
		$IdNS= $row['coppiaNS'];
		$IdEW= $row['coppiaEW'];
   		$row = $dati->fetch_assoc(); 

		// PER I TORNEI MITCHELL  --  in score si mettono I numeri originali delle coppie non quelli 100+
		//if($IdEW>100) $IdEW	= $IdEW%100 + $Niscritti/2;
		//  PER LA STAMPA NEL CASO DI TORNEO MITCHELL
		$IdEW_= $IdEW;
		if($Tipo==2) $IdEW_= $IdEW-$Niscritti/2 + 100;

		$dati1= $connessione->query("SELECT * FROM `brdg_cop_coppie` WHERE coppiaID=$IdNS AND torneoID=$ID_torneo"); 
    	$row1 = $dati1->fetch_assoc();
		$Nome1ID= $row1['nome1ID'];
		$Nome2ID= $row1['nome2ID'];
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$Nome1ID"); 
    	$row1 = $dati1->fetch_assoc();
		$Nome1NS= $row1['nome'];
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$Nome2ID"); 
    	$row1 = $dati1->fetch_assoc();
		$Nome2NS= $row1['nome'];

		$dati1= $connessione->query("SELECT * FROM `brdg_cop_coppie` WHERE coppiaID=$IdEW AND torneoID=$ID_torneo"); 
    	$row1 = $dati1->fetch_assoc();
		$Nome1ID= $row1['nome1ID'];
		$Nome2ID= $row1['nome2ID'];	
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$Nome1ID"); 
    	$row1 = $dati1->fetch_assoc();
		$Nome1EW= $row1['nome'];
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$Nome2ID"); 
    	$row1 = $dati1->fetch_assoc();
		$Nome2EW= $row1['nome'];

		echo"
		<tr>

         <td width=\"5%\" align=\"center\">".$Tavolo."</td>
         <td width=\"5%\" align=\"center\">".$Board."</td>
         <td width=\"2%\" align=\"center\"></td>
         <td width=\"7%\" align=\"center\">".$IdNS."</td>
         <td width=\"37%\" align=\"center\">".$Nome1NS."-".$Nome2NS."</td>
         <td width=\"7%\" align=\"center\">".$IdEW_."</td>
         <td width=\"37%\" align=\"center\">".$Nome1EW."-".$Nome2EW."</td>
		</tr>
		";	
		
	}

    echo "</table>";
	//echo"";
	//<button style="background-color:LightGray;align:center;" onclick="history.go(-1);">Indietro</button>
echo "</td>";
echo "</tr>";
//echo "</table>";  

//echo "</body>";


	$dati = $connessione->query("SELECT * FROM brdg_cop_pswd WHERE 1");
  	$row = $dati->fetch_assoc(); 
   	$PSWD= $row['password'];
	//$PSWDcri= ~$PSWD;

    $connessione->close();
	
	$AzioneBottone="document.forms[0].submit()";	
	$AzioneStampa="window.open('TorneoCopNuovoTurnoStampa.php?torneo=".$NomeTorneo."&orig=admin', '_blank')";
	
 ?>
 	<form action="TorneoCopAdmin.php?torneo=<?php echo $NomeTorneo ?>" method="POST">
		<input type="hidden" name="password" value="<?php echo $PSWD ?>"/>
	</form>

     
        <tr align="center" bgcolor='#f0e090'>         
          <td > 
			<div style="display: flex; justify-content: space-between; width: 95%;">
				<button style="background-color: LightGray;" onclick="<?php echo $AzioneBottone; ?>">Indietro</button> 
				<button style="background-color: LightGray;" onclick="<?php echo $AzioneStampa;  ?>">Stampa</button>
			</div>
		  </td>
        </tr>
      
</table> 

</body>

