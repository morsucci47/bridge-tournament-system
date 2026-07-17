<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>

	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title>Tornei a coppie: accesso</title>
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
	==================================================  -->
	<link rel="shortcut icon" href="images/favicon.ico">

</head>

<!-- ================================================== text="000000"-->

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


<body bgcolor=#55ee55>
	<br>
	<br>
<div class="container-tabelle">
	
<form action="TorneoCopScoresContrattoTav.php?orig=accesso" method="post">
	
<?php 
//  SE IL CONTROLLO VIENE DA SCORES RICEVE IL NOME DEL TORNEO
	$TorneoScelto= $_GET['Torneo'] ?? NULL;
     
//***************************************************************************
//		cerca I tornei aperti con turno attuale > 0																	 
//***************************************************************************
include_once "dbConnessione.php";

		if(!$TorneoScelto)  {
			$sql= "SELECT * FROM `brdg_cop_tornei`  WHERE `TurnoAttuale`>0 AND `Stato`=0 ORDER BY ID_torneo DESC LIMIT 1"; 
			$dati = $connessione->query($sql);
			$row = $dati->fetch_assoc(); 
			$TorneoScelto= $row['NomeTorneo'] ?? NULL;
		}


    	$sql= "SELECT * FROM `brdg_cop_tornei` WHERE `TurnoAttuale`>0 AND `Stato`=0 ORDER BY `ID_torneo`";
    	$dati = $connessione->query($sql);
    	$row = $dati->fetch_assoc(); 
echo "<table align=\"center\" width=\"260\"> ";
echo"<tr >";
echo"<td align=\"center\" colspan=\"5\">";		
		if(!$row) {
			echo"<div  style=\"font-size:24px; background-color: yellow; color:red;\"  align=\"center\"><strong>NON CI SONO TORNEI IN CORSO</strong></div>";
			$NumCoppie= 0;
			goto FormTorneoChiuso;
		}else{
			echo"<div   style=\"font-size:24px; background-color: yellow; color: white;\" align=\"center\"><strong>TORNEI IN CORSO</strong></div>";			
		}
echo"</td>";		
echo"</tr>";    		
		//echo"<br>";

//echo "<div class=\"container\">";		
//echo "<table align=\"center\" width=\"260\">";
echo "<tbody bgcolor=\"#ffa500\">";
																 
		echo"<tr >";
		echo"<td  align=\"center\"><strong>Torneo</strong></td>";
		echo"<td  align=\"center\"><strong>N.Turni</strong></td>";
		echo"<td  align=\"center\"><strong>B.xT.</strong></td>";
		echo"<td  align=\"center\"><strong>Tipo</strong></td>";
		echo"<td  align=\"center\"><strong>N.Tav</strong></td>";
		echo"</tr>";    		
		$k=1;
		while($row) {	
			$NomeTorneo= $row['NomeTorneo'];
			$Turni= $row['Turni'];
			$BoardsXturno= $row['BoardsXturno'];
			$Tipo= $row['Tipo'];
			$NumTavoli= $row['Tavoli'];
            echo"<tr align=\"left\">";
            //echo"<td border=\"1\"><input type=\"radio\" name=\"Torneo_".$k."\"> <strong>".$NomeTorneo."</strong> </td>";
            echo"<td border=\"1\"><input type=\"radio\" 
										class=\"torneo-radio\" 
										name=\"selezione_torneo\" 
										data-nome=\"".$NomeTorneo."\" ";
										if($NomeTorneo == $TorneoScelto) {
											echo " checked ";
											$kTorneo= $k;
										}	
										echo"<strong>".$NomeTorneo."</strong> </td>";



            //echo"<td  align=\"center\">$NomeTorneo</td>";
			echo"<td  align=\"center\">$Turni</td>";
			echo"<td  align=\"center\">$BoardsXturno</td>";
			if($Tipo==1)  {
				echo"<td align=\"center\">How.</td>";
			}else if($Tipo==2){
				echo"<td align=\"center\">Mit.</td>";
			}
			echo"<td  align=\"center\">$NumTavoli</td>";
        	echo"</tr>";    		
		    $row = $dati->fetch_assoc(); 
			$Tornei[$k]= $NomeTorneo;
			$Tavoli[$NomeTorneo]= $NumTavoli;
			
			$k++;
		}

echo "</tbody>";
echo "</table>";
?>
<script>
document.addEventListener('DOMContentLoaded', () => {
	const mappaTavoli = <?php echo json_encode($Tavoli); ?>;
	const torneoScelto = <?php echo json_encode($TorneoScelto); ?>;
	const kTorneo = <?php echo json_encode($kTorneo); ?>;


    const radios = document.querySelectorAll('.torneo-radio');
    const inputDestinazione = document.getElementById('input-torneo');
	const radioSelezionato = document.querySelector('.torneo-radio:checked');

	const selectTavolo = document.getElementById('tavoli-select'); // Uso dell'ID

	//if (!radioSelezionato) {
      //  inputDestinazione.value = "";
    //}
    if (torneoScelto) {
        inputDestinazione.value = torneoScelto;
    }
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Quando un radio viene selezionato, scrive il suo data-nome nell'input
            if (this.checked) {
                inputDestinazione.value = this.getAttribute('data-nome');
 

				const nomeTorneo = this.getAttribute('data-nome');
                
                // 2. Recuperiamo il numero tavoli specifico per questo torneo
                const numTavoli = mappaTavoli[nomeTorneo];
                
                console.log("Torneo selezionato:", nomeTorneo);
                console.log("Numero tavoli trovato:", numTavoli);

                if (numTavoli !== undefined) {				


					// Svuota le opzioni attuali
					selectTavolo.innerHTML = '';
			
					// Genera nuove opzioni da 1 a numTavoli
					for (let i = 1; i <= numTavoli; i++) {
						let opt = document.createElement('option');
						opt.value = i;
						opt.innerHTML = i;
						selectTavolo.appendChild(opt);
					}	
				}	

			}

		});
    });
});
</script>
<?php
//*****************************
    //  CONTROLLA SE IL TORNEO INSERITO ESISTE  NELLA TABELLA brdg_ind_tornei
	//  E RICAVA IL SUO ID 
    $sql= "SELECT * FROM brdg_cop_tornei WHERE NomeTorneo=\"".$TorneoScelto."\"";
    $dati = $connessione->query($sql);
    $row = $dati->fetch_assoc(); 
    $ID_torneo= $row['ID_torneo'] ?? NULL;

	//********************************************
	//   CREA LA LISTA DEI TAVOLI
	//********************************************

/*
<tr align=\"center\">
<td colspan=\"3\" bgcolor=\"#FFFF00\" align=\"center\"><strong>Selezione (* : vedi la lista storica dei tornei)</strong></td>
</tr>
<p><strong>NON ESISTE</strong></p>
*/
	if($ID_torneo) {
		$sql="SELECT COUNT(*) FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo." ORDER BY `coppiaID`" ;			   
//echo "sql=  ".$sql;			   
//echo "<br>";
		$dati= $connessione->query($sql); 
		
		if($dati) $row = $dati->fetch_array();	

		$NumCoppie= $row[0];
	}else{
		$NomeTorneo= "----";
		$NumCoppie= 2;
	}	
echo"
<table align=\"center\" width=\"260\">
<tbody>
<tr align=\"center\">
<td colspan=\"3\" bgcolor=\"#FFFF00\">";
	//  SE E' STATO SCELTO UN TORNEO NON ESISTENTE LO SEGNALA
/*
	if($TorneoScelto!=NULL && $NomeTorneo != $TorneoScelto) {
		echo" 
		<p ><strong>IL TORNEO $TorneoScelto NON ESISTE</strong></p>
		";
	};	
	
	
	
	
*/
//echo"<input name=\"torneo\" size=\"18\" style=\"font-size:24px;\" type=\"text\" value=\"".$NomeTorneo."\">
echo"<input id=\"input-torneo\" name=\"torneo\" size=\"18\" style=\"font-size:24px;\" type=\"text\" value=\"<?php echo htmlspecialchars($NomeTorneo); ?>\">";
//echo"<input id=\"input-torneo\" name=\"torneo\" size=\"18\" style=\"font-size:24px;\" type=\"text\" value=\"htmlspecialchars($NomeTorneo)\">";

echo"
</td>
<tr align=\"center\">
<td colspan=\"3\" bgcolor=\"#FFFF00\" style=\"font-size:24px;\" align=\"center\"><strong>Num Tavolo</strong></td>
</tr>";
FormTorneoChiuso:

echo"
<tr align=\"center\" >

<td width:15% bgcolor=\"yellow\" style=\"vertical-align: top;\">

<!--	<body style=\"text-align: left;\"><input style=\"background-color:lightgrey;font-size:16px;\" name=\"action\" type=\"submit\" value=\"Home\"></body> -->

	<input style=\"background-color: lightgrey;font-size:16px; display: inline;\" name=\"action\" type=\"submit\" value=\"Home\">


</td>";
if($NumCoppie > 0) {
	echo"
	<td width:70%  bgcolor=\"yellow\">
	<select id=\"tavoli-select\" name=\"tavolo\" style=\"font-size:24px; width:80px;\">";
		
			for ($k=1; $k<=$NumCoppie/2 ; $k++) {        	 
				echo  "<option  value='$k' >$k</option>";
				;
			}

	echo "</select>		  
	</td>

	<td width:15% bgcolor=\"yellow\" style=\"vertical-align: top;\">
		<body style=\"text-align: right;\"><input style=\"background-color: powderblue;font-size:16px;\" name=\"action\" type=\"submit\" value=\"  Vai  \"></body>
	</td>";

}
echo"
</tr>

</tbody>
</table>
	
<br>
<br>
<br>

";

//***************************************************************************
//		cerca IL TORNEO CHIUSO CON ID MAGGIORE	/  data inizio maggiore															 
//***************************************************************************
    	//$sql= "SELECT * FROM `brdg_cop_tornei` WHERE `ID_torneo`=(SELECT MAX(ID_torneo)  FROM `brdg_cop_tornei` WHERE `Stato`=1)";
    	$sql= "SELECT * FROM `brdg_cop_tornei` WHERE `Inizio`=(SELECT MAX(Inizio)  FROM `brdg_cop_tornei` WHERE `Stato`=1)";
    	$dati = $connessione->query($sql); 
    	$row = $dati->fetch_assoc(); 
echo "<table align=\"center\" width=\"260\"> ";

echo"<tr >";
echo"<td align=\"center\" colspan=\"5\">";		

		if(!$row) {
			echo"<div  style=\"font-size:24px; color:red;\"  align=\"center\"><strong>NON CI SONO TORNEI CHIUSI</strong></div>";
		}else{
			echo"<div   style=\"font-size:24px;background-color: yellow;\" align=\"center\"><strong>ULTIMO TORNEO CHIUSO</strong></div>";			
		}
echo"</td>";		
echo"</tr>";    		
		//echo"<br>";

//echo "<table align=\"center\" width=\"260\">";
echo "<tbody bgcolor=\"#ffa500\">";
																 
		echo"<tr >";
		echo"<td  align=\"center\"><strong>Torneo</strong></td>";
		echo"<td  align=\"center\"><strong>N.Turni</strong></td>";
		echo"<td  align=\"center\"><strong>B.xT.</strong></td>";
		echo"<td  align=\"center\"><strong>Tipo</strong></td>";
		echo"<td  align=\"center\"><strong>N.Tav</strong></td>";
		echo"</tr>";    		
		//$k=1;
		while($row) {	
			$NomeTorneoChiuso= $row['NomeTorneo'];
			$Turni= $row['Turni'];
			$BoardsXturno= $row['BoardsXturno'];
			$Tipo= $row['Tipo'];
			$NumTavoli= $row['Tavoli'];
            echo"<tr>";
           // echo"<td><input type=\"checkbox\" name=\"Torneo_".$k."\"><label for=\"Torneo".$k."\">$NomeTorneo</label></td>";
            echo"<td  align=\"center\">$NomeTorneoChiuso</td>";
			echo"<td  align=\"center\">$Turni</td>";
			echo"<td  align=\"center\">$BoardsXturno</td>";
			if($Tipo==1)  {
				echo"<td align=\"center\">How.</td>";
			}else if($Tipo==2){
				echo"<td align=\"center\">Mit.</td>";
			}
			echo"<td  align=\"center\">$NumTavoli</td>";
        	echo"</tr>";    		
		    $row = $dati->fetch_assoc(); 
			//$Tornei[$k]= $NomeTorneo;
			//$k++;
		}

echo "</tbody>";
echo "</table>";
echo"
<table align=\"center\" width=\"260\">
<tbody>
<tr align=\"center\">
<td bgcolor=\"#FFFF00\" align=\"center\"><strong>Nome o ID (con * vedi tutti i tornei chiusi)</strong></td>
</tr>
<tr align=\"center\">
<td bgcolor=\"#FFFF00\">

<input name=\"torneo_chiuso\" size=\"18\" style=\"font-size:24px; display: inline;\" type=\"text\" value=\"".$NomeTorneoChiuso."\">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input style=\"background-color: powderblue;font-size:16px; display: inline;\" name=\"action\" type=\"submit\" value=\"Accedi\">
<br>
</td>
</tr>

</tbody>
</table>
";
//

//echo"<div  align=\"center\">";
//echo"<input style=\"background-color: powderblue;font-size:16px;\" name=\"action\" type=\"submit\" value=\"Accedi\">";
//echo"</div>";

//</table>
?>

</form>
</div>

</body>
</html>
