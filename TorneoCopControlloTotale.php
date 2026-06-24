<?php  
// inclusione del file di connessione
include "dbConnessione.php";
echo "<body bgcolor=\"green\">";

//  RICEVE IL PARAMETRO TURNO IN ESAME
	$azione = $_POST['action'];
	$NomeTorneo= $_GET['torneo'];
	//$turnoOsservato= $_GET['turno'];
	// riceve da se stesso il tavolo
	$board= $_POST['board'];
//echo" Tavolo ".$Tavolo;
//echo"<br>";

	$ZonaNS= [false,true,false,true,true,false,true,false,false,true,false,true,true,false,true,false];
	$ZonaEW= [false,false,true,true,false,true,true,false,true,true,false,false,true,false,false,true];

//  CONTROLLA LA PRESENZA DEL TORNEO INSERITO NELLA TABELLA brdg_ind_tornei
	//  E RICAVA IL SUO ID
	$sql= "SELECT * FROM `brdg_cop_tornei` WHERE `NomeTorneo`= '".$NomeTorneo."'";
	$dati = $connessione->query($sql);
	$row = $dati->fetch_assoc(); 
	$ID_torneo= $row['ID_torneo'];
	//$turno= $row['TurnoAttuale'];	
	$NboardsXturno= $row['BoardsXturno'];		
	$Tavoli= $row['Tavoli'];		
    if (!is_numeric($ID_torneo)) {
		// chiusura della connessione
		$connessione->close();
		exit("<body bgcolor=\"#a0eea0\"><b><center>Il torneo \"". $torneo."\" non esiste</center></b></body>");
	 }
	 
	//  ESEGUE LE AZIONI
	//****************************************	
	if($azione=="Vai")  {
	  	header("Location: TorneoCopScoresXbrd.php?NomeTorneo=$NomeTorneo&board=$board");
		exit();
	}
	
	
//------------------------------------------------------
	//echo "Turno in esame->".$turnoOsservato;
	echo  "<br>";
	echo  "<br>";
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
																												
	$sql="SELECT * FROM `brdg_cop_scores` WHERE torneoID=".$ID_torneo." ORDER BY `board`,`turno`"; 		 		
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
      <td align=\"center\"><b> SCORES: </td>
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
     </tr>
	 ";
	$Nrighe=0;	
	while($row) {
		 $turno= $row['turno'];
		 $board= $row['board'];
		 $tavolo= $row['tavolo'];
		 $punti= $row['score'];
		 $coppiaNS= $row['coppiaNS'];
		 $coppiaEW= $row['coppiaEW'];
		 
		 
		$iBoard= ($board-1)%16;
		$VulnNS= $ZonaNS[$iBoard];
		$VulnEW= $ZonaEW[$iBoard];		
		 
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
	   <tr>
        <td align=\"center\"><strong>$turno</strong></td>
        <td align=\"center\"><strong>$tavolo</strong></td>
        <td align=\"center\"><strong>$board</strong></td>
        <td align=\"center\"><strong></strong></td>";
		echo"<td align=\"center\" ";
 		if($VulnNS) echo "style=\"background-color:red;\""; else echo "style=\"background-color:green;\""; echo" ><strong>$coppiaNS</strong></td>";
  								
        echo"<td align=\"center\"><strong>-</strong></td>";
		
		echo"<td align=\"center\" ";
 		if($VulnEW) echo "style=\"background-color:red;\""; else echo "style=\"background-color:green;\""; echo" ><strong>$coppiaEW</strong></td>";
        
        echo"<td align=\"center\"><strong>$punti</strong></td>
       </tr>
	    ";
		$row = $dati->fetch_assoc();
	}
		
    echo "</table>";
		echo " </td>";
     	echo " </tr>";
    echo "</table>";
    echo "</body>";
	
	// RICAVA LA PSWD
	$dati = $connessione->query("SELECT * FROM brdg_cop_pswd WHERE 1");
	$row = $dati->fetch_assoc(); 
	$PSWD= $row['password'];		
	
    // chiusura della connessione
    $connessione->close();
	$AzioneBottone="\"document.forms[0].submit()\"";	
 ?>

  	<form action="TorneoCopAdmin.php?torneo=<?php echo $NomeTorneo ?>" method="POST">
		<input type="hidden" name="password" value="<?php echo $PSWD ?>"/>
	</form>
	
	<form action="TorneoCopControlloTotale.php?torneo=<?php echo $NomeTorneo; ?>" method="post">	
	<table style="width:320px;" align="center" border="1">
        <tr align="center">
          <td style="width:220px;background-color:orange;"><b>Assegna scores del Board: <b></td>
          <td style="font-size:16px;"><input  name="board" size="1" type="numeric" value=""></td>
	      <td style="text-align: center;"><input style="background-color:lightgrey;font-size:16px;" name="action" type="submit" value="Vai"></td>";
        </tr>
    </table>	 	
	</form>	

    <table style="HEIGHT: 10px" width="10" align="center" border="1">
      <tbody>
        <tr align="center" bgcolor=#f0e090>         
         <td ><button style="background-color:LightGray;align:center;" onclick=<?php echo $AzioneBottone; ?>; ">Indietro</button></td>		  
        </tr>
      </tbody>
    </table>

	
