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
	================================================== <div class="container">-->
	<link rel="shortcut icon" href="images/favicon.ico">

</head>
<body bgcolor=#55ee55>
	<br>
	<br>


<div  align="center"><strong><h3>Contratto</h3></strong></div> 
<br>

<?php  

//*************************************************************************
//   QUESTO MODULO E' USATO SOLO PER INTERVENTI ARBITRALI
//      VENGONO TOLTE FUNZIONALITA' SUPERFLUE
//*************************************************************************


// inclusione del file di connessione
include_once "dbConnessione.php";

//$dealer=["N","E","S","W"];

$Posizioni= array(
	'N'	,
    'E' ,
    'S' ,
    'W'
);

$ZonaNS= [false,true,false,true,true,false,true,false,false,true,false,true,true,false,true,false];
$ZonaEW= [false,false,true,true,false,true,true,false,true,true,false,false,true,false,false,true];

$Valori= array(
		'A',
		'K',
		'Q',
		'J',
		'10',
		'9',
		'8',
		'7',
		'6',
		'5',
		'4',
		'3',
		'2'
);
// GENERA I SEMI IN FORMATO unicode UTF8

$Semi= array(
    chr(226).chr(153).chr(163),
    chr(226).chr(153).chr(166),
    chr(226).chr(153).chr(165),
    chr(226).chr(153).chr(160),
	'NT'
);
$BBox= array(
	   "1".chr(226).chr(153).chr(163) ,
       "1".chr(226).chr(153).chr(166) ,
       "1".chr(226).chr(153).chr(165),
       "1".chr(226).chr(153).chr(160),
	   "1".'NT',
	   "2".chr(226).chr(153).chr(163) ,
       "2".chr(226).chr(153).chr(166) ,
       "2".chr(226).chr(153).chr(165),
       "2".chr(226).chr(153).chr(160),
	   "2".'NT',
	   "3".chr(226).chr(153).chr(163) ,
       "3".chr(226).chr(153).chr(166) ,
       "3".chr(226).chr(153).chr(165),
       "3".chr(226).chr(153).chr(160),
	   "3".'NT',
	   "4".chr(226).chr(153).chr(163) ,
       "4".chr(226).chr(153).chr(166) ,
       "4".chr(226).chr(153).chr(165),
       "4".chr(226).chr(153).chr(160),
	   "4".'NT',
	   "5".chr(226).chr(153).chr(163) ,
       "5".chr(226).chr(153).chr(166) ,
       "5".chr(226).chr(153).chr(165),
       "5".chr(226).chr(153).chr(160),
	   "5".'NT',
	   "6".chr(226).chr(153).chr(163) ,
       "6".chr(226).chr(153).chr(166) ,
       "6".chr(226).chr(153).chr(165),
       "6".chr(226).chr(153).chr(160),
	   "6".'NT',
	   "7".chr(226).chr(153).chr(163) ,
       "7".chr(226).chr(153).chr(166) ,
       "7".chr(226).chr(153).chr(165),
       "7".chr(226).chr(153).chr(160),
	   "7".'NT'
);


$FIORI=	 0;
$QUADRI= 1;
$CUORI=  2;
$PICCHE= 3;
$NT=	 4;
	
// *******************************************************
//		LETTURA DEI PARAMETRI
// *******************************************************
   	$azione = $_POST['action'];
//echo "azione= ".$azione ;
//echo  "<br>";

	if($azione==NULL)  { 
		// riceve i parametri dal modulo insert-dati
		// ******************************************
		$torneoID = $_GET['torneo'];
		$turno = $_GET['turno'];
		$board= $_GET['board'];
		//$IDgio = $_GET['IDgio'];
		//$posGio = $_GET['posGio'];
		$NumTavolo = $_GET['tavolo'];
		$NumCoppiaNS = $_GET['coppiaNS'];
		$NumCoppiaEW = $_GET['coppiaEW'];
		$orig = $_GET['orig'];
		$posDealer= ($board-1)%4;
	//echo "orig= ".$orig ;
	//echo  "<br>";
		
		// LEGGE IL CONTRATTO NEL DATABASE
		$dati= $connessione->query("SELECT * FROM `brdg_cop_scores` WHERE `torneoID`=$torneoID AND  `turno`=$turno AND  `board`=$board");
		$row = $dati->fetch_assoc(); 
		$Contratto_= $row['Contratto'];
		$Da= $row['GiocatoDa'];
		$prese= $row['Prese'];
		$Attacco= $row['Attacco'];
		
		//**************************
	//echo "Contratto_= ".$Contratto_ ;
	//echo  "<br>";

		if($Contratto_[1]=="N")  {
			$Contratto= substr($Contratto_,0,3);
			$Contro= substr($Contratto_,3);
		}else if($Contratto_[0]=="P")  {
			$Contratto= "P";
			$Da= "-";
		}else if($Contratto_[0]=="R")  {
			$Contratto= "R";
			$Da= "-";
		}else{
			$Contratto= substr($Contratto_,0,4);
			$Contro= substr($Contratto_,4);
		}

		//if($Contro_ == "!") $Contro= "X";
		//if($Contro_ == "!!") $Contro= "XX";
		
		if($Attacco[1]=="0")  {
			$attNum= "10";
			$attSeme=  substr($Attacco,2);	
		}else{
			$attNum=   substr($Attacco,0,1);
			$attSeme=  substr($Attacco,1);	
		}
		
			
	}else{
		// riceve i parametri da se stesso
		// **********************************
		$torneoID = $_GET['torneoID'];
		$turno = $_GET['turno'];
		$board= $_GET['board'];
		$IDgio = $_GET['IDgio'];
		$posGio = $_GET['posGio'];
		$NumTavolo = $_GET['tavolo'];
		$NumCoppiaNS = $_GET['coppiaNS'];
		$NumCoppiaEW = $_GET['coppiaEW'];
		$Punti = $_GET['punti'];
		$orig = $_GET['orig'];

		$Contratto=  $_POST['Bid'];	
		$Contro=  $_POST['Contro'];	
		$prese=  $_POST['Prese'];	
		$Da=  $_POST['Da'];	
		$attNum=  $_POST['AttNum'];	
		$attSeme=  $_POST['AttSeme'];	
		
		$posContratto= array_search($Da,$Posizioni);
		//$posDich=  $posContratto;
	} 
//echo "NumCoppiaNS= ".$NumCoppiaNS ;
//echo  "<br>";

	$iBoard= ($board-1)%16;
	$iDealer= ($board-1)%4;
	$Dealer= $Posizioni[$iDealer];
			
	if($ZonaEW[$iBoard] && $ZonaNS[$iBoard]) {
		$Vulnerability= "All";
	}else if($ZonaEW[$iBoard] && !$ZonaNS[$iBoard]){
		$Vulnerability= "EW";
	}else if(!$ZonaEW[$iBoard] && $ZonaNS[$iBoard]){
		$Vulnerability= "NS" ;
	}else if(!$ZonaEW[$iBoard] && !$ZonaNS[$iBoard]){
		$Vulnerability= "None";
	}
/*
echo "board= ".$board ;
echo  "<br>";
echo "Dealer= ".$Dealer ;
echo  "<br>";
echo "prese= ".$prese ;
echo  "<br>";
echo "Vulnerability= ".$Vulnerability ;
echo  "<br>";
*/


//	******************************************************* 
	// TROVA LO STATO DEL TORNEO E il NOME
//	******************************************************* 
	$sql="SELECT * FROM `brdg_cop_tornei` WHERE `ID_torneo`=$torneoID";
	$dati= $connessione->query($sql); 		
	if(!$dati) exit("<body bgcolor=\"#a0eea0\"><b><center>Errore ricerca torneo</center></b></body>");
	$row = $dati->fetch_assoc();		
	$Stato= $row['Stato'];
	$NomeTorneo= $row['NomeTorneo'];
//	******************************************************* 
	// TROVA IL NOME  DEL TORNEO
//	******************************************************* 
/*
	$sql= "SELECT `NomeTorneo` FROM `brdg_cop_tornei` WHERE `ID_torneo`=$torneoID";
	$dati= $connessione->query($sql);
	$row = $dati->fetch_assoc(); 
	$NomeTorneo= $row['NomeTorneo'];	
*/
//	******************************************************* 
	// TROVA LA COPPIA DEL GIOCATORE -- SOLO SE LA GESTIONE E' PER GIOCATORE
//	******************************************************* 
/*
	if(!$NumTavolo)  {
		$sql="SELECT * FROM `brdg_cop_coppie` WHERE `torneoID`=$torneoID and (`nome1ID` = $IDgio or `nome2ID` = $IDgio)";
//echo $sql ;
//echo  "<br>";
		$dati= $connessione->query($sql); 		
		if(!$dati) exit("<body bgcolor=\"#a0eea0\"><b><center>Errore ricerca idgio</center></b></body>");
		$row = $dati->fetch_assoc();		
		$coppiaID= $row['coppiaID'];

//	******************************************************* 
	// TROVA IL NOME  DEL GIOCATORE -- SOLO SE LA GESTIONE E' PER GIOCATORE
//	******************************************************* 
	
		$sql="SELECT * FROM `brdg_ind_rubrica` WHERE id = $IDgio";
//echo $sql ;
//echo  "<br>";
		$dati= $connessione->query($sql); 		
		if(!$dati) exit("<body bgcolor=\"#a0eea0\"><b><center>Errore ricerca idgio</center></b></body>");
		$row = $dati->fetch_assoc();		
		$Giocatore= $row['nome'];
//echo "bid= ".$bid ;
//echo  "<br>";

//	******************************************************* 
//  LEGGE GLI ID DELLE COPPIE AL TAVOLO -- SOLO SE LA GESTIONE E' PER GIOCATORE
//  *****************************************************************************
		$dati= $connessione->query("SELECT * FROM `brdg_cop_scores` WHERE `torneoID`=$torneoID AND  `turno`=$turno AND  `board`=$board");
		$row = $dati->fetch_assoc(); 
		//$Tavolo= $row['tavolo'];
		$coppiaNS= $row['coppiaNS'];
		$coppiaEW= $row['coppiaEW'];
		//$dati= $connessione->query("UPDATE `brdg_cop_tavoli` SET `licitaID`=$licitaID WHERE `tavolo`=1");
	}
*/
//	*******************************************************	
//  ESEGUE LE AZIONI
//	******************************************************* 
	//$posGiocatore=  array_search($posGio,$Posizioni);	

//  *****************************************************************************
	if($azione=="OK") {

		if($Punti==NULL) {
			echo"<script>";
			echo"  alert(\"CALCOLARE IL PUNTEGGIO\");";
			echo"</script>";
			goto modulo;			
		}
		if($Stato==1) {
			echo"<script>";
			echo"  alert(\"IL TORNEO RISULTA CHIUSO\");";
			echo"</script>";
			goto modulo;			
		}

   		$Contratto=  $_POST['Bid'];	
   		$Contro=  $_POST['Contro'];	
   		$prese=  $_POST['Prese'];	
   		$Da=  $_POST['Da'];	
   		$attNum=  $_POST['AttNum'];	
   		$attSeme=  $_POST['AttSeme'];	
	
		$Contratto_= $Contratto;
		if($Contro == '!')   $Contratto_= $Contratto."!";
		if($Contro == '!!')  $Contratto_= $Contratto."!!";
		//$Contratto_= $Contratto.$Contro;
		$Attacco= $attNum.$attSeme;

		// INSERISCE IL CONTRATTO NEL DATABASE
		$sql="UPDATE `brdg_cop_scores` SET `score`=$Punti,`Contratto`='$Contratto_',`GiocatoDa`='$Da',`Prese`='$prese',`Attacco`='$Attacco'
					 WHERE `torneoID`=$torneoID and `turno`= $turno and `board` = $board ";
//echo $sql ;
//echo  "<br>";
		$esitoOK= $connessione->query($sql);

		// RITORNA A FORM PUNTI	
//goto modulo;
		//echo"<script>";	
		//echo "location.href='TorneoCopScores.php?NomeTorneo=$NomeTorneo&NumTurno=$turno&board=$board&giocatore=$Giocatore&orig=$orig';";
		//echo "</script>";
	    //header("Location: TorneoCopScores.php?NomeTorneo=".$NomeTorneo."&NumTurno=".$turno."&board=".$board."&giocatore=".$Giocatore."&orig=".$orig);
	    header("Location: TorneoCopScoresTav.php?NomeTorneo=".$NomeTorneo."&NumTurno=".$turno."&board=".$board."&tavolo=".$NumTavolo."&orig=".$orig);
		exit();
	}

//echo "Location: TorneoCopScores.php?NomeTorneo=$NomeTorneo&NumTurno=$turno&board=$board&giocatore=$Giocatore&orig=$orig";
//echo  "<br>";
//  *****************************************************************************
/*
    if($azione=="Licita") {
			
		// VA A LICITA
			
		//  DIRETTAMENTE A LICITA AUTO con auto-refresh
		header("Location: TorneoCopLicita_1.php?torneo=$torneoID&turno=$turno&IDgio=$IDgio&posGio=$posGio&board=$board");
		exit();

	}
*/
//  *****************************************************************************
    if($azione=="FOTO") {

		if($board<10) {
			$board_ = "0".$board;
		}else{
			$board_ = $board;			
		}
		
		// VA alla foto			
		// Controlla l' esistenza della foto
	//   nuovo

		if(file_exists($home_archive."/upload/tornei/".$NomeTorneo."/N_".$board_.".jpeg") ){
			$url = $home_archive."/Gestfoto.php?torneo=".$NomeTorneo."&turno=".$turno."&board=".$board."&tavolo=".$NumTavolo;
			
			echo "<script>";
			echo "alert('ATTENZIONE: LA FOTO DELLA BOARD N.".$board." ESISTE GIA\'');";
			echo "window.location.href = '".$url."';"; 
			echo "</script>";
			exit();
		} else {
			header("Location:".$home_archive."/Gestfoto.php?torneo=".$NomeTorneo."&turno=".$turno."&board=".$board."&tavolo=".$NumTavolo);
			exit();
		}

	
	}
//  *****************************************************************************
    if($azione=="oppure EDIT") {

		if($board<10) {
			$board_ = "0".$board;
		}else{
			$board_ = $board;			
		}
			
		// VA a input manuale		
		// Controlla l' esistenza del file

		if(file_exists($home_archive."/upload/tornei/".$NomeTorneo."/Board_".$board_.".pbn") ){
			$url = $home_archive."/scrivi_pbn.php?torneo=".$NomeTorneo."&turno=".$turno."&board=".$board."&tavolo=".$NumTavolo."&dealer=".$Dealer."&vulner=".$Vulnerability;
			echo "<script>";
			echo "alert('ATTENZIONE: IL FILE PBN DEL BOARD N.".$boardAtt." ESISTE GIA\'');";
			echo "window.location.href = '".$url."';"; 
			echo "</script>";
			exit();
		} else {
			header("Location:".$home_archive."/scrivi_pbn.php?torneo=".$NomeTorneo."&turno=".$turno."&board=".$board."&tavolo=".$NumTavolo."&dealer=".$Dealer."&vulner=".$Vulnerability);
			exit();
		}


	}

//  *****************************************************************************
    if($azione=="Gli altri risultati") {
			
			//	*******************************************************	
			//  CONTROLLO DELLA LICEITA'
			//	******************************************************* 
			//  *****************************************************************************
/*
		$sql= "SELECT * FROM `brdg_cop_coppie` WHERE (nome1ID=$IDgio or nome2ID=$IDgio) AND torneoID=$torneoID";
//echo $sql ;
//echo  "<br>";
		$dati = $connessione->query($sql);
		if(!$dati) exit("<body bgcolor=\"#a0eea0\"><b><center>Errore ricerca Coppia</center></b></body>");
		$row = $dati->fetch_assoc(); 
		$coppiaID= $row['coppiaID'];
*/
		$dati= $connessione->query("SELECT * FROM `brdg_cop_scores` WHERE `torneoID`=$torneoID and `turno`= $turno and `board` = $board");
	   	$row = $dati->fetch_assoc();		
		$score= $row['score'];	
//echo "score= ".$score ;
//echo  "<br>";
		if(!is_numeric($score)) {		
			echo"<script>";
			echo"  alert(\"NON SI POSSONO VEDERE GLI ALTRI RISULTATI PRIMA DI GIOCARE\");";
			echo"</script>";
		}else{
			header("Location: TorneoCopAltriTav.php?torneoID=$torneoID&board=$board");
			exit();
		}
	}
//  *****************************************************************************
    if($azione=="Vedi foto") {
			
			//	*******************************************************	
			//  CONTROLLO DELLA LICEITA'
			//	******************************************************* 
			//  *****************************************************************************
/*
		$sql= "SELECT * FROM `brdg_cop_coppie` WHERE (nome1ID=$IDgio or nome2ID=$IDgio) AND torneoID=$torneoID";
//echo $sql ;
//echo  "<br>";
		$dati = $connessione->query($sql);
		if(!$dati) exit("<body bgcolor=\"#a0eea0\"><b><center>Errore ricerca Coppia</center></b></body>");
		$row = $dati->fetch_assoc(); 
		$coppiaID= $row['coppiaID'];
*/
		$dati= $connessione->query("SELECT * FROM `brdg_cop_scores` WHERE `torneoID`=$torneoID and `turno`= $turno and `board` = $board");
	   	$row = $dati->fetch_assoc();		
		$score= $row['score'];	
//echo "score= ".$score ;
//echo  "<br>";
		if(!is_numeric($score)) {		
			echo"<script>";
			echo"  alert(\"NON SI POSSONO VEDERE LE MANI PRIMA DI GIOCARLE\");";
			echo"</script>";
  			//exit( "<body bgcolor=\"#a0eea0\"><b><center>NON SI POSSONO VEDERE LE MANI PRIMA DI GIOCARLE</center></b></body>");	
		}else{
			header("Location:".$home_archive."/VediFoto.php?torneo=$NomeTorneo&board=$board");
			exit();
		}
	}
//  *****************************************************************************
    if($azione=="Annulla") {
			
			// RITORNA A FORM PUNTI	SENZA FARE NIENTE	

			header("Location: TorneoCopScoresTav.php?NomeTorneo=".$NomeTorneo."&NumTurno=".$turno."&board=".$board."&tavolo=".$NumTavolo."&orig=".$orig);
			exit();
	}
	

//  *****************************************************************************
    //if($azione=="Calcola" || $azione==NULL && $Da!="" && is_numeric($Contratto[0])) {
    if($azione=="Calcola") {
		//unset($punti);
		 $Punti= 0;
		
	    if($Da=="-") {
			goto modulo;
		}
    
		//$sql="INSERT INTO `brdg_cop_licita`(`ID`, `licitaID`, `bid`) VALUES (NULL,$licitaID,'FINE')";
		//$esitoOK= $connessione->query($sql);
				
		$Tabella1fq=   [70,90,110,130,400,920,1440];
		$Tabella1fqX=  [140,180,470,510,550,1090,1630];
		$Tabella1fqXX= [230,560,640,720,800,1380,1960];
		
		$Tabella2fq=   [70,90,110,130,600,1370,2140];
		$Tabella2fqX=  [140,180,670,710,750,1540,2330];
		$Tabella2fqXX= [230,760,840,920,1000,1830,2660];
		
		$Tabella1cp=   [80,110,140,420,450,980,1510];
		$Tabella1cpX=  [160,470,530,590,650,1210,1770];
		$Tabella1cpXX= [520,640,760,880,1000,1620,2240];
		
		$Tabella2cp=   [80,110,140,620,650,1430,2210];
		$Tabella2cpX=  [160,670,730,790,850,1660,2470];
		$Tabella2cpXX= [720,840,960,1080,1200,2070,2940];
		
		$Tabella1nt=   [90,120,400,430,460,990,1520];
		$Tabella1ntX=  [180,490,550,610,670,1230,1790];
		$Tabella1ntXX= [560,680,800,920,1040,1660,2230];
		
		$Tabella2nt=   [90,120,600,630,660,1440,2220];
		$Tabella2ntX=  [180,690,750,810,870,1680,2490];
		$Tabella2ntXX= [760,880,1000,1120,1240,2110,2980];
		//  ******************************************
		
		$Prese1fq=   [-350,-300,-250,-200,-150,-100,-50,0,20,40,60,80,100,120,140];
		$Prese1fqX=  [-1700,-1400,-1100,-800,-500,-300,-100,0,100,200,300,400,500,600,700];
		$Prese1fqXX= [-3400,-2800,-2200,-1600,-1000,-600,-200,0,100,200,300,400,500,600,700];
		
		$Prese2fq=   [-700,-600,-500,-400,-300,-200,-100,0,20,40,60,80,100,120,140];
		$Prese2fqX=  [-2000,-1700,-1400,-1100,-800,-500,-200,0,200,400,600,800,1000,1200,1400];
		$Prese2fqXX= [-4000,-3400,-2800,-2200,-1600,-1000,-400,0,400,800,1200,1600,2000,2400,2800];
		
		$Prese1cp=   [-350,-300,-250,-200,-150,-100,-50,0,30,60,90,120,150,180,210];
		$Prese1cpX=  [-1700,-1400,-1100,-800,-500,-300,-100,0,100,200,300,400,500,600,700];
		$Prese1cpXX= [-3400,-2800,-2200,-1600,-1000,-600,-200,0,100,200,300,400,500,600,700];
		
		$Prese2cp=   [-700,-600,-500,-400,-300,-200,-100,0,30,60,90,120,150,180,210];
		$Prese2cpX=  [-2000,-1700,-1400,-1100,-800,-500,-200,0,200,400,600,800,1000,1200,1400];
		$Prese2cpXX= [-4000,-3400,-2800,-2200,-1600,-1000,-400,0,400,800,1200,1600,2000,2400,2800];		
		
		
		// LEGGE IL CONTRATTO
		if(is_numeric($Contratto[0])) {
  			$numeroContratto= substr($Contratto,0,1) - 1;
  			$semeContratto= substr($Contratto,1);
  			$iSeme= array_search($semeContratto,$Semi);
		}else{
  			$numeroContratto= 0;
  			$iSeme= 0;
		}
		// LEGGE LA LINEA DI CHI GIOCA IL CONTRATTO
		$Linea= $posContratto%2;
		
		// LEGGE LA ZONA DELLA MANO GIOCATA
		$iBoard= ($board-1)%16;
		if($Linea) {
			$Vulnerabile= $ZonaEW[$iBoard];
		}else{
			$Vulnerabile= $ZonaNS[$iBoard];
		}
				
				
		// LEGGE EVENTUALE X XX
		if($Contro == '!')  {$X= true;}else{$X= false;}
		if($Contro == '!!')  {$XX= true;}else{$XX= false;}
	
/*
echo  "<br>";	   
echo "iSeme: ".$iSeme ;
echo  "<br>";	   
echo "Linea: ".$Linea ;
echo  "<br>";	   
echo "iBoard: ".$iBoard ;
echo  "<br>";	   

echo "numeroContratto: ".$numeroContratto ;
echo  "<br>";

echo "prese: ".$prese ;
echo  "<br>";
*/	
		// SE MANTENUTO CALCOLA IL PUNTEGGIO BASE  / X / XX
		if($Contratto=="R")  {
			$Punti= -1;
			$Da= "-";
			goto modulo;
		}
		if($Contratto=="P")  {
			$Punti= 0;
			$Da= "-";
			goto modulo;
		}
		
		if($prese>=0)  {
			if($iSeme<2) {
				if($X) {
					if($Vulnerabile) $Punti= $Tabella2fqX[$numeroContratto]; else $Punti= $Tabella1fqX[$numeroContratto];
				}else if($XX){
					if($Vulnerabile) $Punti= $Tabella2fqXX[$numeroContratto]; else	$Punti= $Tabella1fqXX[$numeroContratto];		
				}else{
					if($Vulnerabile) $Punti= $Tabella2fq[$numeroContratto];	 else	 $Punti= $Tabella1fq[$numeroContratto];							
				}
			}else if($iSeme==4) {
				if($X) {
					if($Vulnerabile) $Punti= $Tabella2ntX[$numeroContratto]; else $Punti= $Tabella1ntX[$numeroContratto]; 
				}else if($XX){
					if($Vulnerabile) $Punti= $Tabella2ntXX[$numeroContratto]; else	$Punti= $Tabella1ntXX[$numeroContratto];	
				}else{
					if($Vulnerabile) $Punti= $Tabella2nt[$numeroContratto];	 else	 $Punti= $Tabella1nt[$numeroContratto];							
				}
			}else{
				if($X) {
					if($Vulnerabile) $Punti= $Tabella2cpX[$numeroContratto]; else $Punti= $Tabella1cpX[$numeroContratto];
				}else if($XX){
					if($Vulnerabile) $Punti= $Tabella2cpXX[$numeroContratto]; else	 $Punti= $Tabella1cpXX[$numeroContratto]; 		
				}else{
					if($Vulnerabile) $Punti= $Tabella2cp[$numeroContratto];	 else	 $Punti= $Tabella1cp[$numeroContratto];						
				}
			}
		}	
		
		//  CALCOLA I PUNTI DI EVENTUALI PRESE IN PIU' O IN MENO / X / XX		
		if($prese != 0)  {
			if($iSeme<2) {
				if($X) {
					if($Vulnerabile) $Punti += $Prese2fqX[$prese+7]; else $Punti += $Prese1fqX[$prese+7]; 
				}else if($XX){
					if($Vulnerabile) $Punti += $Prese2fqXX[$prese+7]; else	$Punti += $Prese1fqXX[$prese+7];	
				}else{
					if($Vulnerabile) $Punti += $Prese2fq[$prese+7];	 else $Punti += $Prese1fq[$prese+7];							
				}
			}else{
				if($X) {
					if($Vulnerabile) $Punti += $Prese2cpX[$prese+7]; else $Punti += $Prese1cpX[$prese+7]; 
				}else if($XX){
					if($Vulnerabile) $Punti += $Prese2cpXX[$prese+7]; else $Punti += $Prese1cpXX[$prese+7];			
				}else{
					if($Vulnerabile) $Punti += $Prese2cp[$prese+7];	 else $Punti += $Prese1cp[$prese+7];							
				}				
			}		
		}
		
		if($Linea==1) $Punti= -$Punti;
	}


// **********************************************************

modulo:

// *********************************************************
//  CALCOLA L' INIZIO DELLA SCELTA
// *********************************************************
		$PrimoNum= 1;
		$PrimoSeme= 0;
 		$numero= $numeroContratto+1;
		$PrimoNum= -6-$numero;
		$UltimoNum= 7-$numero;
		
// *********************************************************
//  PER COLORARE LA LINEA DI GIOCO
// *********************************************************
	$iBoard= ($board-1)%16;
	$VulnNS= $ZonaNS[$iBoard];
	$VulnEW= $ZonaEW[$iBoard];
	
		
echo "<table align=\"center\" width=\"260\"> ";

echo "<table align=\"center\" width=\"300px\"  >";
echo "<tbody bgcolor=\"orange\" style= \"font-size:22px;\">";
	echo"<tr align=\"center\" >";
	echo"<td width=\"3px\"><strong>Board:</strong></td>";
	echo"<td width=10px><strong>$board</strong></td>";
	echo"<td width=\"3px\"><strong>....</strong></td>";
	echo"<td width=\"3px\" "; if($VulnNS) echo "style=\"background-color:red;\""; else echo "style=\"background-color:green;\""; echo" ><strong>NS:</strong></td>";
	echo"<td width=\"10px\"><strong>$NumCoppiaNS</strong></td>";
	echo"<td width=\"3px\" "; if($VulnEW) echo "style=\"background-color:red;\""; else echo "style=\"background-color:green;\""; echo" ><strong>EW:</strong></td>";
	echo"<td width=\"10px\"><strong>$NumCoppiaEW</strong></td>";
	//echo"<td width=80%><font style=\"background-color:violet;font-size:16px\" ><strong>$Giocatore</strong></font></td>";
	echo"</tr>";    		
echo"</tr>";
echo "</tbody>";
echo "</table>";

// *********************************************************
//  INSERIMENTO DEL RISULTATO DEL CONTRATTO
// *********************************************************
  

echo "<table align=\"center\" width=\"300px\" >";
	echo "<tbody bgcolor=\"yellow\" style=\"font-size:18px;\">";
	echo"<tr align=\"center\">";
	echo"<td width=\"8px\"><strong>Contrat</strong></td>";
	echo"<td width=\"8px\"><strong> X  </strong></td>";
	echo"<td width=\"8px\"><strong> Da </strong></td>";
	echo"<td width=\"8px\"><strong>+/-</strong></td>";
	echo"<td colspan=\"2\" width=\"8px\"><strong>Attacco</strong></td>";
	//echo"<td width=\"8px\"><strong>cco</strong></td>";
	echo"</tr>";
	
	echo "</tbody>";
	
	echo "<form action=\"TorneoCopContrattoTav.php?torneoID=$torneoID&turno=$turno&IDgio=$IDgio&posGio=$posGio&board=$board
					&punti=$Punti&tavolo=$NumTavolo&coppiaNS=$NumCoppiaNS&coppiaEW=$NumCoppiaEW&orig=$orig\" method=\"post\">";
	
		echo "<tbody bgcolor=\"f0e090\">";
		echo"<tr >";
		
		// CONTRATTO    width=\"50px\"
		echo"<td align=\"center\" >";
		echo"<select  name=\"Bid\" style=\"text-align: center; width: 60px; height: 40px; font-size:18px; font-weight: bold;\" >";
	
		if($Contratto=="R") {echo "<option value='R' selected='selected' style=\"color: red;\">R</option>";}else{echo "<option value='R' style=\"color: red;\">R</option>";}
		if($Contratto=="P") {echo "<option value='P' selected='selected'>PP</option>";}else{echo "<option value='P'>PP</option>";}

		for($n=0;$n<35;$n++){
			echo "<option value='$BBox[$n]'"; 
			if ($Contratto==$BBox[$n]) {echo " selected='selected'>";}else{echo ">";}
			echo $BBox[$n]."</option>";
		} 
		echo"</select>";
    	echo"</td>";
		
		// 	CONTRO
		echo"<td align=\"center\" width=\"30px\">";
		echo"<select  name=\"Contro\" style=\"text-align: center; width: 50px; height: 40px; font-size:18px; font-weight: bold;\" >";
		echo "<option value='--'></option>";
		if($Contro=="!") {echo "<option value='!' selected='selected'> !</option>";}else{echo "<option value='!'> !</option>";}
		if($Contro=="!!") {echo "<option value='!!' selected='selected'>! !</option>";}else{echo "<option value='!!'>! !</option>";}
		echo"</select>";
    	echo"</td>";
		
		// GIOCATO DA
		echo"<td align=\"center\" width=\"30px\">";
		echo"<select  name=\"Da\" style=\"text-align: center; width: 50px; height: 40px; font-size:18px; font-weight: bold;\" >";
		echo "<option value='-'></option>";		
		for($n=0;$n<4;$n++){
			echo "<option value='$Posizioni[$n]'"; 
			if ($Da==$Posizioni[$n]) {echo " selected='selected'>";}else{echo ">";}
			echo $Posizioni[$n]."</option>";
		} 	
		echo"</select>";
    	echo"</td>";
		
		//  PRESE +/-	
    	echo"<td align=\"center\" width=\"4px\">";
		echo"<select style=\"text-align: center; width: 50px; height: 40px; font-size:18px; font-weight: bold;\" name=\"Prese\">";             
		for($n=$PrimoNum;$n<=$UltimoNum;$n++){
			if($n==$prese) {
				echo "<option value='$n' selected='selected'>".$n."</option>";
			}else{
				echo "<option value='$n'>".$n."</option>";
			}
		} 
		echo"</select>";
    	echo"</td>";
		
		// ATTACCO VALORE
		echo"<td align=\"center\" width=\"40px\">";
		echo"<select  name=\"AttNum\" style=\"text-align: center; width: 55px; height: 40px; font-size:18px; font-weight: bold;\" >";
		echo "<option value='--'></option>";
		for($n=0;$n<13;$n++){
			echo "<option value='$Valori[$n]'"; 
			if ($attNum==$Valori[$n]) {echo " selected='selected'>";}else{echo ">";}
			echo $Valori[$n]."</option>";
		} 		
		echo"</select>";
		echo"</td>";
		
		// ATTACCO SEME
    	echo"<td align=\"center\"width=\"40px\">";
		echo"<select style=\"text-align: center; width: 55px; height: 40px; font-size:18px; font-weight: bold;\" name=\"AttSeme\">";             
		echo "<option value='--'></option>";
		for($n=0;$n<4;$n++){
			echo "<option value='$Semi[$n]'"; 
			if ($attSeme==$Semi[$n]) {echo " selected='selected'>";}else{echo ">";}
			echo $Semi[$n]."</option>";
				//echo "<option value='$n'>$Semi[$n]</option>";
		} 
		echo"</select>";
    	echo"</td>";
			
    	echo"</tr>";
		echo "</table>";

echo "</tbody>";
echo "</table>";


//*******************************************************
// ********************* FORM DI SCELTA  ********************
//*******************************************************

// ***********************************************************************

//echo "Npass: ".$Npass ;
//echo  "<br>";	   
//echo "lastBidVera: ".$lastBidVera ;
//echo  "<br>";	   

		
		echo "<table align=\"center\" width=\"300\" >";
		echo "<tbody bgcolor=\"f0e090\">";
		
    	echo"<tr>";
    	echo"<td width=\"42px\">";
echo  "<br>";	   
echo  "<br>";	   	
    	echo"</td>";
		
    	echo"<td>";
    	echo"</td>";

    	echo"<td>";
		echo "<div style=\"background-color:yellow; font-size:22px;\"><strong>$Punti</strong></div>";
    	echo"</td>";

    	echo"<td>";
					
    	echo"</td>";

    	echo"<td>";
    	echo"</td>";
     	echo"<tr>";

    	echo"<td>";
			echo"<body style=\"text-align: center;\"><input style=\"background-color:gold; font-size:22px;\" name=\"action\" type=\"submit\" value=\"Calcola\"></body>";
		echo"</td>";
		
    	echo"<td width=\"40px\">";
			
    	echo"</td>";
		
    	echo"<td>";    
		echo"<body style=\"text-align: center;\"><input style=\"background-color:MediumSeaGreen;font-size:22px;\" name=\"action\" type=\"submit\" value=\"OK\"></body>";
   		echo"</td>";

    	echo"<td width=\"40px\">";	
			echo "<div  font-size:22px;\"><strong></strong></div>";
			//echo"...";	
			//echo"<body style=\"text-align: center;\"><input style=\"background-color:lightgrey; font-size:22px;\" name=\"action\" type=\"submit\" value=\"TORNA\"></body>";	
    	echo"</td>";
		
    	echo"<td>";		
 			echo"<body style=\"text-align: center;\"><input style=\"background-color:orange; font-size:22px;\" name=\"action\" type=\"submit\" value=\"FOTO\"></body>";
			echo"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo"<body style=\"text-align: center;\"><input id=\"mioTesto2\" style=\"background-color:yellow; font-size:22px; color:black;\" name=\"action\" type=\"submit\" value=\"oppure EDIT\"></body>";
		echo"</td>";
		
    	echo"</tr>";
		echo "</tbody";
		echo "</table>";
			
	echo"</form>";
	
	
$connessione->close();
   echo"
    <table align=\"center\"  width=\"300\" >
      <tbody bgcolor=\"f0e090\">
        <tr align=\"center\" >
          <td >
<br>	   
<br>	   	
		  
	<form action=\"TorneoCopContrattoTav.php?torneoID=$torneoID&turno=$turno&IDgio=$IDgio&posGio=$posGio&board=$board&punti=$Punti&tavolo=$NumTavolo&orig=$orig\" method=\"post\">
		 <!-- <input style=\"background-color:lightgrey; font-size:22px;\" name=\"action\" type=\"submit\" value=\"Gli altri risultati\">	-->
		 <input style=\"background-color:lightgrey; font-size:22px;\" name=\"action\" type=\"submit\" value=\"Annulla\">
		 <!-- <input style=\"background-color:lightgrey; font-size:22px;\" name=\"action\" type=\"submit\" value=\"Vedi foto\">  -->
	</form>
		  </td>
        </tr>
      </tbody>
    </table>
	";

?> 

</table>
</body>
