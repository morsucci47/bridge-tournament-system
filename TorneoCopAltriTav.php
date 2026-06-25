<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>

	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title>Tornei Aperti :)</title>
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


	<!-- Favicons
	================================================== -->
	<link rel="shortcut icon" href="images/favicon.ico">

</head>
<style>
td {
  vertical-align: middle;
  text-align: center;
}
</style>
<?php  

// inclusione del file di connessione
include_once "dbConnessione.php";

	
// *******************************************************
//		LETTURA DEI PARAMETRI
// *******************************************************

	// riceve i parametri dal modulo insert-dati
	// ******************************************
	$torneoID = $_GET['torneoID'];
	$board= $_GET['board'];

	$torneo= $_GET['torneo'];	
	$turno= $_GET['turno'];
	$NumTavolo= $_GET['tavolo'];

	$TavoloAttualeNS= $_GET['TavAttNS'];	
	$TavoloAttualeEW= $_GET['TavAttEW'];
	
	$sql= "SELECT * FROM `brdg_cop_tornei` WHERE `ID_torneo`=$torneoID";
	$dati= $connessione->query($sql);
	$row = $dati->fetch_assoc();		
	$Tipo= $row['Tipo'];
	$Ntavoli= $row['Tavoli'];
	
	

//  ***************************modulo:**************************************************

	if($board<10) {
		$board_ = "0".$board;
	}else{
		$board_ = $board;			
	}
	// si aggiunge una query del timestamp per evitare la cache
	//$filename= '../WEBCAM/upload/tornei/'.$torneo.'/N_'.$board_.'.jpeg?r='.time();	
	$filename= '../WEBCAM/upload/tornei/'.$torneo.'/N_'.$board_.'.jpeg';	

	echo "filename: ".$filename;
	echo  "<br>";
//
	// Verifica che il file esista
	if (file_exists($filename)) {
		echo "<div  align=\"center\"><strong><h3>Board: $board</h3></strong></div> ";
		//echo "torneo=  ".$torneo;			   
		//echo "<br>";
		echo "<p><img src=\"".$filename."\" border=\"1\" align=\"center\" ></p>";

	}


	//<p><img src="<php echo $filename; >" border="1" align="center" ></p>

 

	//echo  "<p><img src=\"".$filename."\" border=\"1\" align=\"center\" ></p>";
	//echo  "<br>";

//echo "<table  border=\"0\" >";
echo"<div  align=\"center\"><strong><h3>Altri risultati</h3></strong></div>"; 
echo "<br>";

//echo "<table width=\"300px\" >";
//echo "</table>";

// *********************************************************
//  RISULTATI DEGLI ALTRI TAVOLI
// *********************************************************
  
//echo"<div class=\"container\">";

echo "<table width=\"300px\" >";


echo "<tbody bgcolor=\"orange\">";
	echo"<tr align=\"center\">";
	echo"<td width=\"3px\" style=\"font-size:24px;\"  colspan=\"7\" ><strong>Board: $board</strong></td>";
	echo"</tr>";    		
echo"</tr>";
echo "</tbody>";




echo "<tbody bgcolor=\"yellow\" style=\"font-size:24px;\">";
	echo"<tr align=\"center\">";
	echo"<td width=\"7%\"><strong>&nbsp NS  </strong></td>";
	echo"<td width=\"7%\"><strong>&nbsp EW </strong></td>";
	echo"<td width=\"20%\"><strong>Contratto</strong></td>";
	echo"<td width=\"7%\"><strong>&nbsp Da </strong></td>";
	echo"<td width=\"7%\"><strong>+/-</strong></td>";
	echo"<td width=\"20%\"><strong><br>Attacco</strong></td>";
	echo"<td width=\"20%\"><strong><br>Score</strong></td>";
	echo"</tr>";
		
	echo "</tbody>";
	
	echo "<tbody bgcolor=\"f0e090\" style=\"font-size:24px;\">";	
	
//  LEGGE I RISULTATI DEGLI ALTRI TAVOLI
	$sql= "SELECT * FROM `brdg_cop_scores` WHERE `torneoID`=$torneoID AND `board`=$board ORDER BY turno";
	$dati= $connessione->query($sql);
//echo "sql=  ".$sql;			   
//echo "<br>";
	$row = $dati->fetch_assoc();		
	while($row) {
		$coppiaNS= $row['coppiaNS'];
		$coppiaEW= $row['coppiaEW'];
			       //  CASO DI TORNEO MITCHELL
		if($Tipo==2) {$coppiaEW_= $coppiaEW-$Ntavoli + 100;
		}else{
			$coppiaEW_= $coppiaEW;
		}
	
		$Contratto= $row['Contratto'];
		$Da= $row['GiocatoDa'];
		$Prese= $row['Prese'];
		$Attacco= $row['Attacco'];
		$score= $row['score'];
		//  COLORAZIONE ROSSA
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

		$row = $dati->fetch_assoc(); 
		echo"<tr  height=\"50\" align=\"center\" >";
		echo"<td width=\"7%\" align=\"center\" ><b>".$coppiaNS."</b></td>";
		echo"<td width=\"7%\" align=\"center\" ><b>".$coppiaEW_."</b></td>";
		//echo"<td width=\"20%\" align=\"center\" ><b>".$Contratto."</b></td>";
		echo"<td width=\"20%\" align=\"center\" ><b>".$Cont."</b>";
		if($Ratto=="♥" || $Ratto=="♦") {
			echo"<b><span style=\"color:red\">".$Ratto."</span>".$Altro."</b></td>";
		}else{
			echo"<b>".$Ratto.$Altro."</b></td>";			
		}
		echo"<td width=\"7%\" align=\"center\" ><b>".$Da."</b></td>";
		echo"<td width=\"7%\" align=\"center\" ><b>".$Prese."</b></td>";
		//echo"<td width=\"20%\" align=\"center\" ><b>".$Attacco."</b></td>";
		echo"<td width=\"20%\" align=\"center\" ><b>".$Att."</b>";
		if($Acco=="♥" || $Acco=="♦") {
			echo"<b><span style=\"color:red\">".$Acco."</span></b></td>";
		}else{
			echo"<b>".$Acco."</b></td>";			
		}
		echo"<td width=\"20%\" align=\"center\" ><b>".$score."</b></td>";
		echo"</tr>";
	}	
		
    echo"</tr>";
	echo "</table>";

echo "</tbody>";
echo "</table>";

echo "</table>";


//*******************************************************
// ********************* TASTO RITORNO  ********************window.location.href = document.referrer;
//history.go(-1);
//*******************************************************return false;

$connessione->close();

$AzioneBottone1=" \"window.location.href ='TorneoCopScoresContrattoTav.php?NomeTorneo=$torneo&NumTurno=$turno&tavolo=$NumTavolo&orig=AltriTav&TavAttNS=$TavoloAttualeNS&TavAttEW=$TavoloAttualeEW';\" ";	
$AzioneBottone2=" \"window.location.href =' ../WEBCAM/leggi_pbn.php?Torneo=$torneo&Board=$board&turno=$turno&tavolo=$NumTavolo';\" ";	
	
	
//echo "AzioneBottone2: ".$AzioneBottone2;
//echo  "<br>";

/*
<script>
  function torna() {
	window.location.href = document.referrer+'&NomeTorneo=<?php echo $torneo; ?>&NumTurno=<?php echo $turno; ?>&tavolo=<?php echo $NumTavolo; ?>&orig=AltriTav';
  }
</script>
	<div align="center">";
         <button style="background-color:LightGray;align:center; font-size:20px;" onclick="torna()">Indietro</button>
    </div> 

*/

?>
<div align="center">
	<span >
         <button style="background-color:LightGray;text-align:center; font-size: 20px;" onclick= <?php echo $AzioneBottone1; ?> >Indietro</button>	  
    </span>
	&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 
	<span>
         <button style="background-color:orange;text-align:center; font-size: 20px;" onclick= <?php echo $AzioneBottone2; ?> >Analisi</button>	  
    </span> 
</div>    
<br>
<br>
