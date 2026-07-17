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
/*
INSERIMENTO COPIE 
*/
// inclusione del file di connessione

include "dbConnessione.php";


	$azione = $_POST['action'] ?? NULL;
//echo "azione=  ".$azione;			   
//echo "<br>";

	$NomeA= $_POST['NomeA'] ?? NULL;
	$NomeB= $_POST['NomeB'] ?? NULL;
	//$Telefono= $_POST['Telefono'];
	//$Mail= $_POST['Mail'];
	//$Associazione= $_POST['Associazione'];
	if($NomeB=="*" || $NomeB=="no") {$SenzaCompagno=true;}else{$SenzaCompagno=false;} 

  //  IL CONTROLLO ARRIVA DA ISCRIZIONE 
  
	$torneo= $_GET['torneo'] ?? NULL;
	$scelta= $_GET['scelta'] ?? NULL;
	if($scelta=="ok") {
		$azione = "Canc ID";		
	}	
    $sql= "SELECT * FROM `brdg_cop_tornei` WHERE NomeTorneo=\"".$torneo."\"" ;
//echo "sql 1=  ".$sql;			   
//echo "<br>";

	$dati= $connessione->query($sql); 
	if(!$dati)  {
		// il torneo � quello spuntato e deve esistere !!
		exit( "<body bgcolor=\"#f0e090\"><b><center>Il torneo non esiste</center></b></body>");
	}
	$row = $dati->fetch_assoc();
	$ID_torneo= $row['ID_torneo'] ?? NULL;
	$turno= $row['TurnoAttuale'] ?? NULL;	
/*
echo "turno=  ".$turno;			   
echo "<br>";
echo "ID_torneo=  ".$ID_torneo;			   
echo "<br>";
*/
	   // Calcola Niscritti
    $sql= "SELECT COUNT(*) FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo ;
//echo "sql 1=  ".$sql;			   
//echo "<br>";
    $dati= $connessione->query($sql); 
    $row = $dati->fetch_array();
    $Niscritti= $row[0];
 
    if($turno > 0 && $azione != "Ritorna") {
	  //  se � partito non compare nella lista
	  echo"<script>";
	  echo"  alert(\"ATTENZIONE: IL TORNEO: '".$torneo."' E' GIA' IN CORSO\");";
	  echo"</script>";
	  goto modulo;
    }

/*--------------------------------------------------------------------*/
    		/*  IL CONTROLLO ARRIVA DAL FORM INTERNO */
		    /*  campi gi� catturati */
	if ($azione=="Iscrivi")  {
		//  CONTROLLO DELLA PRESENZA DEI DATI OBBLIGATORI
		if ($NomeA == NULL || $NomeB == NULL) {
			if(strtoupper($NomeA) != "BYE")  {	   	   
			//if($NomeA != "BYE")  {	   	   
				echo"<script>";
				echo"  alert(\"ATTENZIONE: MANCA UN NOME \");";
				echo"</script>";
				goto modulo;
			}else{
				$NomeB ="-";
			}
		}

		//$mostra= 2;
		
		// fino a 4 spazi tra nome e cognome vengono ridotti a 1
		$NomeA = str_replace("  ", " ", $NomeA); 
		$NomeB = str_replace("  ", " ", $NomeB); 
		$NomeA = str_replace("  ", " ", $NomeA); 
		$NomeB = str_replace("  ", " ", $NomeB); 
		// vengono eliminati spazi iniziali e finali
		$NomeA= trim($NomeA);
		$NomeB= trim($NomeB);
		
		// Controlla la lunghezza del nome
		if (strlen($NomeA)>24 || strlen($NomeB)>24) {
		  	echo"<script>";
		  	echo"  alert(\"ATTENZIONE: Massimo 24 Caratteri \");";
		  	echo"</script>";
			goto modulo;
		}
		
		if ($NomeA != NULL) {
			
			if ( is_numeric($NomeA) ) {
				// DEVE ESSERCI "*" COME SECONDO NOME 
				// CERCA LA COPPIA CON QUEL ID E RICAVA NOME1 E NOME2
				$sql="SELECT * FROM `brdg_cop_coppie` WHERE `torneoID`=$ID_torneo AND `coppiaID`=$NomeA " ;			   
//echo "sql=  ".$sql;			   
//echo "<br>";     
				$dati= $connessione->query($sql); 
				if(!$dati)  { 
					exit( "<body bgcolor=\"#f0e090\"><b><center>Il Numero di coppia non esiste</center></b></body>");
				}
				$row = $dati->fetch_assoc();		
				$nome1ID= $row['nome1ID'];
				$nome2ID= $row['nome2ID'];

				// CONTROLLA CHE NOME2 (con ID = $nome2ID) SIA *
				$sql= "SELECT * FROM `brdg_ind_rubrica` WHERE `ID`=$nome2ID";
//echo "sql=  ".$sql;			   
//echo "<br>";     
				$dati= $connessione->query($sql); 
				if(!$dati)  { 
					exit( "<body bgcolor=\"#f0e090\"><b><center>Il Numero di coppia non esiste</center></b></body>");
				}
				$row = $dati->fetch_assoc();
				$nome2= $row['nome'];							   
//echo "nome2=  ".$nome2;			   
//echo "<br>";     
				if ($nome2 != "*" ) {
					// IN CASO NEGATIVO VA A modulo
					echo"<script>";
					echo"  alert(\"ATTENZIONE: N. di COPPIA ERRATO \");";
					echo"</script>";
					goto modulo;
				}						
				// IN CASO AFFERMATIVO PROSEGUE RICERCANDO NomeA
				$sql= "SELECT * FROM `brdg_ind_rubrica` WHERE `ID`=$nome1ID";
				$dati= $connessione->query($sql); 
				$row = $dati->fetch_assoc();
				$NomeA= $row['nome'];							   
				// CI SIAMO RIPORTATI ALLE CONDIZIONI PRECEDENTI DI SOSTIRUZIONE DEL "*"				
					
			}else{
						
				//   CONTROLLA SE NomeA E' IN RUBRICA
				$sql= "SELECT * FROM `brdg_ind_rubrica` WHERE `nome` LIKE '".$NomeA."%'";
//echo "sql 2=  ".$sql;			   
//echo "<br>";
				$dati= $connessione->query($sql); 
				$row = $dati->fetch_assoc();
				$nome1ID= $row['ID'];
//echo "nome1 id =  ".$nome1ID;			   
//echo "<br>";
				if($nome1ID) {
					//---  TROVATO NOME A  ------------------------------
					$TrovatoA= true;
					
				}else{
					$sql="INSERT INTO `brdg_ind_rubrica` (`ID`,`nome`,`nick`,`telefono`,`email`,`associazione`)
						  VALUES (NULL,'".$NomeA."',NULL,NULL,NULL,NULL)";

//echo "sql =  ".$sql;			   
//echo "<br>";
					$dati= $connessione->query($sql);
					if ($dati==NULL) {
					   exit ("Errore di inserimento in rubrica");
					}
					$sql= "SELECT * FROM `brdg_ind_rubrica` WHERE nome = '".$NomeA."'";
					$dati= $connessione->query($sql); 
	//echo "sql =  ".$sql;			   
	//echo "<br>";
					$row = $dati->fetch_assoc();
					$nome1ID= $row['ID'];			
				}
			}	
		}
		if ($NomeB != NULL) {
		    //   CONTROLLA SE NomeB E' IN RUBRICA
		    $sql= "SELECT * FROM `brdg_ind_rubrica` WHERE nome LIKE '".$NomeB."%'";
//echo "sql 2=  ".$sql;			   
//echo "<br>";
		   	$dati= $connessione->query($sql); 
		   	    $row = $dati->fetch_assoc();
				$nome2ID= $row['ID'];
			if($nome2ID) {
				//---  TROVATO NOME B  ------------------------------
				$TrovatoB= true;
			}else{
				$sql="INSERT INTO `brdg_ind_rubrica` (`ID`,`nome`,`nick`,`telefono`,`email`,`associazione`)
		  	          VALUES (NULL,'".$NomeB."',NULL,NULL,NULL,NULL)";
	    		$dati= $connessione->query($sql);
	    		if ($dati==NULL) {
	               exit ("Errore di inserimento in rubrica");
				}
		    	$sql= "SELECT * FROM `brdg_ind_rubrica` WHERE nome = '".$NomeB."'";
		   		$dati= $connessione->query($sql); 
		   	    $row = $dati->fetch_assoc();
				$nome2ID= $row['ID'];							   
			}
		}
		
	 
		// CONTROLLA LA PRESENZA IN COPPIE DEL TORNEO
		
		// Controlla il primo nome
//*************************************************************************************************************************		
	   $sql= "SELECT COUNT(*) FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo." AND (`nome1ID`=".$nome1ID." OR `nome2ID`=".$nome1ID.")" ;
//echo "sql =  ".$sql;			   
//echo "<br>";
	   $dati= $connessione->query($sql); 
	    /*
	      if ($dati==NULL) {
		   	echo"<script>";
		  	echo"  alert(\"ATTENZIONE: ERRORE NEI DATI\");";
		  	echo"</script>";
			goto modulo;
	      }
		*/  
	   $row = $dati->fetch_array();
	   $InNome1= $row[0];
	   if($InNome1!=0) {
		  //   IL NOME1 E' GIA' NEL TORNEO
		  //   CONTROLLA SE SI TRATTA DI ISCRIZIONE SINGOLA
		    $sql= "SELECT * FROM `brdg_ind_rubrica` WHERE nome = '*'";
			$dati= $connessione->query($sql); 
			$row = $dati->fetch_assoc();
			$IDasterisco= $row['ID'];							   
		  
			$sql= "SELECT COUNT(*) FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo." AND `nome1ID`=".$nome1ID." AND `nome2ID`=".$IDasterisco ;
			$dati= $connessione->query($sql); 
//echo "sql =  ".$sql;			   
//echo "<br>";
			$row = $dati->fetch_array();
			$modifica= $row[0];
			if($modifica!=0) {
				// SI MODIFICA LA LINEA CON ASTERISCO 
				$sql= "UPDATE `brdg_cop_coppie` SET `nome2ID`='".$nome2ID."' WHERE `torneoID`=".$ID_torneo." AND `nome1ID`=".$nome1ID;
				$dati = $connessione->query($sql);
				goto modulo;		  
			}else{
		  
				echo"<script>";
				echo"  alert(\"ATTENZIONE: ALMENO IL NOME1 E' GIA' ISCRITTO AL TORNEO\");";
				echo"</script>";
				goto modulo;
			}
	   }
	   //  Se esiste il compagno controlla il secondo nome		
	   if(!$SenzaCompagno) {  
	   	   $sql= "SELECT COUNT(*) FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo." AND (`nome1ID`=".$nome2ID." OR `nome2ID`=".$nome2ID.")" ;
//echo "sql =  ".$sql;			   
//echo "<br>";
	   	   $dati= $connessione->query($sql); 
	   	   $row = $dati->fetch_array();
	   	   $InNome2= $row[0];
//echo "InNome2=  ".$InNome2;			   
//echo "<br>";
	   
	   	   if($InNome2!=0) {
		  //   IL NOME2 E' GIA' NEL TORNEO
		      echo"<script>";
		      echo"  alert(\"ATTENZIONE:  IL NOME2 E' GIA' ISCRITTO AL TORNEO\");";
		      echo"</script>";
		      goto modulo;
	       }
	   
	   }
		
			
	   //   SE LA COPPIA VA BENE LA INSERISCE NEL TORNEO
	   $coppiaID= $Niscritti+1;
	   $sql="INSERT INTO `brdg_cop_coppie`(`ID`,`torneoID`,`coppiaID`,`nome1ID`,`nome2ID`) VALUES (NULL,".$ID_torneo.",".$coppiaID.",".$nome1ID.",".$nome2ID.")";
//echo "sql 6=  ".$sql;			   
//echo "<br>";
	   $dati= $connessione->query($sql);
	   if ($dati==NULL) {
	       exit ("Errore di inserimento in iscritti");
	   }
		//$Niscritti= $Niscritti+1;
 	   $mostra= 0;	  
	}
//********************************************************************************	
//echo "scelta=  ".$scelta;			   
//echo "<br>";
?>			
			<form action="TorneoCopIscrizione_res.php?torneo=<?php echo $torneo; ?>&scelta=ok" method="POST">
				<input type="hidden" name="NomeA" value=<?php echo $NomeA; ?>>
			</form>
			
			<form action="TorneoCopIscrizione_res.php?torneo=<?php echo $torneo; ?>" method="POST">
				<input type="hidden" name="NomeA" value="">
			</form>
<?php	

	if ($azione=="Canc ID" && $scelta!="ok")  {	
		  // CONTROLLA PRESENZA DELL ID
		if (!is_numeric($NomeA)) {
		    echo"<script>";
		  	echo"  alert(\"ATTENZIONE: INSERIRE AL POSTO DEL NOME L' ID DELLA PERSONA DA CANCELLARE\");";
		  	echo"</script>";
		}

			// CHIEDE CONFERMA
		
		echo"<script>        
			var r = confirm(\"SICURO ?\");
			if (r == true) { 
				document.forms[0].submit();
			}else{
				document.forms[1].submit();				
			} 
	   </script>";		   	
		
	}
	//=========================================
	
	if ($azione=="Canc ID" && $scelta=="ok")  {	
/*
echo "NomeA  =  ".$NomeA;			   
echo "<br>";
echo "scelta  =  ".$scelta;			   
echo "<br>";
*/
		  // CANCELLA DA NOMI DEL TORNEO
		
		$sql= "DELETE  FROM `brdg_cop_coppie` WHERE `coppiaID` = ".$NomeA." AND `torneoID` = ".$ID_torneo;
		$dati = $connessione->query($sql);
		// DECREMENTA ISCRITTI 
		$Niscritti= $Niscritti-1;  
		// MODIFICA GLI ID DELLE COPPIE ISCRITTE
		$k= 1;
		$dati= $connessione->query("SELECT ID,coppiaID FROM `brdg_cop_coppie` WHERE torneoID=".$ID_torneo." ORDER BY ID"); 
		$row = $dati->fetch_assoc();
		while($row) {
			$Id= $row['ID'];
			$sql="UPDATE `brdg_cop_coppie` SET `coppiaID`='".$k."' WHERE `ID`='".$Id."'";
			$result= $connessione->query($sql);	
			$row = $dati->fetch_assoc();
			$k++;
		}
		$NomeA= "";
	}
//****************************************************************************
	if ($azione=="Modifica") {
		echo"<script>";
		echo"  alert(\"ATTENZIONE: FUNZIONE NON IMPLEMENTATA\");";
		echo"</script>";
/*
		$sql="UPDATE `brdg_ind_rubrica` SET `nome`='".$Nome."',`telefono`='".$Telefono."'
					  ,`email`='".$Mail."',`associazione`='".$Associazione."'  WHERE `nick`='".$Nick."'";		
	    $dati= $connessione->query($sql); 
//echo "sql=  ".$sql;			   
//echo "<br>";
		$NomeIns= $Nome;
		$NickIns= $Nick;
		$TelefonoIns= $Telefono;
		$MailIns= $Mail;
		$AssociazioneIns= $Associazione;	
		$mostra= 3;   
*/
	}
	if ($azione=="Ritorna") {
		header("Location: ".$home_pages);
		exit();
	}
	
	if ($azione=="Pulisci") {
		$sql= "DELETE  FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo;
		$dati = $connessione->query($sql); 
	}			 
	

modulo:

echo"<div class=\"container-tabelle\">";		
//##############################################################################################
//       ELENCO  GIOCATORI   E  FORM DI INSERIMENTO		
    echo "<br>";
	echo " <p align=\"center\"><font color=\"#ff0000\" size=\"5\">TORNEO: ".$torneo."</font></p>";	
//echo "  TORNEO: ".$NomeTorneo;
//echo "<br>";
//------------------------------------------------------


	//echo "<p align=\"center\"><font  style= \"text-align: center \" color=\"#000000\" >
		// 	 N.Coppie iscritte: ".$Niscritti."  -----  Turno: ".$turno."</font></p>";	

    //  FA L'ELENCO DELLE PERSONE GIA' INSERITE
	// ##############################################
//<body bgcolor=#55ee55>
	
echo "
<table >
<tr align=\"center\">
<td bgcolor=#f0e090> 
	<br>
";	
	//<table >
	//   <tr>
	  echo" <div align=\"center\"><strong>ELENCO DELLE COPPIE ISCRITTE</strong></div>";
	//   </tr>
   	//</table>
echo"
	<br>
	<table >
		<tr bgcolor= \"#FF9933\" >
   		<td width=\"10%\" align=\"center\"><strong>N.ID</strong></td>
       	<td width=\"45%\" align=\"center\"><strong>Nome 1</strong></td>
   		<td width=\"45%\" align=\"center\"><strong>Nome 2</strong></td>
   		</tr>
";

	$sql="SELECT * FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo." ORDER BY `coppiaID`" ;			   
//echo "sql=  ".$sql;			   
//echo "<br>";     style=\"HEIGHT: 10px\" width=\"400\" align=\"center\" border=\"1\
	$dati= $connessione->query($sql); 
	$row = $dati->fetch_assoc();		
	echo"<table >";
	$k=0;
	while($row) {
		$k++;
		$coppiaID= $row['coppiaID'];
		$nome1ID= $row['nome1ID'];
		$nome2ID= $row['nome2ID'];
		$result= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE `ID`=".$nome1ID); 
		$row = $result->fetch_assoc();		
		$Nome1= $row['nome'];
		$result= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE `ID`=".$nome2ID); 
		$row = $result->fetch_assoc();		
		$Nome2= $row['nome'];

		echo"<tr align=\"center\">";
		echo"<td width=\"10%\" align=\"center\"><b>".$coppiaID."</b></td>";
		echo"<td width=\"45%\" align=\"center\"><b>".$Nome1."</b></td>";
		echo"<td width=\"45%\" align=\"center\"><b>".$Nome2."</b></td>";
		echo"</tr>";
		
		$row = $dati->fetch_assoc();
	}	
	echo"</table>"	;
	
echo "</td>";
echo "</tr>";
echo "</table>";  


// ************************************echo "</body>";
    // MOSTRA LA FORM DI IMMISSIONE
	//  messaggio 
/*	
	echo"<br>";
	echo"<div  align=\"center\"><b><font  color=\"#0000ff\">PER MOTIVI TECNICI I PARTECIPANTI</font></b></div>";
*/	
	//echo"</div>"; // container

	//echo"<div class=\"container\">";		
echo"
	<br>
	<br>
	

<table >
<tr align=\"center\">
<td bgcolor=#f0e090> 
	<br>
    <div align=\"center\"><strong>ISCRIZIONE DELLA COPPIA</strong></div>
";	
	
/*
echo"<form onsubmit=\"javascript:return window.confirm('Sei sicuro?');\" action=\"TorneoCopIscrizione_res.php?torneo=".$torneo."\" method=\"post\">";
*/
echo"<form  action=\"TorneoCopIscrizione_res.php?torneo=".$torneo."\" method=\"post\">";

	echo"<table >";
	
		echo"<tr align=\"center\">";
		echo"<td bgcolor=\"yellow\" align=\"center\"><label>Nome 1 (ID per canc)</label><input  name=\"NomeA\" size=\"12\" value=\"".$NomeA."\"></td>";    
		echo"<td bgcolor=\"yellow\" align=\"center\"><label>Nome 2 (* = nessuno)</label><input  name=\"NomeB\" size=\"12\" value=\"".$NomeB."\"></td>";    
		echo"</tr>";

	echo"</table>";
    echo"<div ><strong>Chi e' solo, metta * come Nome2</strong></div>";
    echo"<div ><strong>Per associarsi, inserire :</strong></div>";
    echo"<div ><strong>Nome1= ID della coppia</strong></div>";
    echo"<div ><strong>Nome2= proprio nome</strong></div>";
	
	echo"<br>";
		//------------------  TASTI DI COMANDO 	      style=\"HEIGHT: 1px\" width=\"400\" align=\"center\" border=\"0\     width=\"50%\" 
		echo "<div align=\"center\">";
		echo"<input style=\"background-color:powderblue;font-size:16px;\" name=\"action\" type=\"submit\" value=\"Iscrivi\">";
		echo "&nbsp&nbsp";
	    echo"<input style=\"background-color:powderblue;font-size:16px;\" name=\"action\" type=\"submit\" value=\"Canc ID\">";
		echo "&nbsp&nbsp";
	    echo"<input style=\"background-color:powderblue;font-size:16px;\" name=\"action\" type=\"submit\" value=\"Ritorna\">";	
		echo "</div>";
echo"</form>";


echo "</td>";
echo "</tr>";
echo "</table>"; 

echo"</div>"; // container
	
$connessione->close();

 ?>
