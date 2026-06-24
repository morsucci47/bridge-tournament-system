<?php
ob_start();
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
?>
<html>
<head>
	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title>Ingresso scores</title>
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


<style>
		#decimal-input {
			width: 50px;
			text-align: center; 
		}   
		.inline-label {
			display: inline;
		}	
		.no-border {
			border: none;
		}

		/* Applica il bordo pesca a tutta la tabella, alle celle (td) e alle intestazioni (th) */
		/*  NON USATA */
		.tabella-pesca, 
		.tabella-pesca th, 
		.tabella-pesca td {
			border: 2px solid #FFCBA4; /* Spessore, stile solido e colore pesca */
			border-collapse: collapse; /* Evita il doppio bordo unendo le linee */
			padding: 8px; /* Un po' di spazio interno per respirare */
		}

/* --------------------------------------------------- */
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

/* NUOVO: Questo elemento avvolge ogni tabella e gestisce lo scorrimento sui telefoni */
.table-wrapper {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

/* MODIFICATO: La tabella adesso può stirarsi liberamente al 100% */
.container-tabelle table {
  width: 100%;               /* Forza la tabella a occupare tutto lo spazio disponibile */
  border: 0px solid #ec8c43; 
  border-collapse: collapse;
  table-layout: fixed;       /* OPZIONALE: Distribuisce le colonne in modo equo */
}

.container-tabelle th, 
.container-tabelle td {
  border: 1px solid #FFCBA4; 
  padding: 8px;              
  /* Nota: se vuoi mantenere le tabelle centrate come nel tuo codice originale, 
     puoi cambiare left con center o rimuovere proprio text-align */
  text-align: center;        
}
/* -----------------------------------------#FFCBA4---FFFDF0---------- */
/* Tabella standard con bordi */
.tabella-con-bordi {
    border-collapse: collapse;
	background-color:  #f0e090
}
.tabella-con-bordi th, .tabella-con-bordi td {
    border: 1px solid #A9A9A9;; /* Colore Silver */
    padding: 8px;
}

/* Tabella pulita senza linee interne */
.tabella-pulita {
    border-collapse: collapse;
}
.tabella-pulita td {
    border: none; /* Elimina le linee */
    padding: 8px;
}
/*firebrick#E2725B*/
.cella-nick {
    background-color: firebrick; /* Arancione */
    color: White;
    padding: 2px;
    text-align: center;
    font-weight: bold;
}

/* Gestione responsive dei select interni alla tabella */
.select-responsive {
  width: 100%;          /* Occupa tutto lo spazio della cella, non di più */
  box-sizing: border-box;
  text-align: center;
  font-weight: bold;
  
  /* Semplifichiamo le altezze e i font usando i rem/px invece dei vw */
  height: 60px;         
  font-size: 26px;      
}

/* Opzionale: se su schermi piccolissimi i select sono troppo sacrificati, 
   puoi rimpicciolire leggermente il font per evitare che si taglino */
@media (max-width: 480px) {
  .select-responsive {
    font-size: 14px;
    height: 35px;
    padding: 2px;
  }
}

        .timer-container {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 1px auto;
            padding: 5px;
            max-width: 80px;
            border: 2px solid #ccc;
            border-radius: 5px;
            background-color: #cccccc;

        }
        .timer-display {
            font-size: 24px;
            font-family: monospace;
            margin: 4px 0;
            color: #070707fa;
            background-color: #cccccc;;
            border-radius: 5px;
        }
        .finished {
            color: red;
        }
</style>



	<!-- Favicons
	================================================== -->
	<link rel="shortcut icon" href="images/favicon.ico">
	
	
</head>
<!-- =============================================== -->




<body bgcolor="#55ee55">

<?php
//ini_set('display_errors', 1); error_reporting(E_ALL);

/*

<style>
.red-suit {
	color: red;
}
</style>	


<style>
  .form-container {
    width: 100%;
    max-width: 600px; 
    margin: auto; 
  }

  @media (max-width: 768px) {
    .form-container {
      max-width: 100%; 
    }
  }
</style>
*/
/*

* genera una form per l'inserimento dei risultati di un turno
* per ogni board del turno relativo ad un tavolo
* La pagina viene chiamata da
* 1- "Tornei a coppie:accesso" che trasmette i parametri del torneo,  e del nome
* 2- la sua stessa pagina , che trasmette i parametri sia in POST che GET
*/
// inclusione del file di connessione

include_once "dbConnessione.php";
   	  
//$PARI= 0;
//$DISPARI= 1;	
	$InputContratto= false;
	$NascondiScores= false;

	$AccediTorneoChiuso= false;
	$msg= "";
	$MostraRisultati= false;
	$numeroContratto = 0;
	$MostraSpostamentiFatti= false; 
	$TurnoInizio = 0;
	$ID_torneo  = 0;

	
//  	SERVE ANCHE PER COLORARE VERDE O ROSSO LE COPPIE
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
    
	$azione = $_POST['action'] ?? NULL;
    //$azione = $_POST['action'];
//echo "azione: ".$azione ;
//echo  "<br>";

    if($azione!=NULL)  { 
		//  L'ORIGINE E' IL MODULO "ACCESSO"  OPPURE QUESTO STESSO MODULO ("INPUT SCORES") con varie azioni
	    $torneo = $_POST['torneo'] ?? NULL;
	    $TorneoChiuso = $_POST['torneo_chiuso'] ?? NULL;
		$turno= $_POST['NumTurno'] ?? NULL;
		$NumTavolo = $_POST['tavolo'] ?? NULL;
		//$Punti = $_POST['punti'] ?? NULL;   // sistema di punteggio   
		$Punti = $_POST['score_calcolato'] ?? NULL;    
		
		$boardAtt = $_GET['board'] ?? NULL;
		$TavoloAttualeNS = $_GET['TavAttNS'] ?? NULL;  
		$TavoloAttualeEW = $_GET['TavAttEW'] ?? NULL;    
		$orig = $_GET['orig'] ?? NULL;
		

		$Contratto=  $_POST['Bid'] ?? NULL;	
		$Contro=  $_POST['Contro'] ?? NULL;	
		$prese=  $_POST['Prese'] ?? NULL;	
		$Da=  $_POST['Da'] ?? NULL;	
		$attNum=  $_POST['AttNum'] ?? NULL;	
		$attSeme=  $_POST['AttSeme'] ?? NULL;	
		
		$posContratto= array_search($Da,$Posizioni);
		
//echo "Contratto: ".$Contratto ;
//echo  "<br>";
//echo "torneo: ".$torneo ;
//echo  "<br>";
//echo "orig: ".$orig ;
//echo  "<br>";
//echo "turno= ".$turno ;
//echo  "<br>";
		
		 
		//if(!$NumTavolo)  $NumTavolo = $_GET['tavolo'];
		   
		$MP_NS[1] = $_POST['MP_NS_1'] ?? NULL;
		$MP_NS[2] = $_POST['MP_NS_2'] ?? NULL;
		$MP_NS[3] = $_POST['MP_NS_3'] ?? NULL;
		$MP_NS[4] = $_POST['MP_NS_4'] ?? NULL;
		   
		$MP_EW[1] = $_POST['MP_EW_1'] ?? NULL;
	    $MP_EW[2] = $_POST['MP_EW_2'] ?? NULL;
	    $MP_EW[3] = $_POST['MP_EW_3'] ?? NULL;
	    $MP_EW[4] = $_POST['MP_EW_4'] ?? NULL;
/*	  
echo "torneo: ".$torneo ;
echo  "<br>";	   
echo "tavolo: ".$NumTavolo ;
echo  "<br>";
 */	  


/*
echo "MP_NS_1: ".$MP_NS[1] ;
echo  "<br>";
echo "MP_EW_1: ".$MP_EW[1] ;
echo  "<br>";
echo "MP_NS_2: ".$MP_NS[2] ;
echo  "<br>";
echo "MP_EW_2: ".$MP_EW[2] ;
echo  "<br>";
*/

    }else{
		
		//  AZIONE NULLA: L'ORIGINE E'  "ALTRI RISULTATI", 					(torneo in fase di gioco)
		//  oppure  "controllo Turni"
		//  oppure    "CLASSIFICA", "RISULTATI", "VEDI SCORES", "VEDI BOARDS"  	(torneo finito o chiuso)
        $torneo = $_GET['NomeTorneo'];
   	    $turno= $_GET['NumTurno'];
	    $NumTavolo = $_GET['tavolo'];
	    $orig = $_GET['orig'];
	    $boardAtt = $_GET['boardAtt'];

		$TavoloAttExNS= $_GET['TavAttNS'];	
		$TavoloAttExEW= $_GET['TavAttEW'];
/*	  
echo "TavoloAttExNS: ".$TavoloAttExNS ;
echo  "<br>";	   
echo "TavoloAttExEW: ".$TavoloAttExEW ;
echo  "<br>";
 */	  

		if($boardAtt)  {
			$InputContratto= false;
			$MostraRisultati= true;
			$msg= "FATTA FOTO BOARD: $boardAtt";  
			
		}
	    //$msg= "OK - RITORNO DA CONTRATTO - DATO REGISTRATO";   
    }

	// SE SI ACCEDE A UN TORNEO CHIUSO CAMBIA IL NOME DEL TORNEO
	if (substr($azione, 0, 4) === "Acce") {   // questo per i tablet con traduttore automatico 
	//if($azione=="Accedi") {
	   //$turno= NULL;  // cos� legge quello attuale dal database tornei
	   $msg= "GUARDA I RISULTATI DEL TORNEO";
	   //$azione= "Gioca";
	   $torneo= $TorneoChiuso;
	   // SE TORNEO E' UN NUMERO CERCA IL NOME DEL TORNEO CON ID CORRISPONDENTE
		if(is_numeric($torneo)) {
			$sql= "SELECT * FROM `brdg_cop_tornei` WHERE ID_torneo=".$torneo;
		//echo $sql ;
		//echo  "<br>";
			$dati = $connessione->query($sql);
			if(!$dati) exit("<h1 style=\"background-color:yellow;\"><b><center>Il TORNEO \"". $torneo."\" non esiste</center></b></h1>");
			$row = $dati->fetch_assoc(); 
			$torneo= $row['NomeTorneo'];		
		}
	   
	   
	   $AccediTorneoChiuso= true;
	   //$orig= "AccediTorneoChiuso";
	}   
	if($orig=="risultati") {
	   //$turno= NULL;  // cos� legge quello attuale dal database tornei
	   $msg= "GUARDA I RISULTATI DEL TORNEO";
	   //$azione= "Gioca";
	   $AccediTorneoChiuso= true;
	   //$orig= "AccediTorneoChiuso";
	}   
	
//echo "orig= ".$orig ;
//echo  "<br>";
//echo "turno= ".$turno ;
//echo  "<br>";

	if($torneo[0] == "*") {
		header("Location: TorneoCopListaTornei.php?orig=$azione&torneo=".$torneo);
		exit();
	}
	

	//****************************************************************
   //  CONTROLLA LA PRESENZA DEL TORNEO INSERITO NELLA TABELLA IL TORNEO
   //  RICAVA IL SUO ID IL TURNO CORRENTE e il numero di board per turno     #a0eea0 verde bottiglia
 
   	$sql= "SELECT * FROM `brdg_cop_tornei` WHERE NomeTorneo='".$torneo."'";
//echo $sql ;
//echo  "<br>";
   	$dati = $connessione->query($sql);
	if(!$dati) exit("<h1 style=\"background-color:yellow;\"><b><center>Il TORNEO '". $torneo."' non esiste</center></b></h1>");
   	$row = $dati->fetch_assoc(); 
   	$ID_torneo= $row['ID_torneo'];
   	if (!is_numeric($ID_torneo)) {
		// chiusura della connessione
		$connessione->close();
		//echo"<script>";
		//echo"  alert("Il torneo'". $torneo."' non esiste");";
		//echo"</script>";
		$azione= "<-";
	}
	$BoardsXturno= $row['BoardsXturno'];
	$NumTurni= $row['Turni'];
	$Stato= $row['Stato'];
	$Tipo= $row['Tipo'];
	$Ntavoli= $row['Tavoli'];

	//  RILETTURA  DEL TURNO PER VEDERE SE E' STATO AGGIORNATO
	// 
	$TurnoAttuale= $row['TurnoAttuale'];
//echo "TurnoAttuale= ".$TurnoAttuale ;
//echo  "<br>";
//echo "turno= ".$turno ;
//echo  "<br>";

	//  QUANDO PROVIENE DA ACCESSO
	if($orig=="accesso") {
		$turno= $TurnoAttuale;
	}   


	if($turno==NULL)  {
		$turno= $TurnoAttuale;
	}


	if($turno != $TurnoAttuale)   {
	// SE IL TURNO E' CAMBIATO  E PROVIENE DA "ALTRI RISULTATI" NON AGGIORNA IL TURNO
		//$TurnoCambiato= true;
		$msg="TURNO CONCLUSO: AGGIORNARE";
		if($azione!=NULL)  {
			//$turno= $TurnoAttuale;
			$msg="TURNO CAMBIATO: AGGIORNARE";
		}
	}
	
//**********************************************************************************	
//********************   CONTROLLA SE CI SONO TUTTI I RISULTATI   ******************
//**********************************************************************************	
	
	$sql="SELECT COUNT(*) FROM `brdg_cop_scores` WHERE `torneoID`=$ID_torneo  and `score`IS NULL";
	$dati= $connessione->query($sql);
	if(!$dati) exit("<h1 style=\"background-color:yellow;\"><b><center>Il TORNEO con ID: \"". $ID_torneo."\" non esiste</center></b></h1>");
	$row = $dati->fetch_array();
	$NumScoresTotaliNulli= $row[0];
	if($NumScoresTotaliNulli==0) {
		$TorneoFinito= true;
	}else{
		$TorneoFinito= false;
	}	

//**********************************************************************************	
//******************   CONTROLLI                   *********************************	
//**********************************************************************************	
	                        //INPUT
// TurnoCambiato
// $azione=="Vai"
// $TurnoCompletato 
// $BoardsTavoloGiocate
// $ScoresTuttiNulli
// $TorneoFinito


	                       //OUTPUT
// $NascondiScores
			//quando si preme aggiorna e  sono giocate tutte le boards al tavolo 

// $MostraSpostamentiFatti
			//quando il turno � appena cambiato 
			
// Mostra Spostamenti pulsanti
			//quando sono giocate tutte le boards al tavolo 
			
//**********************************************************************************	
			
	// CONTROLLA SE IL TURNO E' STATO COMPLETATO
	$sql="SELECT COUNT(*) FROM `brdg_cop_scores` WHERE `torneoID`=$ID_torneo and `turno`=$turno and `score`IS NULL";
//echo $sql ;
//echo  "<br>";
	$dati= $connessione->query($sql);
	$row = $dati->fetch_array();
	$NumScoresNulli= $row[0];
	if($NumScoresNulli == 0)  {
		// TURNO COMPLETATO 
		$TurnoCompletato= true;
	}else{
		// TURNO NON COMPLETATO 
		$TurnoCompletato= false;
	}

	if($NumScoresNulli == $Ntavoli*$BoardsXturno)  {
		$TurnoInizio= true;
	}else{
		$TurnoInizio= false;
	}



//**********************************************************************************	
//  ------------------ OPERAZIONI COMUNI  -----------------
//**********************************************************************************	


     //******************************
	// CERCA LE COPPIE del TAVOLO 
     //******************************

	$sql="SELECT * FROM `brdg_cop_scores` WHERE  torneoID=$ID_torneo AND turno=$turno AND tavolo=$NumTavolo  ORDER BY id" ;	
//echo $sql ;
//echo  "<br>";
	$dati= $connessione->query($sql); 
	if(!$dati) exit("<b><center>Errore ricerca coppia nel turno - avv</center></b>");
	
	$row = $dati->fetch_assoc();
	if (!$row) {
		$connessione->close();		
  		exit( "<b><center>TORNEO o TURNO NON CORRETTI</center></b>");
	}
	$NumCoppiaNS= $row['coppiaNS'];	
	$NumCoppiaEW= $row['coppiaEW'];	
	
	// CERCA I BOARDS DEL TURNO  	
	$kboard=0;
	while($row)  {
		$kboard++;	
		$Boards[$kboard]=  $row['board'];
		$row = $dati->fetch_assoc();	
	}
	if ($kboard!=$BoardsXturno) {
		$connessione->close();		
		exit( "<b><center>NON TORNA IL NUM DEI BOARRDS</center></b>");
	}
     //******************************
	//  CERCA I COMPONENTI DELLA COPPIA  IN  NS
     //******************************
	$sql="SELECT * FROM `brdg_cop_coppie` WHERE `torneoID`=$ID_torneo  AND `coppiaID`= $NumCoppiaNS" ;	
	$dati = $connessione->query($sql);
	if(!$dati) exit("<b><center>Errore ricerca Coppia NS</center></b>");
	$row = $dati->fetch_assoc();		
	$NumNomeN= $row['nome1ID'];
	$NumNomeS= $row['nome2ID'];		
	//$NomeID= $NumNome1;	
	// CERCA I NOMI DI QUESTA COPPIA
	$dati= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$NumNomeN"); 		
	$row = $dati->fetch_assoc();
	$NomeN= $row['nome'];		// *******
	
	$dati= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$NumNomeS"); 		
	$row = $dati->fetch_assoc();
	$NomeS= $row['nome'];		// *******

     //******************************
	// CERCA I COMPONENTI DELLA COPPIA IN EW
     //******************************
	$sql="SELECT * FROM `brdg_cop_coppie` WHERE `torneoID`=$ID_torneo  AND `coppiaID`= $NumCoppiaEW" ;
	$dati = $connessione->query($sql);
	if(!$dati) exit("<b><center>Errore ricerca Coppia EW</center></b>");
	$row = $dati->fetch_assoc(); 
	$NumNomeE= $row['nome1ID'];
	$NumNomeW= $row['nome2ID'];	
	// CERCA I NOMI DI QUESTA COPPIA
	$dati= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$NumNomeE"); 		
	$row = $dati->fetch_assoc();
	$NomeE= $row['nome'];		// *******

	$dati= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$NumNomeW"); 		
	$row = $dati->fetch_assoc();
	$NomeW= $row['nome'];		// *******

		
	//   accorcia i nomi a 10 caratteri
	$NickN= substr($NomeN,0,16);
	$NickS= substr($NomeS,0,16);
	$NickE= substr($NomeE,0,16);
	$NickW= substr($NomeW,0,16);		

    //******************************
	// CERCA GLI SPOSTAMENTI PER IL TURNO SUCCESSIVO
     //******************************
	$turnoSuc=$turno+1;	
	if($turnoSuc > $NumTurni) {
		$tavoloSucNS= '--';			
		$tavoloSucEW= '--';			
	}else{
		$sql="SELECT * FROM `brdg_cop_scores` WHERE  torneoID=$ID_torneo AND turno=$turnoSuc AND (coppiaNS=$NumCoppiaNS or coppiaEW=$NumCoppiaNS) ORDER BY id" ;	
//echo $sql ;
//echo  "<br>";
		$dati= $connessione->query($sql); 
		if(!$dati) exit("<b><center>Errore ricerca coppia nel turno successivo</center></b>");
		$row = $dati->fetch_assoc();
		if (!$row) {
			$connessione->close();		
			exit( "<b><center>TORNEO o TURNO NON CORRETTI</center></b>");
		}
		$tavoloSucNS= $row['tavolo'];
		
		$CoppiaSucNS= $row['coppiaNS'];
		if($CoppiaSucNS==$NumCoppiaNS)  {
			$PosSucNS= "NS";
		}else{
			$PosSucNS= "EW";		
		}
		// RICAVA LE BOARDS SUCCESSIVE PER LA COPPIA NS
		$kboard=0;
		while($row)  {
			$kboard++;	
			$BoardsSucNS[$kboard]=  $row['board'];
			$row = $dati->fetch_assoc();	
		}		
		
		$sql="SELECT * FROM `brdg_cop_scores` WHERE  torneoID=$ID_torneo AND turno=$turnoSuc AND (coppiaNS=$NumCoppiaEW or coppiaEW=$NumCoppiaEW) ORDER BY id" ;	
//echo $sql ;
//echo  "<br>";
		$dati= $connessione->query($sql); 
		if(!$dati) exit("<b><center>Errore ricerca coppia nel turno successivo</center></b>");
		$row = $dati->fetch_assoc();
		if (!$row) {
			$connessione->close();		
			exit( "<b><center>TORNEO o TURNO NON CORRETTI</center></b>");
		}
		$tavoloSucEW= $row['tavolo'];
		
		$CoppiaSucNS= $row['coppiaNS'];
		if($CoppiaSucNS==$NumCoppiaEW)  {
			$PosSucEW= "NS";			
		}else{
			$PosSucEW= "EW";
		}
		// RICAVA LE BOARDS SUCCESSIVE PER LA COPPIA EW
		$kboard=0;
		while($row)  {
			$kboard++;	
			$BoardsSucEW[$kboard]=  $row['board'];
			$row = $dati->fetch_assoc();	
		}		
	}
/*	
    //  VALORI NON UTILIZZATI PER ORA
echo "PosSucNS: ".$PosSucNS;
echo  "<br>";
echo "PosSucEW: ".$PosSucEW ;
echo  "<br>";
*/


	//*****************************************************************************
		// Costruisce il vettore ArrayBoards   
	//*****************************************************************************
	$sql= "SELECT * FROM `brdg_cop_scores` WHERE  torneoID=$ID_torneo AND turno = $turno  AND coppiaNS=$NumCoppiaNS ORDER BY id";		
//echo $sql ;
//echo  "<br>";
	$dati= $connessione->query($sql); 
	if(!$dati) {
		$connessione->close();
		exit("<b><center>Errore ricerca boards</center></b>");				
	}
	$row = $dati->fetch_assoc();
	$kboard=1;
	
	while($row)  {
		$Board= $row['board'];
		$ArrayBoards[$kboard]= 	$Board;
		$row = $dati->fetch_assoc();
		$kboard++;
	}	

//**********************************************************************************	
//**********************************************************************************	

//  AZIONI  PROVENIENTI DA ACCESSO
	if($azione=="  Vai  ") {
	   //$turno= NULL;  // cos� legge quello attuale dal database tornei
	   $msg= "TOCCA IL N. DEL BOARD PER INSERIRE IL CONTRATTO";
	   $azione= "VaiGioca";
	}   

	if($azione=="Home") {
	   header("Location: \..");
	   exit();
	} 
		

//  AZIONI PROVENIENTI DALL INTERNO
	if($azione=="I" && $MP_NS[1]==NULL && $MP_EW[1]==NULL) {
	   $msg= "IL RISULTATO DEL BOARD 1 E' VUOTO";
	   $azione= "Gioca";
	}
	
	if($azione=="Aggiorna il turno") {
		// MODIFICA RECENTE PER GENERARE IL NUOVO TURNO CON QUESTA AZIONE
		// ANZICHE GENERARLO AUTOMATICAMENTE
/*
echo "turno: ".$turno;
echo  "<br>";
echo "TurnoCompletato: ".$TurnoCompletato ;
echo  "<br>";
*/
		if($TorneoFinito)  {
			echo"<script>";
			echo"  alert(\"IL TORNEO E' FINITO\");";
			echo"</script>";			
		}else{
			if($TurnoCompletato)  {
				// CI SONO TUTTI I RISULTATI DEL TURNO
				// GENERA IL TURNO SUCCESSIVO
				if($turno < $NumTurni) {
					// AGGIORNA IL TURNO ATTUALE
					$turno= $turno+1;
										
					//$turno;
					$dati = $connessione->query("UPDATE `brdg_cop_tornei` SET `TurnoAttuale`=".$turno." WHERE `ID_torneo`=".$ID_torneo);	
					//$msg= "ATTENZIONE - TURNO TERMINATO -  ";


					// AGGIORNA LE COPPIE
					 //******************************
					// CERCA LE COPPIE del TAVOLO 
					 //******************************

					$sql="SELECT * FROM `brdg_cop_scores` WHERE  torneoID=$ID_torneo AND turno=$turno AND tavolo=$NumTavolo  ORDER BY id" ;	
//echo $sql ;
//echo  "<br>";
					$dati= $connessione->query($sql); 
					if(!$dati) exit("<b><center>Errore ricerca coppia nel turno - avv</center></b>");
					
					$row = $dati->fetch_assoc();
					if (!$row) {
						$connessione->close();		
						exit( "<b><center>TORNEO o TURNO NON CORRETTI</center></b>");
					}
					$NumCoppiaNS= $row['coppiaNS'];	
					$NumCoppiaEW= $row['coppiaEW'];	
					
					// CERCA I BOARDS DEL TURNO  	
					$kboard=0;
					while($row)  {
						$kboard++;	
						$Boards[$kboard]=  $row['board'];
						$row = $dati->fetch_assoc();	
					}
					if ($kboard!=$BoardsXturno) {
						$connessione->close();		
						exit( "<b><center>NON TORNA IL NUM DEI BOARRDS</center></b>");
					}
					 //******************************
					//  CERCA I COMPONENTI DELLA COPPIA  IN  NS
					 //******************************
					$sql="SELECT * FROM `brdg_cop_coppie` WHERE `torneoID`=$ID_torneo  AND `coppiaID`= $NumCoppiaNS" ;	
					$dati = $connessione->query($sql);
					if(!$dati) exit("<b><center>Errore ricerca Coppia NS</center></b>");
					$row = $dati->fetch_assoc();		
					$NumNomeN= $row['nome1ID'];
					$NumNomeS= $row['nome2ID'];		
					//$NomeID= $NumNome1;	
					// CERCA I NOMI DI QUESTA COPPIA
					$dati= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$NumNomeN"); 		
					$row = $dati->fetch_assoc();
					$NomeN= $row['nome'];		// *******
					
					$dati= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$NumNomeS"); 		
					$row = $dati->fetch_assoc();
					$NomeS= $row['nome'];		// *******

					 //******************************
					// CERCA I COMPONENTI DELLA COPPIA IN EW
					 //******************************
					$sql="SELECT * FROM `brdg_cop_coppie` WHERE `torneoID`=$ID_torneo  AND `coppiaID`= $NumCoppiaEW" ;
					$dati = $connessione->query($sql);
					if(!$dati) exit("<b><center>Errore ricerca Coppia EW</center></b>");
					$row = $dati->fetch_assoc(); 
					$NumNomeE= $row['nome1ID'];
					$NumNomeW= $row['nome2ID'];	
					// CERCA I NOMI DI QUESTA COPPIA
					$dati= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$NumNomeE"); 		
					$row = $dati->fetch_assoc();
					$NomeE= $row['nome'];		// *******

					$dati= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$NumNomeW"); 		
					$row = $dati->fetch_assoc();
					$NomeW= $row['nome'];		// *******

						
					//   accorcia i nomi a 16 caratteri
					$NickN= substr($NomeN,0,16);
					$NickS= substr($NomeS,0,16);
					$NickE= substr($NomeE,0,16);
					$NickW= substr($NomeW,0,16);		
				
					
				}else{
					$msg= "ATTENZIONE - TORNEO FINITO - AVVERTIRE ";
					$TorneoFinito= true;
				}
				// 	MOSTRA GLI SPOSTAMENTI AVVENUTI ANCHE DOPO IL CAMBIO TURNO
				//  NEL CASO LA COPPIA NON SI SIA ANCORA SPOSTATA
				$TavoloAttExNS= $tavoloSucNS;	
				$TavoloAttExEW= $tavoloSucEW;
				
				$MostraSpostamentiFatti= true;
				$msg= "NUOVO TURNO";				
			}else{
				if($TurnoInizio)  {
					$msg= "NUOVO-TURNO";
					$MostraSpostamentiFatti= true;
					$NascondiScores= false;
				}else{
					$msg= "TURNO NON COMPLETATO";
					//echo"<script>";
					//echo"  alert(\"QUALCUNO STA ANCORA GIOCANDO\");";
					//echo"</script>";				
					$MostraSpostamentiFatti= false;
					$NascondiScores= true;
				}
			}
		}
		$boardAtt=0;
		$azione= "Gioca";
		//$ClickAggiorna= true;
	}   

//echo "NumScoresNulli: ".$NumScoresNulli;
//echo  "<br>";
//echo "Ntavoli: ".$Ntavoli ;
//echo  "<br>";
//echo "TurnoInizio: ".$TurnoInizio ;
//echo  "<br>";


 
//**********************************************************************************	
//  AZIONI PROVENIENTI DAL MODULO CONTRATTO
//**********************************************************************************	
 	if($azione=="TORNA") {
		$boardAtt=0;
	} 
/*	
	if($azione=="Calcola") {
		$InputContratto= true;	
	}
*/
	//if(!$turno) $turno= $TurnoAttuale;
//echo "turno attuale: ". $turno ;
//echo  "<br>";	
//echo "BoardsXturno: ". $BoardsXturno ;
//echo  "<br>";	
	
	if ($turno<1) {
		//$connessione->close();		
		echo"<script>";
		echo"  alert(\"QUESTO TURNO NON ESISTE O NON E' PRONTO\");";
		echo"</script>";				
	}

//echo "ID_torneo: ".$ID_torneo ;
//echo  "<br>";
	//****************************************************************
     //******************************
	// DECIDE SE MOSTRARE LA MASCHERA DI INPUT CONTRATTO
     //******************************
	if(is_numeric($azione) )  {
		$InputContratto= true;
		$MostraSpostamentiFatti= false;
		$TurnoInizio= false;
		$boardSel = $azione;
		if($boardAtt)  {
			if($boardAtt!=$boardSel){
				$boardAtt= $boardSel;
			}	
		}else{
			$boardAtt= $boardSel;
		}
		
		// LEGGE IL CONTRATTO NEL DATABASE
		$dati= $connessione->query("SELECT * FROM `brdg_cop_scores` WHERE `torneoID`=$ID_torneo AND  `turno`=$turno AND  `board`=$boardAtt AND  `tavolo`=$NumTavolo");
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
			$Da= "--";
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
	}
	if($boardAtt){
		$iBoard= ($boardAtt-1)%16;
		$VulnNS= $ZonaNS[$iBoard];
		$VulnEW= $ZonaEW[$iBoard];
		
		$jBoard= ($boardAtt-1)%4;
		$Dealer= $Posizioni[$jBoard];

		if($VulnNS==false && $VulnEW==false) {
			$Vulnerability= "None";
		}else if($VulnNS==true && $VulnEW==true) {
			$Vulnerability= "All";			
		}else if($VulnNS==true && $VulnEW==false) {
			$Vulnerability= "NS";			
		}else if($VulnNS==false && $VulnEW==true) {
			$Vulnerability= "EW";			
		}
	}
	/*
echo "VulnNS=  ".$VulnNS ;
echo  "<br>";
echo "VulnEW=  ".$VulnEW ;
echo  "<br>";
echo "Vulnerability=  ".$Vulnerability ;
echo  "<br>";
exit();
*/
	//  **************************************************
	//  AZIONI ATTIVE A FINE TORNEO
	//  **************************************************
	if($azione=="Classifica"){
		header("Location: TorneoCopClassifica.php?torneo=".$torneo."&turno=".$turno);
		exit();
	}
		
	if($azione=="Risultati"){
		header("Location: TorneoCopDettagli_1.php?torneo=".$torneo."&turno=".$turno);
		exit();
	}

	if($azione=="Scores"){
		header("Location: TorneoCopDettagli_2.php?torneo=".$torneo."&turno=".$turno);
		exit();
	}

	if($azione=="Vedi Foto"){
		  header("Location: TorneoCopVediBoardsFree.php?NomeTorneo=$torneo&turno=$turno&tavolo=$NumTavolo");
		  exit();
	}

	if($azione=="Analisi"){
		// Codice PHP che esegue le operazioni...
		  header("Location: TorneoCopListaFilesPBN.php?Torneo=".$torneo);
		  exit();
	}

	if($azione=="Giocatori"){
		header("Location: TorneoCopListaCompleta.php?torneo=".$torneo);
		exit();
	}
	
	if($azione=="Tempi"){
		  header("Location: TorneoCopControlloTempi.php?torneo=".$torneo."&orig=scores");
		  exit();
	}
	//echo "orig=".$orig ;
	//echo  "<br>";

	if($azione=="<-"){
		if($orig == "admin")  {
			header("Location: TorneoCopControllo.php?torneo=".$torneo."&turno=".$turno);
			exit();
		}else{	
			header("Location: TorneoCopAccessoContrattoTav.php?Torneo=$torneo");
			exit();
		}
	}
	if($azione=="?"){
		$pdf_file = './doc/Webridge.pdf';
		echo "<script>window.location.href =\"".$pdf_file."\"</script>";
		//echo '<a href="' . $pdf_file . '" target="_blank">Apri il PDF</a>';
	}

//**********************************************************************
//  QUI SI INSEERISCE IL PUNTEGGIO NEL DATABASE
//********************************************************************** 
	if($azione=="OK") {

		if($Stato==1) {
			echo"<script>";
			echo"  alert(\"IL TORNEO RISULTA CHIUSO\");";
			echo"</script>";
			goto noOK;			
		}
		if($TurnoCompletato) {
			echo"<script>";
			echo"  alert(\"QUESTO TURNO E' COMPLETO: SOLO L' AMMINISTRATORE PUO' MODIFICARNE I RISULTATI\");";
			echo"</script>";
			goto noOK;			
		}
		
		if($Contratto=="A")  {
			// Cerca le caselle del board attuale			
			for($i=1; $i<=$BoardsXturno ;$i++) {
				if($boardAtt == $ArrayBoards[$i]) break;
			}
			$ValoreNS= $MP_NS[$i];
			$ValoreEW= $MP_EW[$i];
/*
echo "i=  ".$i ;
echo  "<br>";
echo "ValoreNS=  ".$ValoreNS ;
echo  "<br>";
echo "ValoreEW=  ".$ValoreEW ;
echo  "<br>";
exit();
*/
			
			//Controlla la presenza di numeri  nelle caselle del board attuale
			if(!is_numeric($ValoreNS) || !is_numeric($ValoreEW) ){
				echo"<script>";
				echo"  alert(\"INSERIRE IL PUNTEGGIO ARBITRALE\");";
				echo"</script>";
				$InputContratto= true;			
				goto noOK;
			}
			else {
				// Calcola il punteggio convenzionale arbitrale
				$Da= "-";
				$Punti= 10000 + $ValoreNS*100 + $ValoreEW;			
			}
		}
		if($Contratto=="R")  {
			$Punti= -1;
			$Da= "-";
		}
		if($Contratto=="P")  {
			$Punti= 0;
			$Da= "-";
		}

//echo "Punti=  ".$Punti ;
//echo  "<br>";

		if($Punti===NULL) {
			echo"<script>";
			echo"  alert(\"CALCOLARE IL PUNTEGGIO\");";
			echo"</script>";
			$InputContratto= true;			
			goto noOK;			
		}
/*      SONO GIA' DEFINITI
   		$Contratto=  $_POST['Bid'];	
   		$Contro=  $_POST['Contro'];	
   		$prese=  $_POST['Prese'];	
   		$Da=  $_POST['Da'];	
   		$attNum=  $_POST['AttNum'];	
   		$attSeme=  $_POST['AttSeme'];	
*/	
		$Contratto_= $Contratto;
		if($Contro == '!')   $Contratto_= $Contratto."!";
		if($Contro == '!!')  $Contratto_= $Contratto."!!";
		//$Contratto_= $Contratto.$Contro;
		$Attacco= $attNum.$attSeme;


		// INSERISCE IL CONTRATTO NEL DATABASE
		$Secondi= time();
		$sql="UPDATE `brdg_cop_scores` SET `score`=$Punti,`Contratto`='$Contratto_',`GiocatoDa`='$Da',`Prese`='$prese',`Attacco`='$Attacco',`secondi`='$Secondi'
					 WHERE `torneoID`=$ID_torneo and `turno`= $turno and `board` = $boardAtt and `tavolo` = $NumTavolo ";
//echo $sql ;
//echo  "<br>";
		$esitoOK= $connessione->query($sql);

		// RITORNA A FORM PUNTI	
		if($esitoOK)  {
			$InputContratto= false;
			$MostraRisultati= true;
			$msg= "OK: REGISTRATO BOARD: $boardAtt"; 

			// CONTROLLA SE IL TURNO E' COMPLETATO
			$sql="SELECT COUNT(*) FROM `brdg_cop_scores` WHERE `torneoID`=$ID_torneo and `turno`=$turno and `score`IS NULL";
			$dati= $connessione->query($sql);
			$row = $dati->fetch_array();
			$NumScoresNulli= $row[0];
			if($NumScoresNulli == 0)  {
				$TurnoCompletato= true;
			}else{
				$TurnoCompletato= false;
			}

			
		}else{
			$msg= "BOARD: $boardAtt NON REGISTRATO"; 
			
		}
	}
noOK:	
//  *****************************************************************************
    if($azione=="FOTO") {
			
		// VA alla foto			
		// Controlla l' esistenza della foto
		if($boardAtt<10) {
			$board_ = "0".$boardAtt;
		}else{
			$board_ = $boardAtt;			
		}
		//echo$home_archive."/upload/tornei/".$torneo."/N_".$board_.".jpeg";	
		//$AA=file_exists($home_archive."/upload/tornei/".$torneo."/N_".$board_.".jpeg");
		//echo"AA= ".$AA;	
/*		
		if(file_exists($home_archive."/upload/tornei/".$torneo."/N_".$board_.".jpeg") ){
			// ATTENZIONE	QUESTO SCRIPT NON PUO FUNZIONARE	
     		echo"<script>";	
     		echo" alert(\"ATTENZIONE: LA FOTO DELLA BOARD N.".$boardAtt." ESISTE GIA'\");";
			//echo "location.href='$home_archive."/Gestfoto.php?torneo=$torneo&turno=$turno&board=$boardAtt&tavolo=$NumTavolo';";
     		echo"</script>";
			header("Location: $home_archive."/Gestfoto.php?torneo=".$torneo."&turno=".$turno."&board=".$boardAtt."&tavolo=".$NumTavolo);	
			//echo "<script type='text/javascript'>";
			//echo "location.href='$home_archive."/Gestfoto.php?torneo=$torneo&turno=$turno&board=$boardAtt&tavolo=$NumTavolo';";
			//echo "</script>";
		}else{
			header("Location: $home_archive."/Gestfoto.php?torneo=".$torneo."&turno=".$turno."&board=".$boardAtt."&tavolo=".$NumTavolo);	
		}
*/

		if(file_exists($home_archive."/upload/tornei/".$torneo."/N_".$board_.".jpeg") ){
			$url = $home_archive."/Gestfoto.php?torneo=".$torneo."&turno=".$turno."&board=".$boardAtt."&tavolo=".$NumTavolo;
			
			echo "<script>";
			echo "alert('ATTENZIONE: LA FOTO DELLA BOARD N.".$boardAtt." ESISTE GIA\'');";
			echo "window.location.href = '".$url."';"; // Reindirizzamento via JS
			echo "</script>";
			exit();
		} else {
			header("Location:".$home_archive."/Gestfoto.php?torneo=".$torneo."&turno=".$turno."&board=".$boardAtt."&tavolo=".$NumTavolo);
			exit();
		}

	}
//  *****************************************************************************
    if($azione=="EDITA MANO") {
			
		// VA a input manuale		
		// Controlla l' esistenza del file

		if($boardAtt<10) {
			$board_ = "0".$boardAtt;
		}else{
			$board_ = $boardAtt;			
		}

		if(file_exists($home_archive."/upload/tornei/".$torneo."/Board_".$board_.".pbn") ){
			$url = $home_archive."/scrivi_pbn.php?torneo=".$torneo."&turno=".$turno."&board=".$boardAtt."&tavolo=".$NumTavolo."&dealer=".$Dealer."&vulner=".$Vulnerability;
			echo "<script>";
			echo "alert('ATTENZIONE: IL FILE PBN DEL BOARD N.".$boardAtt." ESISTE GIA\'');";
			echo "window.location.href = '".$url."';"; // Reindirizzamento via JS
			echo "</script>";
			exit();
		} else {
			header("Location:".$home_archive."/scrivi_pbn.php?torneo=".$torneo."&turno=".$turno."&board=".$boardAtt."&tavolo=".$NumTavolo."&dealer=".$Dealer."&vulner=".$Vulnerability);
			exit();
		}

	}

		/*
		*/


//  *****************************************************************************

if($azione=="Vedi altri risultati") {
//echo "TurnoInizio   ". $TurnoInizio ;
//echo  "<br>";	
		if($TurnoInizio) {
			$turno_1= $turno-1;
		}else{
			$turno_1= $turno;			
		}	
		//header("Location: TorneoCopAltriTav.php?torneoID=$ID_torneo&board=$boardAtt&torneo=$torneo&turno=$turno&tavolo=$NumTavolo");
		header("Location: TorneoCopAltriTav.php?torneoID=$ID_torneo&board=$boardAtt&torneo=$torneo&turno=$turno_1&tavolo=$NumTavolo&TavAttNS=$TavoloAttualeNS&TavAttEW=$TavoloAttualeEW");
		exit();
	}

//  *****************************************************************************
    if($azione=="Vedi foto") {

			header("Location:".$home_archive."/VediFoto.php?torneo=$torneo&board=$boardAtt&turno=$turno&tavolo=$NumTavolo");
			exit();
			//  *****************************************************************************
	}
//  *****************************************************************************


// -----------------   I N V I O   I N I Z I O   ---------------------------

	if($azione=="I" && $Stato==0){   // torneo aperto
		 
		for($kboard=1; $kboard<=$BoardsXturno ;$kboard++) {	
			// 	    INSERISCE I RISULTATI NEL DATABASE
			//  P == all-pass --> inserisce 0
			//  R == riposo   --> inserisce -1
			if(strtoupper($MP_NS[$kboard])=="R")   {
				$MP_= -1;
			}else if(strtoupper($MP_NS[$kboard])=="P") {
				$MP_= 0;	
			}else if(is_numeric($MP_NS[$kboard]) && is_numeric($MP_EW[$kboard]))   {
				$MP_=10000+$MP_NS[$kboard]*100+$MP_EW[$kboard];
			}else if(is_numeric($MP_NS[$kboard]))   {
				$MP_=$MP_NS[$kboard];
			}else if(is_numeric($MP_EW[$kboard]))  {
				$MP_=-$MP_EW[$kboard];
			}else if(is_null($MP_NS[$kboard])){
				$MP_= 1;	
			}else if(empty($MP_NS[$kboard])){
				$MP_= 1;	
			}else if($MP_NS[$kboard]==""){
				$MP_= 1;	
			}else{
				$MP_= 1;	
			}

			$Board_= $Boards[$kboard];

			if($MP_== 1)  {
				$sql= "UPDATE brdg_cop_scores SET `score`= NULL WHERE coppiaNS=$NumCoppiaNS 			   
											 AND torneoID=\"".$ID_torneo."\" AND turno=\"".$turno."\" AND board=$Board_ ";	
			}else{
				$sql= "UPDATE brdg_cop_scores SET `score`= $MP_ WHERE coppiaNS=$NumCoppiaNS  			   
											 AND torneoID=\"".$ID_torneo."\" AND turno=\"".$turno."\" AND board=$Board_ ";	
			}		

			$esitoOK= $connessione->query($sql);
		}  	

		if (!$esitoOK) {
			 echo "Errore della query:: " . $connessione->error . ".";
			 $connessione->close();
		}else{ 
			 $msg= "OK - RISULTATO INSERITO CORRETTAMENTE";		 
		}

	}else if($azione=="I" && $Stato==1){   // torneo  chiuso
		$msg= "IL TORNEO RISULTA CHIUSO";
	}

//---------------- I N V I O  F I N E --------------------

//--------------------------------------------------------------------------------------------
// DOPO L'INSERIMENTO, SE IL TURNO E' COMPLETATO, GENERA AUTOMATICAMENTE IL TURNO SUCCESSIVO
//       !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// MODIFICATO: ADESSO PER GENERARE IL TURNO NUOVO E' NECESSARIO PREMERE "AGGIORNA TURNO"
//--------------------------------------------------------------------------------------------
//	if(substr($msg,0,2) == "OK")  {	
/*
	if($azione == "OK" || $azione == "I")  {	
		$sql="SELECT COUNT(*) FROM `brdg_cop_scores` WHERE `torneoID`=$ID_torneo and `turno`=$turno and `score`IS NULL";
		$dati= $connessione->query($sql);
		$row = $dati->fetch_array();
		$NumScoresNulli= $row[0];
//echo"NumScoresNulli ----- ".$NumScoresNulli;
//echo"<br>";
		if($NumScoresNulli == 0) {
			// CI SONO TUTTI I RISULTATI DEL TURNO
			if($turno < $NumTurni) {
				// AGGIORNA IL TURNO ATTUALE
				$turnoFuturo= $turno+1;
				//$turno;
				$dati = $connessione->query("UPDATE `brdg_cop_tornei` SET `TurnoAttuale`=".$turnoFuturo." WHERE `ID_torneo`=".$ID_torneo);	
				$msg= "ATTENZIONE - TURNO TERMINATO - AVVERTIRE ";
			}else{
				$msg= "ATTENZIONE - TORNEO FINITO - AVVERTIRE ";
				$TorneoFinito= true;
			}
			//  EMETTE UN SUONO DI AVVERTIMENTO
			echo"<script>
				var audio = new Audio('beep-1.wav');
				audio.play();
				</script>";	  
		}	
	}
*/


//--------------------------------------------------------------------------------------------
// DOPO L'INSERIMENTO, SE IL TURNO E' COMPLETATO, AVVERTE CON MESSAGGIO E SUONO
//--------------------------------------------------------------------------------------------

//echo"azione ----- ".$azione;
//echo"<br>";
//echo"TurnoCompletato ----- ".$TurnoCompletato;
//echo"<br>";
	if(($azione == "OK" || $azione == "I") && $TurnoCompletato)  {	
		// CI SONO TUTTI I RISULTATI DEL TURNO
//echo"passa ----- ".$TurnoCompletato;
//echo"<br>";
		if($turno < $NumTurni) {
			$msg= "ATTENZIONE - TURNO TERMINATO - AVVERTIRE ";
			//$MostraSpostamentiFatti
		}else{
			$msg= "ATTENZIONE - TORNEO FINITO - AVVERTIRE ";
			$TorneoFinito= true;
		}
		
		//  EMETTE UN SUONO DI AVVERTIMENTO
/*
		<script>
			var sound = new Audio();
			sound.autoplay = false;
			sound.muted = false;
			sound.volume = 1.0;
			sound.src = navigator.userAgent.match(/Firefox/) ? 'beep1.ogg' : 'beep-1.wav';
			sound.play();
		</script>
*/
	}


//--------------------------------------------------------------------------------------------
// DOPO L'INSERIMENTO, CONTROLLA SE I RISULTATI DEL TAVOLO SONO COMPLETATI, 
//--------------------------------------------------------------------------------------------
	$sql="SELECT COUNT(*) FROM `brdg_cop_scores` WHERE `torneoID`=$ID_torneo and `turno`=$turno and `tavolo`=$NumTavolo and `score`IS NULL";
	$dati= $connessione->query($sql);
	$row = $dati->fetch_array();
	$NumScoresNulli= $row[0];
	if($NumScoresNulli == 0) {
		$BoardsTavoloGiocate= true;
	}else{
		$BoardsTavoloGiocate= false;
	}
/*
echo"NumScoresNulli ----- ".$NumScoresNulli;
echo"<br>";
echo"BoardsTavoloGiocate ----- ".$BoardsTavoloGiocate;
echo"<br>";

	if($NumScoresNulli == $BoardsXturno) {
		$ScoresTuttiNulli= true;
	}else{
		$ScoresTuttiNulli= false;
	}
*/
	if($BoardsTavoloGiocate  ) {
		// SE AL TAVOLO LE MANI SONO TUTTE GIOCATE, VIENE DEFINITO IL TAVOLO DEL NUOVO TURNO PER LE COPPIE
		$TavoloAttualeNS= $tavoloSucNS;
		$TavoloAttualeEW= $tavoloSucEW;
	}

/*
	if($orig=="AltriTav" && $TurnoInizio) {
	   $turno= $turno-1;  // 
	}   

	$orig= "scores";
*/	
/*
echo "boardAtt: ".$boardAtt ;
echo  "<br>";
echo "Punti: ".$Punti ;
echo  "<br>";
echo "orig: ".$orig ;
echo  "<br>";
 */
//   ********************************************************************************************	
	  //   @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@        F O R M         @@@@@@@@@@@@@@@@@@@@@@@@@@@@
//  ***********************************************************************************************

//   ********************************************************************************************	
	  //   @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@        F O R M         @@@@@@@@@@@@@@@@@@@@@@@@@@@@
//  ***********************************************************************************************
//   ********************************************************************************************	
	  //   @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@        F O R M         @@@@@@@@@@@@@@@@@@@@@@@@@@@@
//  ***********************************************************************************************
//************************************************************************************** */
//  TERMINARE QUI IL PHP
//****************************************************************************************** */
// </body>
?>	
	<!--  QUI ERA LO SCRIPT PER FAR BLINCARE IL TESTO  -->
			

    <!-- MOSTRA LA FORM DI IMMISSIONE    a0eea0   6b8e23   fdf5e6
	// INPUT TORNEO  TURNO  E NUMERO DI COPPIA 		 bgcolor="#eee8aa"	<td style="width: 8%;"  class="no-border" style="text-align:right;"><label><h4>Tavolo</h4></label></td>  
	 -->


<div class="container-tabelle">
	<!--" class="tabella-pesca" width="100" align="center" border="1"> -->
  
    <!--" class="form-container">-->

	<?php $orig= "self"; ?>

	<form <?php echo"action=\"TorneoCopScoresContrattoTav.php?board=$boardAtt&punti=$Punti&TavAttNS=$TavoloAttualeNS&TavAttEW=$TavoloAttualeEW&orig=$orig\"  method=\"post\" "; ?> >
	
	<div class="table-wrapper">

		<!-- PRIMA INTESTAZIONE  : TORNEO, TAVOLO -->
		<table class= "tabella-pulita" align="center" style="width: 100%; table-layout: fixed; border-collapse: collapse;"> 

			<tr align="center"> 
				<td style="width: 20%; padding: 0px;" class="no-border">
					<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%;">
						<input style="font-size:25px; width: 100%; box-sizing: border-box; text-align: center;" <?php echo "name='torneo' value='$torneo'"; ?>>
					</div>
				</td>
				
				<td style="width: 10%;">
					<div class="timer-container">
						<div class="timer-display" id="timer">00:00</div>
					</div>
				</td> 
				<td style="width: 10%;" class="no-border">
					<div style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 5px; width: 100%;">
						<label for="input_1" style="font-size: 18px; font-weight: bold; white-space: nowrap;">Tavolo</label>
						<input style="text-align:center; font-size:36px; width: 45px; box-sizing: border-box;" name="tavolo" id="input_1" value="<?php echo $NumTavolo; ?>" readonly>
					</div>
				</td>
			</tr>
		</table>

		<!-- SECONDA INTESTAZIONE :  TURNO;  AGGIORNA TURNO  -->				
		<table class= "tabella-pulita" style="margin: 0 auto; border-collapse: collapse; ">
			<tr>
				<td class="no-border" style="padding: 5px; text-align: center; width: 40%;">
					<div style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 2px;">
						<label for="input_2" style="font-size: 18px; font-weight: bold; white-space: nowrap;">Turno</label>
						
						<input style="text-align: center; font-size: 24px; width: 50px; " 
							name="NumTurno" 
							id="input_2" 
							type="number" 
							min="1"
							value="<?php echo htmlspecialchars($turno); ?>" readonly>
						
						<span style="font-size: 22px; white-space: nowrap;">/<?php echo htmlspecialchars($NumTurni); ?></span>
					</div>
				</td>
				
				<td class="no-border" style="padding: 0px; text-align: center;">
					<input style="background-color: yellow; font-size: 22px; cursor: pointer; padding: 2px 5px;" 
						name="action" 
						type="submit" 
						value="Aggiorna il turno">
				</td>
			</tr>
		</table>   
<?php   
    //*****************************************************************************
    if($AccediTorneoChiuso) goto risultati;
    //*****************************************************************************
    //  MOSTRA POSIZIONE E NOMI DEI GIOCATORI  style=\"HEIGHT: 10px\"
    //*****************************************************************************
    //  CASO DI TORNEO MITCHELL
    if($Tipo==2) {
        $NumCoppiaEW_= $NumCoppiaEW-$Ntavoli + 100;
    } else {
        $NumCoppiaEW_= $NumCoppiaEW;
    }
    ?>


		<!-- NUMERO DELLE COPPIE  NS  EW   -->	<!--bgcolor="silver"-->			
		<!-- NOMI DELLE COPPIE   -->
		<!-- TAVOLO SUCCESSIVO   -->			
    <table class= "tabella-con-bordi" align="center" border="0">
        <tr align="center">
            <td  width="50px" align="center"><strong>Scores</strong></td>    
            <?php if($boardAtt) { // SE UNA BOARD E' SELEZIONATA MOSTRA I COLORI E IL NUMERO DI COPPIA ?>

				<td width="45%" align="center" style="background-color: <?php echo $VulnNS ? '#D97373' : '#7FB37D'; ?>;">
                    <strong><h4>NS: <?php echo $NumCoppiaNS; ?></h4></strong>
    			</td>			

				<td width="45%" align="center" style="background-color: <?php echo $VulnEW ? '#D97373' : '#7FB37D'; ?>;">
                    <strong><h4>EW: <?php echo $NumCoppiaEW_; ?></h4></strong>
    			</td>			
<!--                <td width="45%" align="center" <?php if($VulnNS){ ?> style="background-color:'#D97373';" <?php }else{ ?> style="background-color:'#7FB37D';" <?php } ?>>
                    <strong><h4>NS: <?php echo $NumCoppiaNS; ?></h4></strong>
                </td>
			
                <td width="45%" align="center" <?php if($VulnEW){ ?> style="background-color:'#D97373';" <?php }else{ ?> style="background-color:'#7FB37D';" <?php } ?>>
                    <strong><h4>EW: <?php echo $NumCoppiaEW_ ?></h4></strong>
                </td>
-->	            
			<?php } else { ?>
                <td width="45%" align="center"><strong><h4>NS: <?php echo $NumCoppiaNS ?></h4></strong></td>
                <td width="45%" align="center"><strong><h4>EW: <?php echo $NumCoppiaEW_ ?></h4></strong></td>            
            <?php } ?>
        </tr>
        <tr align="center">
            <td width="50px" align="center" style="writing-mode: vertical-lr; "><strong><h5>Board</h5></strong></td>	
<!--
			<td bgcolor="orange" width="45%" align="center" style="color: white;"><strong><h5><?php echo $NickN; ?> <br><?php echo $NickS; ?></h5></strong></td>
            <td bgcolor="orange" width="45%" align="center" style="color: white;"><strong><h5><?php echo $NickE; ?> <br><?php echo $NickW; ?></h5></strong></td>       
-->            
			<td class="cella-nick">
				<?php echo $NickN; ?><br><?php echo $NickS; ?>
			</td>
			<td class="cella-nick">
				<?php echo $NickE; ?><br><?php echo $NickW; ?>
			</td>
        </tr>

        <?php if($BoardsTavoloGiocate && !$boardAtt){ // MOSTRA IL TAVOLO SUCCESSIVO PULSANTE ?>
            <tr id="mioTesto" align="center">
                <td width="50px" align="center"><strong><h4></h4></strong></td>
                <td bgcolor="violet" width="45%" align="center"><strong><h4>Tav.Suc: <?php echo $tavoloSucNS;?>-<?php echo $PosSucNS;?></h4></strong></td>
                <td bgcolor="violet" width="45%" align="center"><strong><h4>Tav.Suc: <?php echo $tavoloSucEW;?>-<?php echo $PosSucEW;?></h4></strong></td>
            </tr>
            <?php if(!$TorneoFinito) $msg= $msg."<br>VEDERE NUOVO POSTO e AGGIORNARE"; ?>
            <?php $TavoloAttualeNS= $tavoloSucNS;?>
            <?php $TavoloAttualeEW= $tavoloSucEW;?>
        <?php } else if( ($MostraSpostamentiFatti || $TurnoInizio) && $turno!=1 ) {
            if($TavoloAttExNS) {
                $TavoloAttualeNS= $TavoloAttExNS;
                $TavoloAttualeEW= $TavoloAttExEW;          
            }   
            // TURNO APPENA AGGIORNATO: MOSTRA IL TAVOLO ATTUALE DELLA COPPIA PRECEDENTE
        ?>          
            <tr align="center">
                <td width="50px" align="center"><strong><h4></h4></strong></td>
                <td bgcolor="violet" width="45%" align="center"><strong><h4>ex-NS al Tav: <?php echo $TavoloAttualeNS ?></h4></strong></td>
                <td bgcolor="violet" width="45%" align="center"><strong><h4>ex-EW al Tav: <?php echo $TavoloAttualeEW ?></h4></strong></td>
            </tr>
        <?php } ?>
    </table>	
	</div>

	<div class="table-wrapper">

	<!--   NUMERO DELLE BOARDS  E SCORES  -->
	
    <table class= "tabella-con-bordi" align="center" border="0">

<?php
//          ###############################################

//*****************************************************************************
    // LEGGE i risultati eventualmente esistenti per le varie boards   height="50px"
//*****************************************************************************
	$sql= "SELECT * FROM `brdg_cop_scores` WHERE  torneoID=$ID_torneo AND turno = $turno  AND coppiaNS=$NumCoppiaNS ORDER BY id";		
//echo $sql ;
//echo  "<br>";
	$dati= $connessione->query($sql); 
	if(!$dati) {
		$connessione->close();
		exit("<b><center>Errore ricerca risultato</center></b>");				
	}
	$row = $dati->fetch_assoc();
	$kboard=1;
	
	$minuti= 0 ;
	$secondi= -1 ;
	
	
	while($row)  {
?>		
		<tr align="center" >

<?php					
		$Board= $row['board'];
		$ArrayBoards[$kboard]= 	$Board;
		 //******************************
		// CONTROLLA SE LA BOARD E' STATA GIOCATA NEI TURNI PRECEDENTI
		 //******************************
//    ###################################################

		$sql="SELECT COUNT(*) FROM `brdg_cop_scores` WHERE  torneoID=$ID_torneo AND turno<$turno AND board=$Board  ORDER BY id" ;	
//echo $sql ;
//echo  "<br>";
		$dati1= $connessione->query($sql); 		
		$row1 = $dati1->fetch_array();
		$Giocata= $row1[0];
		if($Giocata) {
			$ColoreBoard="green";
		}else{
			$ColoreBoard="red";
		}	
//     ####################################################

//echo "boardAtt: ".$boardAtt ;
//echo  "<br>";
//echo "ColoreBoard: ".$ColoreBoard ;
//echo  "<br>";
		// IMPOSTA IL TIMER per tutto il turno
//echo "azione= ".$azione ;
//echo  "<br>";
		if($azione=="VaiGioca") {    		//PRIMO TURNO:  PROVIENE DA ACCESSO
			if ($Giocata)  {  				// verde
				//$minuti= 0;  
				//$secondi=3;	
				$minuti= 8*$BoardsXturno;
				$secondi=0;	
			}else{							// rosso  pi� tempo per mescolare e fotografare
				$minuti= 10*$BoardsXturno;
				$secondi=0;	
			}
		}
		if(substr($msg,0,6) == "NUOVO ") {	
			if ($Giocata)  {  				// verde
				$minuti= 8*$BoardsXturno;
				$secondi=0;	
			}else{							// rosso  pi� tempo per mescolare e fotografare
				$minuti= 10*$BoardsXturno;
				$secondi=0;	
			}
		}
		if($BoardsTavoloGiocate) {
			unset($minuti)  ;
			unset($secondi) ;
			//$minuti= 0.017 ;
			//$secondi= 0 ;
		}

		if($turno == 1)  {	
			unset($minuti)  ;
			unset($secondi) ;
		}

		if($azione == "OK" && $TurnoCompletato)  {	
			$minuti= 0;
			$secondi= -2;
		}


//echo "minuti= ".$minuti ;
//echo  "<br>";
		

		//==============================================================================	
		//   COLORA DI GIALLO LA MANO ATTIVA
		//==============================================================================
?>
			<td width="62px" bgcolor="<?php echo $ColoreBoard;?>"  align="center" style="padding: 2px;"> 
   
		<?php 	if($Board==$boardAtt) { ?>
				<input  style="width: 56px;background-color:yellow;font-size:36px;vertical-align: middle;" name="action" type="submit" value= <?php echo $Board;?> >
<?php			
			 //******************************
			// CONTROLLA SE E' PRESENTE LA FOTO DELLA BOARD ATTUALE
			 //******************************
				
			 		if (is_file('$home_archive."/upload/tornei/'.$torneo.'/N_'.$Board.'.jpeg')) {
					//$FotoPresente=true;
					}else if($orig == "GestFoto") {
					//$FotoPresente=false;
						$msg= $msg."<br>FOTO NON SALVATA";
					}
							
				}else{
?>			
				<input  style="width: 56px;background-color:lightgrey;font-size:36px;vertical-align: middle;" name="action" type="submit" value= <?php echo $Board;?> >
<?php
				}
//       ####################           ####################      ##############
?>		
			</td>

		
<?php		
		$score_NS= $row['score'];

//echo "score_NS= ".$score_NS ;
//echo  "<br>";

		// NASCONDE I RISULTATI SE E' STATO PREMUTO AGGIORNA E IL TURNO NON E' COMPLETO
		if($NascondiScores) {  
			$score_NS=-2;
			$score_EW=-2;
			//$msg="TURNO NON COMPLETATO";
		}
		
		// bgcolor= "yellow"  bgcolor= #eee8aa 
		if($score_NS==NULL) { ?>
				<td  width=30%  align="center"> 
					<input align="center"  style="font-size:36px;vertical-align: middle;width:130px;"  name="MP_NS_<?php echo $kboard;?>"  value="">
				</td>
				<td  width=30%  align="center">
					<input align="center"  style="font-size:36px;vertical-align: middle;width:130px;"  name="MP_EW_<?php echo $kboard;?>"  value="">
				</td>   								
<?php	}else if($score_NS==0) { 
			//$score_= "P"; ?>
				<td  width=45% align="center"> 
					<input align="center"  style="font-size:36px;vertical-align: middle;width:130px;"  name="MP_NS_<?php echo $kboard;?>"  value="PP">
				</td>
				<td  width=45% align="center"> 
					<input align="center"  style="font-size:36px;vertical-align: middle;width:130px;"  name="MP_EW_<?php echo $kboard;?>"  value="">
				</td>    								
<?php	}else if($score_NS==-1) {
			//$score_= "R"; ?>
				<td  width=45% align="center"> 
					<input align="center"  style="font-size:36px;vertical-align: middle;width:130px;"  name="MP_NS_<?php echo $kboard;?>"  value="R">
				</td>     
				<td  width=45% align="center"> 
					<input align="center"  style="font-size:36px;vertical-align: middle;width:130px;"  name="MP_EW_<?php echo $kboard;?>"  value="">
				</td>   								

<?php	}else if($score_NS<-2) {     
			$score_EW= -$score_NS; ?>
				<td  width=45% align="center"> 
					<input align="center"  style="font-size:36px;vertical-align: middle;width:130px;"  name="MP_NS_<?php echo $kboard;?>" value="">
				</td>
				<td  width=45% align="center"> 
					<input align="center" style="font-size:36px;vertical-align: middle;width:130px;text-align:right;" name="MP_EW_<?php echo $kboard;?>"  value="<?php echo $score_EW;?>">
				</td>    								
<?php	}else if($score_NS>1 && $score_NS<10000) { ?>
				<td  width=45% align="center"> 
					<input align="center" style="font-size:36px;vertical-align: middle;width:130px;text-align:right;" name="MP_NS_<?php echo $kboard;?>"  value="<?php echo $score_NS;?>">
				</td>
				<td  width=45% align="center"> 
					<input align="center" style="font-size:36px;vertical-align: middle;width:130px;" name="MP_EW_<?php echo $kboard;?>"  value="">
				</td>  
<?php	}else if($score_NS>10000) {
			//$score_= "A";
			$val= $score_NS;		
			$score_NS= intval(($val-10000)/100) ;
			$score_EW= $val-10000-$score_NS*100;	 ?>
				<td  width=45% align="center"> 
					<input align="center" style="font-size:36px;vertical-align: middle;width:130px;text-align:right;" name="MP_NS_<?php echo $kboard;?>"  value="<?php echo $score_NS;?>">
				</td>
				<td  width=45% align="center"> 
					<input align="center" style="font-size:36px;vertical-align: middle;width:130px;text-align:right;" name="MP_EW_<?php echo $kboard;?>"  value="<?php echo $score_EW;?>">
				</td>  
<?php	}else if($score_NS==-2) {
			// NELL AREA DISPONONIBILE SCRIVE I BOARDS SUCCESSIVI
			$BoardsSucNS_k= $BoardsSucNS[$kboard];
			$BoardsSucEW_k= $BoardsSucEW[$kboard];
			if($kboard==1) { ?>				 
				<td bgcolor= "white" width=45% align="center" style="font-size:28x;color:black;font-weight: bold;border-color:white;">
					<span style="display:block; text-align:center;"><u>STANNO ANCORA GIOCANDO </u></span>
					<br><br>Board Suc: <?php echo $BoardsSucNS_k;?>
				</td> 
			 	<td bgcolor= "white" width=45% align="center" style="font-size:28x;font-weight: bold;border-color:white;">
					<span style="display:block; color: red;text-align:center;"><u>AGGIORNA A FINE TURNO !</u></span>
					<br><br>Board Suc: <?php echo $BoardsSucEW_k;?>
				</td>
<?php		}else {?>
				<td bgcolor= "white" width=45% align="center" style="font-size:28x;color:black;font-weight: bold;border-color:white;">Board Suc: <?php echo $BoardsSucNS_k;?>
				</td> 
				<td bgcolor= "white" width=45% align="center" style="font-size:28x;color:black;font-weight: bold;border-color:white;">Board Suc: <?php echo $BoardsSucEW_k;?>
				</td>
<?php		}?>
<?php	} ?>

		</tr>
<?php		
		$row = $dati->fetch_assoc();
		$kboard++;
	} 
?>	
	</table>
	</div>
<?php
risultati:	

	
	if(!$InputContratto)  {
		//  MESSAGGI    echo"<p style=\"text-align: center;\">
		//  --------------
		if(substr($msg,0,5) == "ATTEN")  {	
			echo "<div><center><b><font style=\"background-color:yellow;font-size:20px;\" color=\"#FF0000\">".$msg."</font></b></center></div>";
		}else{
			echo "<div bgcolor=\"#a0eea0\"><center><font style=\"background-color:aqua;font-size:20px;\" color=\"#0000FF\">".$msg."</font></center></div>";
		}
					

		if($ColoreBoard=="red" && (substr($msg,0,5)=="TOCCA" || substr($msg,0,5)=="NUOVO")) {   //msg= TOCCA per ....
			echo"<p style=\"text-align: center;\"><input id=\"mioTesto\" style=\"background-color:red; font-size:22px; color:white;\" name=\"action\" type=\"submit\" value=\"MANI DA MESCOLARE\"></p>";
		}else if($ColoreBoard=="red" && ($azione == "OK" || $azione == "I")){			
			echo"<div style=\"display: flex; justify-content: space-between; align-items: center; width: 100%;\">";
				echo"<input  style=\"background-color:yellow; font-size:16px; color:black;\" name=\"action\" type=\"submit\" value=\"FOTO\">";
				echo"<input id=\"mioTesto1\" style=\"background-color:red; font-size:22px; color:white;\" name=\"action\" type=\"submit\" value=\"EDITA MANO\">";
			echo"</div>";		

				}else if($ColoreBoard=="green" && substr($msg,0,5)=="TOCCA"){ //msg= Tocca per ....
			echo "<p><center><font style=\"background-color:lightgreen;font-size:20px;\" >MANI GIA' MESCOLATE</font></center></p>"; 
		} 		
	
		//echo ""><center><font style="background-color:#f0e090;font-size:20px;" >Toccare N.Board per inserire</font></center></div>"; 
		//echo"<div style=\"width: 300px;\">";		
			echo"<div style=\"display: flex; justify-content: space-between; align-items: center; width: 100%;\">";
			//echo"<div style=\"display: inline-block; float: left; text-align: left;\">";
				echo"<input style=\"background-color:lightgrey;font-size:20px;\" name=\"action\" type=\"submit\" value=\"<-\">";
				echo "<div><center><font style=\"background-color:#f0e090;font-size:20px;\" >(P= ALL-PASS......R= RIPOSO)</font></center></div>";
				echo"<input style=\"background-color:lightgrey;font-size:20px;\" name=\"action\" type=\"submit\" value=\"I\">";
			echo"</div>";
/*			
			//  INPUT DI ?  PER VEDERE UN FILE TUTORIAL
			//******************************************
			echo"div style="display: inline-block; float: center; text-align: center;">";
				//$pdf_file = './doc/Webridge.pdf';
				//echo '<button style="background-color:lightgrey;font-size:20px;" href="' . $pdf_file . '" target="_blank">?</button>';
				echo"<input style="background-color:lightgrey;font-size:20px;" name="action" type="submit" value="?">";
			echo"</div>";
*/			
			//echo"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				
			//echo"<div style=\"display: inline-block;  float: right;text-align: right;\">";
		//echo"</div>";		
		//echo "</br>";


	}

	
	if($TorneoFinito || $Stato==1)  {
?>	
		<div class="table-wrapper">
		<table class= "tabella-pulita">
 		<!--<tbody bgcolor=#eee8aa>-->
			<tr align="center">
				<td> 
				<div style="display: flex; justify-content: space-between; align-items: center; width: 80%; margin: 0 auto;">
						<input style="text-align: center; background-color: firebrick; color: white; font-size: 20px;" name="action" type="submit" value="Classifica">
						<input style="text-align: center; background-color: firebrick; color: white; font-size: 20px;" name="action" type="submit" value="Scores">
						<input style="text-align: center; background-color: firebrick; color: white; font-size: 20px;" name="action" type="submit" value="Analisi">
					</div>      
					<div style="display: flex; justify-content: space-between; align-items: center; width: 80%; margin: 10px auto;">
						<input style="text-align: center; background-color: firebrick; color: white; font-size: 20px;" name="action" type="submit" value="Risultati">
						<input style="text-align: center; background-color: firebrick; color: white; font-size: 20px;" name="action" type="submit" value="Vedi Foto">
						<input style="text-align: center; background-color: firebrick; color: white; font-size: 20px;" name="action" type="submit" value="Tempi">
					</div>      
				</td>
			</tr>		<!--</tbody>-->
		</table>
		</div>
<?php
	}

//#######################################################################################################
//#######################################################################################################	
//*************************************************************************	
//*************************************************************************	
//     SCRIPT   DI CALCOLO DELLO SCORE
//*************************************************************************	
//*************************************************************************	
?>
<script>

		function calcolaScoreBridge(contratto, prese, isVuln) {
			let match = contratto.toUpperCase().match(/^([1-7])(NT|N|S|H|D|C|♠|♥|♦|♣)(X{1,2})?$/);
			if (!match) return 0;

			let livello = parseInt(match[1], 10);
			let semeInput = match[2];
			let raddoppio = match[3] || "";

			let seme = "";
			if (semeInput === "NT" || semeInput === "N") seme = "NT";
			else if (semeInput === "H" || semeInput === "♥" || semeInput === "S" || semeInput === "♠") seme = "MAJOR";
			else seme = "MINOR";

			let preseContratto = livello + 6;
			let differenza = prese ;

    		//console.log("Debug Calcolo:", { contratto: contratto, prese: prese, livello: livello , seme: semeInput, raddoppio: raddoppio , vuln: isVuln });

			// CONTRATTO K.O. (DOWN)
			if (differenza < 0) {
				let down = Math.abs(differenza);
				let puntiDown = 0;

				if (raddoppio === "") {
					puntiDown = down * (isVuln ? 100 : 50);
				} else if (raddoppio === "X") {
					if (!isVuln) {
						if (down >= 1) puntiDown += 100;
						if (down >= 2) puntiDown += 200;
						if (down >= 3) puntiDown += 200;
						if (down > 3) puntiDown += (down - 3) * 300;
					} else {
						if (down >= 1) puntiDown += 200;
						if (down > 1) puntiDown += (down - 1) * 300;
					}
				} else if (raddoppio === "XX") {
					if (!isVuln) {
						if (down >= 1) puntiDown += 200;
						if (down >= 2) puntiDown += 400;
						if (down >= 3) puntiDown += 400;
						if (down > 3) puntiDown += (down - 3) * 600;
					} else {
						if (down >= 1) puntiDown += 400;
						if (down > 1) puntiDown += (down - 1) * 600;
					}
				}
				return -puntiDown;
			}

			// CONTRATTO REALIZZATO
			let puntiPreseContratto = 0;
			let moltiplicatore = (raddoppio === "X") ? 2 : (raddoppio === "XX") ? 4 : 1;

			if (seme === "MINOR") puntiPreseContratto = (livello * 20) * moltiplicatore;
			else if (seme === "MAJOR") puntiPreseContratto = (livello * 30) * moltiplicatore;
			else if (seme === "NT") puntiPreseContratto = ((livello * 30) + 10) * moltiplicatore;

			let bonus = 0;
			if (puntiPreseContratto < 100) bonus += 50; // Parziale
			else bonus += isVuln ? 500 : 300; // Manche

			if (livello === 6) bonus += isVuln ? 750 : 500; // Piccolo Slam
			if (livello === 7) bonus += isVuln ? 1500 : 1000; // Grande Slam

			if (raddoppio === "X") bonus += 50;
			if (raddoppio === "XX") bonus += 100;

			let puntiOvertricks = 0;
			if (differenza > 0) {
				if (raddoppio === "") {
					puntiOvertricks = differenza * (seme === "MINOR" ? 20 : 30);
				} else if (raddoppio === "X") {
					puntiOvertricks = differenza * (isVuln ? 200 : 100);
				} else if (raddoppio === "XX") {
					puntiOvertricks = differenza * (isVuln ? 400 : 200);
				}
			}

			return puntiPreseContratto + bonus + puntiOvertricks;
		}	
		

		function eseguiCalcolo() {
			let c = document.getElementById('bid-select').value;
			let x  = document.getElementById('contro-select').value;
			let da = document.getElementById('da-select').value;

			let p = parseInt(document.getElementById('prese-select').value);
			let b = parseInt(<?php echo json_encode($boardAtt); ?>);
			let bXt = parseInt(<?php echo json_encode($BoardsXturno); ?>);
			let array_b = <?php echo json_encode($ArrayBoards); ?>;
			let mp_ns = <?php echo json_encode($MP_NS); ?>;
			let mp_ew = <?php echo json_encode($MP_EW); ?>;

			let res; 

			//  CONTROLLI E ALLERTAMENTI

			if (c[0] === "A" || c[0] === "P" || c[0] === "R") {
				alert("Usare direttamente il tasto <OK>");
				return;
			}
			else  {
				//  CASO  DI CONTRATTO REGOLARE
				if (da === "--") {
					alert("INSERIRE CHI GIOCA IL CONTRATTO");
					return; 
				}
				let linea = (da === 'N' || da === 'S') ? 0 : 1;

				const ZonaNS = [false, true, false, true, true, false, true, false, false, true, false, true, true, false, true, false];
				const ZonaEW = [false, false, true, true, false, true, true, false, true, true, false, false, true, false, false, true];

				// Assumendo che boardAtt e Linea siano già definiti nel tuo codice JS
				let iBoard = (b - 1) % 16;
				let vuln;

				if (linea) {
					vuln = ZonaEW[iBoard];
				} else {
					vuln = ZonaNS[iBoard];
				}

				let cont= c; 
				if (x === '!') {
					cont = c + 'X';
				} else if (x === '!!') {
					cont = c + 'XX';
				}			

				//console.log("Debug Calcolo:", { contratto: cont, prese: p, board: b , da: da, contro: x , vuln: vuln });
				

				// Validazione base
				if (!c || isNaN(p)) {
					alert("Per favore, inserisci contratto e prese validi!");
					return;
				}

				res = calcolaScoreBridge(cont, p, vuln);

				if (linea) res= -res;
			}
			
			// Aggiorna l'interfaccia
			document.getElementById('displayScore').innerText = res;
			document.getElementById('score_calcolato').value  = res;
		}
</script>

<?php

//*************************************************************************	
//*************************************************************************	
//*************************************************************************	
//***************** MASCHERA DI NSERIMENTO CONTRATTO **********************	
//*************************************************************************	
//*************************************************************************	
//*************************************************************************	
//*************************************************************************	

modulo:	
	if($InputContratto) {
		// LEGGE IL CONTRATTO NEL DATABASE
		if($azione != "Calcola")  {			
			$sql="SELECT * FROM `brdg_cop_scores` WHERE `torneoID`=$ID_torneo AND  `turno`=$turno AND  `board`=$boardAtt AND  `tavolo`=$NumTavolo";
			//$sql="SELECT * FROM `brdg_cop_scores` WHERE `torneoID`=$torneoID AND  `turno`=$turno AND  `board`=$boardAtt AND  `tavolo`=$NumTavolo";
//echo "sql= ".$sql ;
//echo  "<br>";
						
			$dati= $connessione->query($sql);		
			if($dati) {			
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
				}else if($Contratto_[0]=="A")  {
					$Contratto= "A";
					$Da= "--";
				}else if($Contratto_[0]=="P")  {
					$Contratto= "P";
					$Da= "--";
				}else if($Contratto_[0]=="R")  {
					$Contratto= "R";
					$Da= "--";
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
			}
		}
		
		// *********************************************************
		//  CALCOLA L' INIZIO DELLA SCELTA
		// *********************************************************
/*
		$PrimoNum= 1;
		$PrimoSeme= 0;
		$numero= $numeroContratto+1;
		$PrimoNum= -6-$numero;
		$UltimoNum= 7-$numero;
*/		
		$PrimoNum= -9;
		$UltimoNum= 6;
		
		// *********************************************************
		//  PER COLORARE LA LINEA DI GIOCO     width="300px" 
		// *********************************************************
		$iBoard= ($boardAtt-1)%16;
		$VulnNS= $ZonaNS[$iBoard];
		$VulnEW= $ZonaEW[$iBoard];		
?>		
<!--      INTESTAZIONE del NODULO INPUT CONTRATTO       -->
<!--	  
<table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
    <tbody style="background-color: orange; font-size: 22px; text-align: center;">
        <tr>
            <td><strong>Board:</strong></td>
            <td><strong><?php echo $boardAtt;?>  </strong></td>
           
            <td style="background-color: <?php echo $VulnNS ? '#D97373' : '#7FB37D'; ?>; color: white;"><strong>NS:</strong></td>
            <td><strong><?php echo $NumCoppiaNS;?></strong></td>
            <td style="background-color: <?php echo $VulnEW ? '#D97373' : '#7FB37D'; ?>; color: white;"><strong>EW:</strong></td>
            <td><strong><?php echo $NumCoppiaEW;?></strong></td>
        </tr>          
    </tbody>
</table>
-->
<?php
    // *********************************************************
    //  INSERIMENTO DEL RISULTATO DEL CONTRATTO    border-collapse: collapse;margin: 10px auto;
    // *********************************************************
?>
<div style="margin-top: 20px;">  
	<table style="width: 100%;  table-layout: fixed; ">
		<tbody style="background-color: orange; font-size: 18px; text-align: center;">
			<tr>
				<th><strong>Contrat</strong></th>
				<th><strong>X</strong></th>
				<th><strong>Da</strong></th>
				<th><strong>+/-</strong></th>
				<th colspan="2"><strong>Attacco</strong></th>
			</tr>
		</tbody>
		
		<tbody style="background-color: #f0e090; text-align: center;">
			<tr>
<?php               
				// CONTRATTO   
				echo "<td>";
					echo "<select id=\"bid-select\" name=\"Bid\" class=\"select-responsive\">";
						if($Contratto=="A") {echo "<option value='A' selected='selected'>A</option>";}else{echo "<option value='A'>A</option>";}
						if($Contratto=="R") {echo "<option value='R' selected='selected' style=\"color: red;\">R</option>";}else{echo "<option value='R' style=\"color: red;\">R</option>";}
						if($Contratto=="P") {echo "<option value='P' selected='selected'>PP</option>";}else{echo "<option value='P'>PP</option>";}
						for($n=0;$n<35;$n++){
							echo "<option value='$BBox[$n]'"; 
							if ($Contratto==$BBox[$n]) {echo " selected='selected'>";}else{echo ">";}
							echo $BBox[$n]."</option>";
						} 
					echo "</select>";
				echo "</td>";
		
				// CONTRO
				echo "<td>";
					echo "<select id=\"contro-select\" name=\"Contro\" class=\"select-responsive\">";
						echo "<option value='--'></option>";
						if($Contro=="!") {echo "<option value='!' selected='selected'> X</option>";}else{echo "<option value='!'> X</option>";}
						if($Contro=="!!") {echo "<option value='!!' selected='selected'>X X</option>";}else{echo "<option value='!!'>X X</option>";}
					echo "</select>";
				echo "</td>";
				
				// GIOCATO DA
				echo "<td>";
					echo "<select id=\"da-select\" name=\"Da\" class=\"select-responsive\">";
						echo "<option value='--'></option>";        
						for($n=0;$n<4;$n++){
							echo "<option value='$Posizioni[$n]'"; 
							if ($Da==$Posizioni[$n]) {echo " selected='selected'>";}else{echo ">";}
							echo $Posizioni[$n]."</option>";
						}   
					echo "</select>";
				echo "</td>";
				
				// PRESE +/-   
				echo "<td>";
					echo "<select id=\"prese-select\" name=\"Prese\" class=\"select-responsive\">";             
						for($n=$PrimoNum;$n<=$UltimoNum;$n++){
							if($n==$prese) {
								echo "<option value='$n' selected='selected'>".$n."</option>";
							}else{
								echo "<option value='$n'>".$n."</option>";
							}
						} 
					echo "</select>";
				echo "</td>";
				
				// ATTACCO VALORE
				echo "<td>";
					echo "<select name=\"AttNum\" class=\"select-responsive\">";
						echo "<option value='--'></option>";
						for($n=0;$n<13;$n++){
							echo "<option value='$Valori[$n]'"; 
							if ($attNum==$Valori[$n]) {echo " selected='selected'>";}else{echo ">";}
							echo $Valori[$n]."</option>";
						}       
					echo "</select>";
				echo "</td>";
				
				// ATTACCO SEME
				echo "<td>";
					echo "<select name=\"AttSeme\" class=\"select-responsive\">";            
						echo "<option value='--'></option>";
						for($n=0;$n<4;$n++){
							echo "<option value='$Semi[$n]'"; 
							if ($attSeme == $Semi[$n]) {echo " selected='selected'";}
							echo ">" . $Semi[$n] . "</option>";
						} 
					echo "</select>";
				echo "</td>";
	?>          
			</tr>
		</tbody>
	</table>
					<script>
						// CAMBIA DINAMICAMENTE I LIMITI DEL SELEECT
						const selectElement = document.querySelector('#bid-select');
						const preseSelect = document.querySelector('#prese-select');

						selectElement.addEventListener('change', (event) => {
							const valoreSelezionato = event.target.value;
							const primoCarattere = parseInt(valoreSelezionato.charAt(0));
							
							let nuovoPrimo, nuovoUltimo;

							// In JS si usa !isNaN() per controllare se è un numero
							if (!isNaN(primoCarattere)) {
								nuovoPrimo = -6 - primoCarattere;
								nuovoUltimo = 7 - primoCarattere;
							} else {
								nuovoPrimo = -9;
								nuovoUltimo = 6;
							}

							// Svuota e ripopola DENTRO l'evento
							preseSelect.innerHTML = '';
							for (let n = nuovoPrimo; n <= nuovoUltimo; n++) {
								let option = document.createElement('option');
								option.value = n;
								option.textContent = n; // textContent è più sicuro di .text
								preseSelect.appendChild(option);
								if (n === 0) {
									option.selected = true;
								}							}
						});
					</script>
<?php
			//*******************************************************
			// ********************* FORM DI SCELTA  ********************   width="100"
			//*******************************************************

		//echo "Npass: ".$Npass ;
		//echo  "<br>";	   
		//echo "lastBidVera: ".$lastBidVera ;
		//echo  "<br>";	   

?>						
	<table  class= "tabella-con-bordi" align="center" style=" border-collapse: collapse;">
	<!--<tbody bgcolor="f0e090">-->
		<tr>
			<td>
				<!--<div style="display: flex; flex-wrap: wrap; gap: 60px; justify-content: center; align-items: center; padding: 10px;">-->
				<div style="display: flex; align-items: center;gap: 10px; justify-content: space-between;  width: 100%;">

					<button style="background-color:gold;font-size:20px; text-align: center; padding: 10px 5px; height: 46px; border-radius: 5px; cursor: pointer; box-sizing: border-box;" type="button" onclick="eseguiCalcolo()">Calcola</button>

					<div style="background-color:yellow;font-size:25px; text-align: center; padding: 10px 5px;  height: 46px; border-radius: 5px; box-sizing: border-box;padding: 10px; border: 1px solid #ccc; align-self: flex-start;" id="displayScore">Score</div>
					
					<input type="hidden" name="score_calcolato" id="score_calcolato">
					
					
					<!-- <button type="submit">OK</button>-->
					<input style="background-color:MediumSeaGreen; font-size:20px; text-align: center; padding: 10px 20px; height: 46px; border-radius: 5px; color: white; cursor: pointer; box-sizing: border-box;" name="action" type="submit" value="OK">

					<!-- Bottone TORNA-->
					<input style="background-color:lightgrey; font-size:14px; text-align: center; padding: 10px 5px; height: 46px; border-radius: 5px; cursor: pointer; box-sizing: border-box;" name="action" type="submit" value="TORNA">


				</div>

			</td>
		</tr>		
	<!-- </tbody>---------------------------------------------------- -->
	
	</table>	
</div>
<?php	
	}
 //-------------------------------------------------- width="300"
	if($MostraRisultati){
?>

		<table class= "tabella-con-bordi" align="center"  >
		
			<tr align="center" >
				<td >
		   			<br>
					<span width="3px" style=" font-size:22px;"><strong>Board: <?php echo $boardAtt; ?></strong></span>
					<input style="background-color:lightgrey; font-size:22px;" name="action" type="submit" value="Vedi altri risultati">	
					<!-- <input style="background-color:lightgrey; font-size:22px;" name="action" type="submit" value="Vedi foto"> -->
				</td>
			</tr>
		  
		</table>
							
<?php	} ?>

	</form>	
</div>  

<?php
//*******************************************************************************************

$connessione->close();
?>
		<!--  SCRIPT PER FAR  BLINCARE IL TESTO  -->

	<script>
		function blink() {
			var testo = document.getElementById('mioTesto');
			testo.style.opacity = (testo.style.opacity == '0') ? '1' : '0';
		}
		setInterval(blink, 800); // Cambia l'opacit� ogni 500 millisecondi

		function blink1() {
			var testo1 = document.getElementById('mioTesto1');
			testo1.style.opacity = (testo1.style.opacity == '0') ? '1' : '0';
		}
		setInterval(blink1, 250); // Cambia l'opacit� ogni 250 millisecondi
	</script>

    <script>

       
	// Leggiamo i valori PHP e li convertiamo in secondi totali
        //const initialHours = <?php echo $ore; ?>;
        const initialMinutes = <?php echo $minuti; ?>;
        const initialSeconds = <?php echo $secondi; ?>;
        //alert("minuti iniziali:  "+initialMinutes+"  secondi iniziali:  "+initialSeconds+"  sound:  "+soundF);
        //alert("minuti iniziali:  "+initialMinutes+"  secondi iniziali:  "+initialSeconds);
		
        function formatTime(totalSeconds) {
            //const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;			
        }

        function startCountdown() {
            const timerElement = document.getElementById('timer');
			let timeLeft;			
			let StrTimeLeft;			
					
			// IMPOSTA o RECUPERA 	
			fineTurno= 0;
			if (initialMinutes > 0 || initialSeconds > 0) {    
				// Se ci sono valori iniziali, li usiamo
				timeLeft = Math.floor(initialMinutes * 60) + initialSeconds;
		        //alert("Valore impostato:  "+String(timeLeft));
			} else if(initialSeconds == -2){  	
				// Altrimenti  (QUANDO MINUTI=0  E SECONDI=-2)  SUONA IL FINE TURNO		
				timeLeft = 1;  // MODIFICA
				fineTurno= 1;
			} else {  	
				// Altrimenti  (QUANDO MINUTI=0  E SECONDI=-1)  recuperiamo il valore salvato
				StrTimeLeft = sessionStorage.getItem('Rimasto');
				timeLeft = Number(StrTimeLeft);
		        //alert("Valore recuperato:  "+StrTimeLeft);
			}
			
            timerElement.textContent = formatTime(timeLeft);

            const interval = setInterval(() => {
                timeLeft--;
				// SALVA timeLeftt in VARIABILE locale	
				sessionStorage.setItem('Rimasto', String(timeLeft));
                //sessionStorage.setItem('Rimasto', timeLeft.toString());
                //if (timeLeft < 3 && timeLeft > 1) {
                if (timeLeft == 0) {
                    clearInterval(interval);
					if(fineTurno==0)  {
						timerElement.textContent = "TEMPO SCADUTO";
					}else{
						timerElement.textContent = "FINE TURNO";
					}
                    timerElement.classList.add('finished');

					//var audio = new Audio('beep-2.wav');
					//var audio = new Audio('ALARM2.wav');
					//audio.play();

					var audio = new Audio();
					audio.autoplay = false;
					audio.muted = false;
					audio.volume = 1.0;
					if(fineTurno==0)  {
						//audio.src = navigator.userAgent.match(/Firefox/) ? 'ALARM2.ogg' : 'sollecito.wav';
						//audio.src = 'ALARM2.wav';
						//audio.src = 'sollecito.wav';
						audio.src = 'sollecito.mp3';
						//alert("PER FAVORE AFFRETTARSI");
					}else{
						//audio.src = navigator.userAgent.match(/Firefox/) ? 'beep-1.ogg' : 'beep-1.wav';
						audio.src = 'FineTurno.mp3';
						alert("IL TURNO E' FINITO  -  AVVISARE TUTTI CHE DEVONO AGGIORNARE");
					}

					audio.play();

                    return;
               // }else if(timeLeft < 1){
                   // clearInterval(interval);
                   // timerElement.classList.add('finished');					
				}
                
                timerElement.textContent = formatTime(timeLeft);
            }, 1000);
        }




        startCountdown();

	</script>	


	</body>
</html>


