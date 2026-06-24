<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->

<head>

	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title>Tornei a Coppie: Iscrizione</title>
	<meta http-equiv="X-UA-Compatible" content="IE=10" />
	<meta name="description" content="mostra tornei aperti per iscrizione">
	<meta name="author" content="ORMA">

	<!-- Mobile Specific Metas
  ================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- CSS
  ================================================== -->
	<link rel="stylesheet" href="css/base.css">
	<link rel="stylesheet" href="css/skeleton.css">
	<link rel="stylesheet" href="css/layout.css">

	<!--[if lt IE 9]>
		<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>-->
	<!--[endif]-->

	<!-- Favicons
	================================================== -->
	<link rel="shortcut icon" href="images/favicon.ico">

</head>
<style>

.container-tabelle {
  display: flex;
  flex-direction: column;    
  width: 100%;               
  gap: 2px;                 
  max-width: 800px;          
  margin: 0 auto;            

  background-color: #C0C0C0; 
  border: 2px solid #A9A9A9; 
  border-radius: 8px;        
  padding: 2px;             
  box-sizing: border-box;    
}
</style>


<?php  

include_once "dbConnessione.php";
	  $Punt=0;	
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
	
//  CONTROLLA LA PRESENZA DEL TORNEO INSERITO NELLA TABELLA brdg_ind_tornei
	//  E RICAVA IL SUO ID
	$sql= "SELECT * FROM `brdg_cop_tornei` WHERE `NomeTorneo`='".$NomeTorneo."'";
//echo "  sql ----->".$sql;
//echo "<br>";
	$dati = $connessione->query($sql);
	$row = $dati->fetch_assoc(); 
	$ID_torneo= $row['ID_torneo'];
	$NboardsXturno= $row['BoardsXturno'];
	$Nturni= $row['Turni'];
	$Punteggio= $row['Punteggio'];
	$Tipo= $row['Tipo'];
 	//$turno= $row['turno'];	
   if (!is_numeric($ID_torneo)) {
		// chiusura della connessione
		$connessione->close();
		exit("<body bgcolor=\"#a0eea0\"><b><center>Il torneo \"". $torneo."\" non esiste</center></b></body>");
	}
	$Nboards= $NboardsXturno * $Nturni;
	
	// -------- LEGGE IL NUMERO DI GIOCATORI ----------
	$sql="SELECT COUNT(*) FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo;
	$dati= $connessione->query($sql); 
	$row = $dati->fetch_array();
	$Ncoppie= $row[0];

	$NcoppieBRD= $Ncoppie/2;
    $MPteorici= ($Ncoppie/2-1)*2;

/*	
class=\"container\"
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
//  COLORE DELLA PAGINA
//echo"<div width=\"300\">";		  text=\"000000\"
  echo"<body bgcolor=#55ee55 >";
	
//  CONTROLLA LA COMPLETEZZA DEI RISULTATI PER QUESTO TURNO
	
	$sql="SELECT * FROM `brdg_cop_scores` WHERE  torneoID= ".$ID_torneo." AND turno = ".$NumTurno." AND score IS NULL" ;
	$dati= $connessione->query($sql); 
	$row = $dati->fetch_assoc();
	if($row) {
		$completa= false;
	}else{
		$completa= true;
	}	
	
	// ##############################################
	// Mostra la tabella della classifica
	if($completa==false) {
  	    echo "<center><b><font color=\"#FF0000\">I RISULTATI NON SONO COMPLETI</font></b></center>";
		echo "<br>";
	}

	//###############################################
	//QUI INSERISCE IL CALCOLO DELLE PERCENTUALI O DEI VOTI
	include "TorneoCopCalcClassifica.php";
		
	
	if($Punteggio==1)  {
		echo "<b><center><font size=\"5\" color=\"#0000FF\">CLASSIFICA PER MP</font></center></b>";
		echo "<br>";
	}else if($Punteggio==2)  {
		echo "<b><center><font size=\"5\" color=\"#0000FF\">CLASSIFICA PER PUNTI DI MERITO</font></center></b>";
		echo "<br>";
	}else if($Punteggio==3)  {
		echo "<b><center><font size=\"5\" color=\"#0000FF\">CLASSIFICA PER IMP</font></center></b>";
		echo "<br>";
	}else if($Punteggio==4)  {
		echo "<b><center><font size=\"5\" color=\"#0000FF\">CLASSIFICA PER DMP</font></center></b>";
		echo "<br>";
	}
	
		
	// ##############################################width=\"600\" width=\"600\"
	// Mostra la tabella della classifica


echo"
<div class=\"container-tabelle\">
<table  align=\"center\" border=\"1\" width=\"300\">
<tr align=\"center\">
<td bgcolor=#f0e090> 
	
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"300\">
	 <tr style=\"font-size: 25px;\" height=\"50\">
      <td align=\"center\"><b> CLASSIFICA DEL TORNEO: </b>$NomeTorneo</td>
      <td align=\"center\"><b>Turno </b>$NumTurno</td>
     </tr>
    </table>
    "	;
	
    echo "
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"300\">
	 <tr style=\"font-size: 23px;\" height=\"40\" >
       <td width=\"4%\" align=\"center\"><strong>Pos</strong></td>
       <td width=\"8%\" align=\"center\"><strong>Coppia</strong></td>
       <td width=\"45%\" align=\"center\"><strong>Giocatore_1</strong></td>
       <td width=\"45%\" align=\"center\"><strong>Giocatore_2</strong></td>";
	   if($Punteggio==1)  {
           echo"<td width=\"10%\" align=\"center\"><strong>Perc</strong></td>";
	   }else if($Punteggio==2)  {
           echo"<td width=\"10%\" align=\"center\"><strong>Voto</strong></td>";
	   }else if($Punteggio==3)  {
           echo"<td width=\"10%\" align=\"center\"><strong>IMP</strong></td>";
	   }else if($Punteggio==4)  {
           echo"<td width=\"10%\" align=\"center\"><strong>DMP</strong></td>";
	   }
    echo"</tr>";
	

	//  MOSTRA LA CLASSIFICA

	$k= 0;
	foreach ($PercMedia as $key => $value) {
		$Coppia[$k]=$key+1;		
		$CopID= $Coppia[$k];
			//  CASO DI TORNEO MITCHELL
		$coppiaID_= $CopID;		
		if($coppiaID_ > $Ncoppie/2 && $Tipo==2) $coppiaID_= $coppiaID_-$Ncoppie/2 + 100;
		
		$sql= "SELECT * FROM brdg_cop_coppie WHERE coppiaID=$CopID and torneoID= $ID_torneo";
		$dati = $connessione->query($sql); 
		$row = $dati->fetch_assoc();	
		$Nome1ID= $row['nome1ID'];
		$Nome2ID= $row['nome2ID'];
		
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$Nome1ID"); 	
    	$row1 = $dati1->fetch_assoc();
		$Nome1= $row1['nome'];	
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$Nome2ID"); 	
    	$row1 = $dati1->fetch_assoc();
		$Nome2= $row1['nome'];	

		$k1= $k+1;	
		$PercMedia_2 = number_format($PercMedia[$CopID-1], 2);
echo"
		<tr style=\"font-size: 22px;\" height=\"40\">
       <td width=\"4%\" align=\"center\">".$k1."</td> 
       <td width=\"8%\" align=\"center\">".$coppiaID_."</td>
       <td width=\"39%\" text-align=\"left\">".$Nome1."</td>
       <td width=\"39%\" text-align=\"left\">".$Nome2."</td>
       <td width=\"10%\" align=\"center\">".$PercMedia_2."</td>
		</tr>
";	
		$k += 1;
	}

 echo "</table>";


 echo "</td>";
 echo "</tr>"; 
echo "</table>";

/*
echo "  NomeTorneo ----->".$NomeTorneo;
echo "<br>";
echo "  NumTurno -->".$NumTurno;
echo "<br>";          

*/
	if($Origine=="admin") {
		// RICAVA LA PSWD
		$dati = $connessione->query("SELECT * FROM brdg_cop_pswd WHERE 1");
		$row = $dati->fetch_assoc(); 
		$PSWD= $row['password'];		
		//$PSWDcri=~$PSWD;
		
		$AzioneBottone="document.forms[0].submit()";	
	}else{
		//$AzioneBottone="\"window.history.go(-1)\"";	
		//$URL= "TorneoCopScoresContrattoTav.php";
		//$AzioneBottone="\"window.location.href =\"".$URL."\"";	
		$AzioneBottone="window.location.href ='TorneoCopScoresContrattoTav.php?NomeTorneo=$NomeTorneo&NumTurno=$NumTurno&tavolo=1&orig=risultati'";	
		
//// <td ><button style="background-color:LightGray;align:center;" onclick="window.history.go(-2); return false;">Indietro</button></td>
	}

// chiusura della connessione
//<-- </div> >width="10"width="10"
	$connessione->close();
 	
	$AzioneStampa="window.open('TorneoCopClassificaStampa.php?torneo=".$NomeTorneo."&turno=".$NumTurno."&orig=admin', '_blank')";

?>

	<form action="TorneoCopAdmin.php?torneo=<?php echo $NomeTorneo ?>" method="POST">
		<input type="hidden" name="password" value="<?php echo $PSWD ?>"/>
	</form>
<?php
echo"<form action=\"TorneoCopClassifica.php?torneo=".$NomeTorneo."&turno=".$NumTurno."\" method=\"post\">"; 


/*
	<table style="HEIGHT: 10px"  align="center" border="0">
      <tbody>
        <tr align="center" >         
         <td ><button style="background-color:LightGray;align:center; font-size: 36px;" onclick=<?php echo $AzioneBottone; ?>; return false;">Indietro</button></td>		  
        </tr>
      </tbody>
    </table> 
 	<br>
	<table style="HEIGHT: 10px"  align="center" border="0">
      <tbody>
        <tr align="center">
          <td ><button style="background-color:LightGray;align:center;font-size: 36px;" onclick=<?php echo $AzioneStampa ; ?>;>Stampa</button></td>
        </tr>
      </tbody>
    </table> 

*/


?>

    <table style="HEIGHT: 10px" align="center" border="1">
      <tbody>
        <tr align="center" bgcolor=#f0e090>
          <td ><input style="background-color:powderblue;" type="submit" value="MP" name="action"></td>
          <td >&nbsp;</td>
          <td ><input style="background-color:powderblue;" type="submit" value="IMP" name="action"></td>
          <td>&nbsp;</td>
          <td ><input style="background-color:powderblue;" type="submit" value="POM" name="action"></td>
           <td>&nbsp;</td> 
           <td ><input style="background-color:powderblue;" type="submit" value="DMP" name="action"></td> 
        </tr>
      </tbody>
    </table>
</form>	 

    <table style="HEIGHT: 10px" align="center" border="1">

        <tr align="center" bgcolor=#f0e090>
          <td > 
			<div style="display: flex; justify-content: space-between; width: 80%;">
				<button style="background-color: LightGray; font-size: 20px;" onclick="<?php echo $AzioneBottone; ?>">Indietro</button> 
				<button style="background-color: LightGray; font-size: 20px;" onclick="<?php echo $AzioneStampa;  ?>">Stampa</button>
			</div>
		  </td>
        </tr>
     
    </table>
 
</div>	
 
</body>

	     
