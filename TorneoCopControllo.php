<?php  
// inclusione del file di connessione
include "dbConnessione.php";
echo "<body bgcolor=\"green\">";

//  RICEVE IL PARAMETRO TURNO IN ESAME
	$azione = $_POST['action'] ?? NULL;
	$NomeTorneo= $_GET['torneo'] ?? NULL;
	$turnoOsservato= $_GET['turno'] ?? NULL;
	// riceve da se stesso il tavolo
	$Tavolo= $_POST['tavolo'] ?? NULL;
	$Tavolo_1= $_POST['tavolo_1'] ?? NULL;
	$TurnoNuovo= $_POST['turno'] ?? NULL;
//echo" Tavolo ".$Tavolo;
//echo"<br>";
	if($TurnoNuovo)  {
		$turno= $TurnoNuovo;
	}else{
		$turno= $turnoOsservato;
	}
	
//  CONTROLLA LA PRESENZA DEL TORNEO INSERITO NELLA TABELLA brdg_ind_tornei
	//  E RICAVA IL SUO ID
	$sql= "SELECT * FROM `brdg_cop_tornei` WHERE `NomeTorneo`= '".$NomeTorneo."'";
	$dati = $connessione->query($sql);
	$row = $dati->fetch_assoc(); 
	$ID_torneo= $row['ID_torneo'];
	$Tipo= $row['Tipo'];		
	$Ntavoli= $row['Tavoli'];	
	$NboardsXturno= $row['BoardsXturno'];		
    if (!is_numeric($ID_torneo)) {
		// chiusura della connessione
		$connessione->close();
		exit("<body bgcolor=\"#a0eea0\"><b><center>Il torneo \"". $torneo."\" non esiste</center></b></body>");
	 }
	 
	//  ESEGUE LE AZIONI
	//****************************************	
	if($azione=="Vai")  {
        // TROVA UN NOME A QUESTO TAVOLO
	
    	$sql= "SELECT * FROM `brdg_cop_scores` WHERE `torneoID`=$ID_torneo AND `tavolo`=$Tavolo AND `turno`=$turnoOsservato";
    	$dati = $connessione->query($sql);
    	$row = $dati->fetch_assoc(); 
    	$coppiaNS= $row['coppiaNS'];
    
    	$dati = $connessione->query("SELECT * FROM `brdg_cop_coppie` WHERE `torneoID`=$ID_torneo and `coppiaID`=$coppiaNS");
    	$row = $dati->fetch_assoc(); 
    	$nome2ID= $row['nome2ID'];
    
    	$dati = $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE `ID`=$nome2ID");
    	$row = $dati->fetch_assoc(); 
    	$nomeGio= $row['nome'];
	  	//header("Location: TorneoCopScoresXtav.php?NomeTorneo=$NomeTorneo&tavolo=$Tavolo&NumTurno=$turnoOsservato&orig=admin");
	  	header("Location: TorneoCopScoresTav.php?NomeTorneo=$NomeTorneo&tavolo=$Tavolo&NumTurno=$turnoOsservato&orig=admin");
		exit();
/*	*/		
		
		
		
       // VARIAZIONE FATTA PER IL CONTROLLO DELLE BOARDS NELLA VERSIONE WEBRIDGE PER TAVOLO
	  	//header("Location: TorneoCopScoresContrattoTav.php?NomeTorneo=$NomeTorneo&NumTurno=$turno&tavolo=$Tavolo&orig=controllo");
		//exit();
//echo" sql---> ".$sql;
//echo"<br>";

		
	}
	//*******************************
	if($azione=="Inverti")  {
        // TROVA UN NOME A QUESTO TAVOLO
    	$sql= "SELECT * FROM `brdg_cop_scores` WHERE `torneoID`=$ID_torneo AND `tavolo`=$Tavolo_1 AND `turno`=$turno";    //Osservato
//echo" sql---> ".$sql;
//echo"<br>";
    	$dati = $connessione->query($sql);
    	$row = $dati->fetch_assoc(); 
    	$coppiaNS= $row['coppiaNS'];
    	$coppiaEW= $row['coppiaEW'];

    	$sql= "UPDATE `brdg_cop_scores` SET  `coppiaNS`= $coppiaEW, `coppiaEW`= $coppiaNS WHERE `torneoID`=$ID_torneo AND `tavolo`=$Tavolo_1 AND `turno`=$turno";   //Osservato
    	$dati = $connessione->query($sql);
	}
	//*******************************
	//*******************************
	if($azione=="Cambia")  {
        // CAMBIA IL NUMERO DI TURNO
		 // header("Location: TorneoCopControllo.php?torneo=".$NomeTorneo."&turno=".$TurnoNuovo);
		 // exit();
	}
	//*******************************
/*
	if($azione=="Indietro")  {
    	// RICAVA LA PSWD
    	$dati = $connessione->query("SELECT * FROM brdg_cop_pswd WHERE 1");
      	$row = $dati->fetch_assoc(); 
       	$PSWD= $row['password'];		
    	$PSWDcri=~$PSWD;
	  	header("Location: TorneoCopAdmin.php?torneo=$NomeTorneo&password=$PSWDcri&msg=Torneo: ");
		exit();
	}
*/	
	
//------------------------------------------------------
	//echo "Turno in esame->".$turnoOsservato;
	echo  "<br>";
	echo  "<br>";
	echo  "<br>";
//------------------------------------------------------


//  CONTROLLA SE CI SONO TUTTI I RISULTATI   //Osservato 
	$sql="SELECT COUNT(*) FROM `brdg_cop_scores` WHERE torneoID=$ID_torneo
					   					and turno=$turno                     
										and score IS NULL";
//echo" sq---> ".$sql;
//echo"<br>";
	$dati= $connessione->query($sql); 								
	$row = $dati->fetch_array();
	$Nnulli= $row[0];
	
//  SELEZIONA I DATI	
																												
	$sql="SELECT * FROM `brdg_cop_scores` WHERE torneoID=".$ID_torneo."
					   					AND turno=".$turno." ORDER BY `ID`"; 		 //		Osservato
	$dati= $connessione->query($sql);
	if(!$dati ) echo"ERRORE: inserire turno";
	$row = $dati->fetch_assoc();
	
	// ##############################################
	echo "
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"700\">
	 <tr>
	 <td bgcolor=\"orange\">
	";
	
	// Mostra la tabella dei risultati introdotti al turno osservato
	echo "
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"700\">
	 <tr>
      <td align=\"center\"><b> TORNEO: </b>".$NomeTorneo."</td>
      <td align=\"center\"><b> TURNO: </b>".$turno."</td>
     </tr>
    </table>
    "	;                                        //Osservato
		
    echo "
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"700\">
	 <tr>
       <td align=\"center\"><strong>N.Tav</strong></td>
       <td align=\"center\"><strong>N.Brd</strong></td>
       <td align=\"center\"><strong>Copp.</strong></td>
       <td align=\"center\"><strong>Gio-N</strong></td>
       <td align=\"center\"><strong>Gio-S</strong></td>
       <td align=\"center\"><strong>Copp.</strong></td>
        <td align=\"center\"><strong>Gio-E</strong></td>
       <td align=\"center\"><strong>Gio-W</strong></td>
       <td align=\"center\"><strong>P.ti-NS</strong></td>
     </tr>
	 ";
	$Nrighe=0;	
	while($row) {
		 $board= $row['board'];
		 $tavolo= $row['tavolo'];
		 $punti= $row['score'];
		 $coppiaNS= $row['coppiaNS'];
		 $coppiaEW= $row['coppiaEW'];
		// CASO DI TORNEI MITCHELL
		$coppiaEW_= $coppiaEW;
		if($Tipo==2) $coppiaEW_= $coppiaEW-$Ntavoli + 100;
		 
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
		 
		 //  <td align=\"center\"><strong><font style=\"background-color:LightGreen;\">$nomeS</font></strong></td>

		 
		 echo"	
	   <tr>
        <td align=\"center\"><strong>$tavolo</strong></td>
        <td align=\"center\"><strong>$board</strong></td>
        <td align=\"center\"><strong>$coppiaNS</strong></td>
        <td align=\"center\"><strong>$nomeN</strong></td>
        <td align=\"center\"><strong>$nomeS</strong></td>
									
        <td align=\"center\"><strong>$coppiaEW_</strong></td>
        <td align=\"center\"><strong>$nomeE</strong></td>
        <td align=\"center\"><strong>$nomeW</strong></td>
        <td align=\"center\"><strong>$punti</strong></td>
       </tr>
	    ";
		$row = $dati->fetch_assoc();
	}
		
    echo "</table>";
		echo " </td>";
     	echo " </tr>";
    echo "</table>";
    echo "</body>";
	
	$dati = $connessione->query("SELECT * FROM brdg_cop_pswd WHERE 1");
  	$row = $dati->fetch_assoc(); 
   	$PSWD= $row['password'];
	$AzioneBottone="\"document.forms[0].submit()\"";	
	
    // chiusura della connessione
    $connessione->close();
?>
 	<form action="TorneoCopAdmin.php?torneo=<?php echo $NomeTorneo ?>" method="POST">
		<input type="hidden" name="password" value="<?php echo $PSWD ?>"/>
	</form>
	
  <form action="TorneoCopControllo.php?torneo=<?php echo $NomeTorneo; ?>&turno=<?php echo $turno; ?>" method="post">	<!--Osservato  -->
	
	<table style="width:280px;" align="center" border="1">
        <tr align="center">
          <td style="width:180px;background-color:orange;"><b>Controlla turno N.: <b></td>
          <td style="font-size:16px;"><input  name="turno" size="1" type="numeric" value=""></td>
	      <td style="text-align: center;"><input style="background-color:lightgrey;font-size:16px;" name="action" type="submit" value="Cambia"></td>
        </tr>
    </table>	
	<table style="width:280px;" align="center" border="1">
        <tr align="center">
          <td style="width:180px;background-color:orange;"><b>Assegna scores del Tav.: <b></td>
          <td style="font-size:16px;"><input  name="tavolo" size="1" type="numeric" value=""></td>
	      <td style="text-align: center;"><input style="background-color:lightgrey;font-size:16px;" name="action" type="submit" value="Vai"></td>
        </tr>
    </table>	
  	
	<table style="width:280px;" align="center" border="1">
        <tr align="center">
          <td style="width:180px;background-color:orange;"><b>Inverti linea del Tav.: <b></td>
          <td style="font-size:16px;"><input  name="tavolo_1" size="1" type="numeric" value=""></td>
	      <td style="text-align: center;"><input style="background-color:lightgrey;font-size:16px;" name="action" type="submit" value="Inverti"></td>
        </tr>
    </table>	
  	<br>
  </form>	

    <table style="HEIGHT: 10px" width="10" align="center" border="1">
      <tbody>
        <tr align="center" bgcolor=#f0e090>         
         <td ><button style="background-color:LightGray;align:center;" onclick=<?php echo $AzioneBottone; ?>; ">Indietro</button></td>		  
        </tr>
      </tbody>
    </table>
	
	
