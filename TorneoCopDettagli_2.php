
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
/*	 
	$NcoppieBRD= $Ncoppie/2;        
    $MPteorici= ($Ncoppie/2-1)*2;  
	$N_boards= $NboardsXturno * $Nturni;
	$Nboards= $N_boards;
	 
echo "  Ncoppie ----->".$Ncoppie;
echo "<br>";
echo "  MPteorici -->".$MPteorici;
echo "<br>";
echo "  N_boards -->".$N_boards;
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
	echo"</table>"	;
	
echo "</td>";
echo "</tr>";
echo "</table>"; 
 
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
	
	
	include "TorneoCopCalcClassifica.php";

	

echo"<body bgcolor=#55ee55>";

	if($completa==false) {
  	    echo "<center><b><font color=\"#FF0000\">I RISULTATI NON SONO COMPLETI</font></b></center>";
		echo "<br>";
	}


//*************************************
//  stampa gli scores estesi
//*************************************
	echo"

		<table width=\"100\" align=\"center\" border=\"1\">
		<tr align=\"center\">
		<td bgcolor=#f0e090> 
	
			<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"300\">
			<tr>
				<td align=\"center\"><strong>SCORES DEL TORNEO: $NomeTorneo</strong></td>
				<td align=\"center\"><b>Turno </b>$NumTurno</td>
			</tr>
			</table>
";


	//$sql="SELECT * FROM `brdg_cop_scores` WHERE torneoID=".$ID_torneo." ORDER BY `board`,`turno`"; 		 		


//   RICAVA  CONTRATTI E PUNTEGGI
//*************************************
    for($i_board=1; $i_board <= $Nboards ; $i_board++)    {
		echo"<table bgcolor= \"seashell\" width=\"310\" align=\"center\" border=\"1\">";
		//echo"<thead>";
			//echo"<tr><th colspan=\"18\"><b>RISULTATI</b></th></tr>";
		//echo"</thead>";
			echo"<tr align=\"center\">";
			echo"<th >Board</th>";
			echo"<th >Perc.NS</th>";
			echo"<th >NS</th>";
			echo"<th >Contratto</th>";
			echo"<th >Gioc.Da</th>";
			echo"<th >Attacco</th>";
			echo"<th >Punti</th>";
			echo"<th >EW</th>";
			echo"<th >Perc.EW</th>";
			echo"</tr>";
  				  		
		$sql= "SELECT * FROM brdg_cop_scores WHERE torneoID= $ID_torneo AND `board`=$i_board ORDER BY `turno`";	               
		$dati = $connessione->query($sql); 
		$row = $dati->fetch_assoc();
	    
		while($row)  {
			$CoppiaNS= $row['coppiaNS'];
			$CoppiaEW= $row['coppiaEW'];
			if($Tipo==2) {
				$CoppiaEW_= $CoppiaEW - $Ncoppie/2 + 100;
			}else{
				$CoppiaEW_= $CoppiaEW ;
			}				
			$Contratto=  $row['Contratto'];
			$Prese=  $row['Prese'];
			$Da=  $row['GiocatoDa'];
			$Attacco=  $row['Attacco'];
			
					
			// ** COLORAZIONE ROSSA  **********
			//<span style="color:red">♦  ♥</span>
			$Cont= $Contratto[0];
			$PosEscl= strpos($Contratto,'!');
			if($PosEscl){
				$Ratto=substr($Contratto,1,$PosEscl-1);
				$Altro=substr($Contratto,$PosEscl);
			}else{
				$Ratto=substr($Contratto,1);
				$Altro="";
			}
			
			$Att= $Attacco[0];
			if($Att=='1')  {
				$Att= "10";
				$Acco=substr($Attacco,2);
			}else{
				$Acco=substr($Attacco,1);	
			}
			//**********************************
			
			if($Prese==0) {
				$Prese= "=";			
			}else if($Prese>0){
				$Prese= "+".$Prese;
			}
			//$ContrPrese= $Contratto.$Prese;
			$ContrPrese= "";
			$Punti= $PuntiCop[$i_board-1][$CoppiaNS-1];
			$PercNS=  round($PercCop[$i_board-1][$CoppiaNS-1],2);
			$PercEW=  round($PercCop[$i_board-1][$CoppiaEW-1],2);
			if($Punti==-1)  {
				$Punti= "--";
				$PercNS= -1;
				$PercEW= -1;
				$Prese=  "--";
				$Da=  "--";
				$Attacco=  "--";
				$ContrPrese= "RIPOSO";
			}
			if(!isset($Punti))  {
				$Punti= "--";
				$PercNS= "--";
				$PercEW= "--";
				$Prese=  "--";
				$Da=  "--";
				$Attacco=  "--";
				$ContrPrese= "MANCA";
			}
			
			echo"<tr align=\"center\">";
			
			echo"<td align=\"center\">$i_board</td>";
			if($PercNS == 0) {
				echo"<td style=\"background-color:#FF0000;\" align=\"center\">$PercNS</td>"; 
			}else if($PercNS>0 && $PercNS<25){
				echo"<td style=\"background-color:#FF6464;\" align=\"center\">$PercNS</td>"; 
			}else if($PercNS>=25 && $PercNS<50){
				echo"<td style=\"background-color:#FFC8C8;\" align=\"center\">$PercNS</td>"; 
			}else if($PercNS==50){
				echo"<td style=\"background-color:#FFFF00;\" align=\"center\">$PercNS</td>"; 
			}else if($PercNS>=50 && $PercNS<75){
				echo"<td style=\"background-color:#C8FFC8;\" align=\"center\">$PercNS</td>"; 
			}else if($PercNS>=75 && $PercNS<100){
				echo"<td style=\"background-color:#96FF96;\" align=\"center\">$PercNS</td>"; 
			}else if($PercNS==100){
				echo"<td style=\"background-color:#00FF00;\" align=\"center\">$PercNS</td>"; 
			}else{					
				echo"<td align=\"center\">--</td>";
			}
			echo"<td align=\"center\">$CoppiaNS</td>";
			
			if($ContrPrese) {
				echo"<td align=\"center\">$ContrPrese</td>";
			}else{
				echo"<td align=\"center\">".$Cont;
				if($Ratto=="♥" || $Ratto=="♦") {
					echo"<span style=\"color:red\">".$Ratto."</span>".$Altro.$Prese."</td>";
				}else{
					echo $Ratto.$Altro.$Prese."</td>";			
				}		
			}		
			
			echo"<td align=\"center\">$Da</td>";
			
			//echo"<td align=\"center\">$Attacco</td>";
			echo"<td align=\"center\" >".$Att;
			if($Acco=="♥" || $Acco=="♦") {
				echo"<span style=\"color:red\">".$Acco."</span></td>";
			}else{
				echo $Acco."</td>";			
			}
			
			echo"<td align=\"center\">$Punti</td>";
			echo"<td align=\"center\">$CoppiaEW_</td>";

			if($PercEW == 0) {
				echo"<td style=\"background-color:#FF0000;\" align=\"center\">$PercEW</td>"; 
			}else if($PercEW>0 && $PercEW<25){
				echo"<td style=\"background-color:#FF6464;\" align=\"center\">$PercEW</td>"; 
			}else if($PercEW>=25 && $PercEW<50){
				echo"<td style=\"background-color:#FFC8C8;\" align=\"center\">$PercEW</td>"; 
			}else if($PercEW==50){
				echo"<td style=\"background-color:#FFFF00;\" align=\"center\">$PercEW</td>"; 
			}else if($PercEW>=50 && $PercEW<75){
				echo"<td style=\"background-color:#C8FFC8;\" align=\"center\">$PercEW</td>"; 
			}else if($PercEW>=75 && $PercEW<100){
				echo"<td style=\"background-color:#96FF96;\" align=\"center\">$PercEW</td>"; 
			}else if($PercEW==100){
				echo"<td style=\"background-color:#00FF00;\" align=\"center\">$PercEW</td>"; 
			}else{					
				echo"<td align=\"center\">--</td>";
			}
			echo"</tr>";
		   
			$row = $dati->fetch_assoc();
		}
		echo"<tr align=\"center\">";
		echo"</tr>";
		echo"</table>";
		echo "<br>";
		//echo "<br>";
			
	}	
	
//**********************************************************	
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

 
echo "<form action=\"./TorneoCopDettagli_2.php?torneo=".$NomeTorneo."&turno=".$NumTurno."\" method=\"post\">"; 
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
<br>
    <table style="HEIGHT: 10px" width="10" align="left" border="1">
      <tbody>
        <tr>
          <td ><button style="background-color:LightGray;align:center; font-size: 36px;" onclick=<?php echo $AzioneBottone; ?>; return false;">Indietro</button></td>
        </tr>
      </tbody>
    </table>

</body>




