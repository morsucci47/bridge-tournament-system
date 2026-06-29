<?php  
// inclusione del file di connessione
include "dbConnessione.php";
echo "<body bgcolor=\"green\">";

//  RICEVE IL PARAMETRO TURNO IN ESAME
	$azione = $_POST['action'];
	$NomeTorneo= $_GET['torneo'];
	$Origine= $_GET['orig'];
	//$turnoOsservato= $_GET['turno'];
	// riceve da se stesso il tavolo
	$board= $_POST['board'];
//echo" Tavolo ".$Tavolo;
//echo"<br>";
	
//  CONTROLLA LA PRESENZA DEL TORNEO INSERITO NELLA TABELLA brdg_ind_tornei
	//  E RICAVA IL SUO ID
	$sql= "SELECT * FROM `brdg_cop_tornei` WHERE `NomeTorneo`= '".$NomeTorneo."'";
	$dati = $connessione->query($sql);
	$row = $dati->fetch_assoc(); 
	$ID_torneo= $row['ID_torneo'];
	//$turno= $row['TurnoAttuale'];	
	$NboardsXturno= $row['BoardsXturno'];		
 	$SecondiInizio= $row['Inizio'];	
    if (!is_numeric($ID_torneo)) {
		// chiusura della connessione
		$connessione->close();
		exit("<body bgcolor=\"#a0eea0\"><b><center>Il torneo \"". $torneo."\" non esiste</center></b></body>");
	 }


//***************************************************************************************
	// -------- LEGGE IL NUMERO DELLE COPPIE ----------
	$sql="SELECT COUNT(*) FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo;
	$dati= $connessione->query($sql); 
	$row = $dati->fetch_array();
	$Ncoppie= $row[0];
/*	 
	 echo "  Ncoppie ----->".$Ncoppie;
echo "<br>";
*/
//***************************************************************************************
	// ##############################################
	// MOSTRA LA LISTA DEGLI ISCRITTI 
	// ##############################################
	
	$Niscritti= $Ncoppie;
	echo  "<br>";
	echo  "<br>";
	
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
	echo"</table>"	;
	
echo "</td>";
echo "</tr>";
echo "</table>"; 
 
//echo "<br>";
//echo "<br>";

//***************************************************************************************
	 
	//  ESEGUE LE AZIONI
	//****************************************	
	if($azione=="Vai")  {
	  	header("Location: TorneoCopScoresXbrd.php?NomeTorneo=$NomeTorneo&board=$board");
		exit();
	}
	
	
//------------------------------------------------------
	//echo "Turno in esame->".$turnoOsservato;
	echo  "<br>";
//------------------------------------------------------

/*
//  CONTROLLA SE CI SONO TUTTI I RISULTATI
	$sql="SELECT COUNT(*) FROM `brdg_cop_scores` WHERE torneoID=$ID_torneo
					   					and turno=$turnoOsservato 
										and score IS NULL";
//echo" sq---> ".$sql;
//echo"<br>";
	$dati= $connessione->query($sql); 								
	$row = $dati->fetch_array();
	$Nnulli= $row[0];
*/	
//  SELEZIONA I DATI	
																												
	$sql="SELECT * FROM `brdg_cop_scores` WHERE torneoID=".$ID_torneo." ORDER BY `turno`,`tavolo`,`board`"; 		 		
	$dati= $connessione->query($sql);
	if(!$dati ) echo"ERRORE: inserire board";
	$row = $dati->fetch_assoc();
	
	// ##############################################
	echo "
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"600\">
	 <tr>
	 <td bgcolor=\"orange\">
	";
	
	// Mostra la tabella dei risultati introdotti al turno osservato
	echo "
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"500\">
	 <tr>
      <td align=\"center\"><b> TORNEO: </b>".$NomeTorneo."</td>
      <td align=\"center\"><b> SCORES E TEMPI: </td>
     </tr>
    </table>
    "	;
		
    echo "
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"600\">
	 <tr>
       <td align=\"center\"><strong>N.Tur</strong></td>
       <td align=\"center\"><strong>N.Tav</strong></td>
       <td align=\"center\"><strong>N.Brd</strong></td>
       <td align=\"center\"><strong></strong></td>
       <td align=\"center\"><strong>Copp-NS</strong></td>
       <td align=\"center\"><strong>-</strong></td>
        <td align=\"center\"><strong>Copp-EW</strong></td>
       <td align=\"center\"><strong>P.ti-NS</strong></td>
       <td align=\"center\"><strong>Tempo(min)</strong></td>
     </tr>
	 ";
	$Nrighe=0;	
	$TurnoAtt= 0;
	$TavoloPrima= 0;
	$SecondiPrima= $SecondiInizio;
	$Colore= "#FFA500";
	while($row) {
		$turno= $row['turno'];
		$board= $row['board'];
		$tavolo= $row['tavolo'];
		$punti= $row['score'];
		$coppiaNS= $row['coppiaNS'];
		$coppiaEW= $row['coppiaEW'];
		$Secondi= $row['secondi'];

		// CALCOLA IL MASSIMO TEMPO DEL TURNO ATTUALE
		if($turno > $TurnoAtt)  {
			$SecondiRif= $SecondiPrima;
			$sql="SELECT MAX(secondi) FROM `brdg_cop_scores` WHERE torneoID=".$ID_torneo." AND turno=".$turno; 		 		
			$dati1= $connessione->query($sql);
			$row1 = $dati1->fetch_array();
			$SecondiMax= $row1[0];
			
			$TurnoAtt= $turno;
			$SecondiPrima= $SecondiMax;		

			echo"	
		   <tr style=\"background-color:green;\">
			<td colspan=\"9\" align=\"center\"></td>
			";

/*
echo" turno---> ".$turno;
echo"<br>";
echo" Secondi---> ".$Secondi;
echo"<br>";
echo" SecondiMax---> ".$SecondiMax;
echo"<br>";
*/
		}
		//  CONTROLLA SE E' CAMBIATO IL TAVOLO PER COLORARE
		if($tavolo != $TavoloPrima)  {
			if($Colore == "#FFA500")  {
				$Colore= "#FFC500";
			}else{
				$Colore= "#FFA500";
			}
			$TavoloPrima= $tavolo;
		}
				 
		if($Secondi) {
			$Minuti= round( ($Secondi - $SecondiRif)/60,2);
		}else{
			$Minuti= "--";
		}	
		
/*		 
		$sql= "SELECT * FROM brdg_cop_coppie WHERE coppiaID=".$coppiaNS." AND `torneoID`=".$ID_torneo ;		
		$dati1 = $connessione->query($sql); 
		$row1 = $dati1->fetch_assoc();
		$nome1ID= $row1['nome1ID'];
		$nome2ID= $row1['nome2ID'];
		
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$nome1ID"); 
    	$row1 = $dati1->fetch_assoc();		
		$nomeN= substr($row1['nome'],0,12);	
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$nome2ID"); 
    	$row1 = $dati1->fetch_assoc();		
		$nomeS= substr($row1['nome'],0,12);	
		 
		$sql= "SELECT * FROM brdg_cop_coppie WHERE coppiaID=".$coppiaEW." AND `torneoID`=".$ID_torneo ;		
		$dati1 = $connessione->query($sql); 
		$row1 = $dati1->fetch_assoc();
		$nome1ID= $row1['nome1ID'];
		$nome2ID= $row1['nome2ID'];
		
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$nome1ID"); 
    	$row1 = $dati1->fetch_assoc();		
		$nomeE= substr($row1['nome'],0,12);	
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$nome2ID"); 
    	$row1 = $dati1->fetch_assoc();		
		$nomeW= substr($row1['nome'],0,12);	
*/		 
		 //  <td align=\"center\"><strong><font style=\"background-color:LightGreen;\">$nomeS</font></strong></td>

		 
		echo"	
	   <tr style=\"background-color:$Colore;\">
        <td align=\"center\"><strong>$turno</strong></td>
        <td align=\"center\"><strong>$tavolo</strong></td>
        <td align=\"center\"><strong>$board</strong></td>
        <td align=\"center\"><strong></strong></td>
        <td align=\"center\"><strong>$coppiaNS</strong></td>
									
        <td align=\"center\"><strong>-</strong></td>
        <td align=\"center\"><strong>$coppiaEW</strong></td>
        <td align=\"center\"><strong>$punti</strong></td>";
		
		if($Secondi==$SecondiMax) {
			echo"<td align=\"center\" style=\"background-color:yellow;\"><strong>$Minuti</strong></td>";
		}else{
			echo"<td align=\"center\"><strong>$Minuti</strong></td>";
		}
		
        echo"</tr>";
	    
		$row = $dati->fetch_assoc();
	}
		
    echo "</table>";

	
	// RICAVA LA PSWD
	$dati = $connessione->query("SELECT * FROM brdg_cop_pswd WHERE 1");
	$row = $dati->fetch_assoc(); 
	$PSWD= $row['password'];		
	

	if($Origine=="admin") {
		// RICAVA LA PSWD
		$dati = $connessione->query("SELECT * FROM brdg_cop_pswd WHERE 1");
		$row = $dati->fetch_assoc(); 
		$PSWD= $row['password'];		
		
		$AzioneBottone="\"document.forms[0].submit()\"";	
	}else{
		$AzioneBottone="\"window.location.href ='TorneoCopScoresContrattoTav.php?NomeTorneo=$NomeTorneo&NumTurno=$turno&tavolo=1&orig=risultati'";	
	}
    // chiusura della connessione
    $connessione->close();
?>

  	<form action="TorneoCopAdmin.php?torneo=<?php echo $NomeTorneo ?>" method="POST">
		<input type="hidden" name="password" value="<?php echo $PSWD ?>"/>
	</form>

	
   <table style="HEIGHT: 10px" width="10" align="left" border="1">
      <tbody>
        <tr align="center" bgcolor=#f0e090>         
         <td ><button style="background-color:LightGray;align:center;font-size: 36px;" onclick=<?php echo $AzioneBottone; ?>; ">Indietro</button></td>		  
        </tr>
      </tbody>
    </table>
	


	</td>
    </tr>
    </table>
    </body>

<?php

/*	
		<form action="TorneoCopControlloTotale.php?torneo=<?php echo $NomeTorneo; ?>" method="post">	
	<table style="width:320px;" align="center" border="1">
        <tr align="center">
          <td style="width:220px;background-color:orange;"><b>Assegna scores del Board: <b></td>
          <td style="font-size:16px;"><input  name="board" size="1" type="numeric" value=""></td>
	      <td style="text-align: center;"><input style="background-color:lightgrey;font-size:16px;" name="action" type="submit" value="Vai"></td>";
        </tr>
    </table>	 	
	</form>	



   <table style="HEIGHT: 10px" width="10" align="left" border="1">
      <tbody>
        <tr align="center" bgcolor=#f0e090>         
         <td ><button style="background-color:LightGray;align:center;font-size: 36px;" onclick=<?php echo $AzioneBottone; ?>; ">Indietro</button></td>		  
        </tr>
      </tbody>
    </table>


*/
	
 ?>

	

 


	
