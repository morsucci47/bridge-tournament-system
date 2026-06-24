<?php  
// inclusione del file di connessione
include "dbConnessione.php";
	
	// ##############################################
	// MOSTRA LA LISTA DEI TORNEI 
	// ##############################################
	$Origine= $_GET['orig'];
	$Torneo	= $_GET['torneo'];

	echo "
<body bgcolor=\"#55ee55\">
<table width=\"100\" align=\"center\" border=\"1\">
<tr align=\"center\">
<td bgcolor=\"#f0e090\"> 
	<br>
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"500\">
	 <tr>
      	<td colspan=\"4\" align=\"center\"><b> ELENCO DEI TORNEI </b></td>
      	
     </tr>
    </table>
    
	<br>
	<table align=\"center\" border=\"1\" cellspacing=\"1\" width=\"460\">
	 <tr bgcolor= \"#FF9933\" >
       <!-- <td width=\"4%\" align=\"center\"><strong>N.</strong></td> -->
       <td width=\"10%\" align=\"center\"><strong>N.ID</strong></td>
       <td width=\"50%\" align=\"center\"><strong>Nome Torneo</strong></td>
       <td width=\"10%\" align=\"center\"><strong>Turn</strong></td>
       <td width=\"8%\" align=\"center\"><strong>Su</strong></td>
       <td width=\"8%\" align=\"center\"><strong>Ntav</strong></td>
       <td width=\"14%\" align=\"center\"><strong>Stato</strong></td>
     </tr>
	 ";
//echo "Origine=  ".$Origine;			   
//echo "<br>";
//echo "Torneo=  ".$Torneo;			   
//echo "<br>";

	if($Torneo=="Archivio") {
		if($Origine=="Accedi")  {
			$sql="SELECT * FROM `brdg_cop_tornei` WHERE `Stato`=1 ORDER BY `ID_torneo` DESC LIMIT 10" ;	
//echo "sql=  ".$sql;			   
//echo "<br>";
		}else{
			$sql="SELECT * FROM `brdg_cop_tornei` WHERE TRUE ORDER BY ID_torneo DESC LIMIT 10" ;			
		}
	}else{
		if($Origine=="Accedi")  {
			$sql="SELECT * FROM `brdg_cop_tornei` WHERE `Stato`=1 ORDER BY `ID_torneo` DESC" ;	
		}else{
			$sql="SELECT * FROM `brdg_cop_tornei` WHERE TRUE ORDER BY ID_torneo DESC" ;
		}
	}	
	$dati= $connessione->query($sql); 
	$row = $dati->fetch_assoc();		
	echo"<table style=\"HEIGHT: 10px\" width=\"460\" align=\"center\" border=\"1\>";
	$k=0;
	while($row) {
		$k++;
		$ID= $row['ID_torneo'];
		$NomeTorneo= $row['NomeTorneo'];
		$TurnoAttuale= $row['TurnoAttuale'];
		$NumTurni= $row['Turni'];
		$Stato= $row['Stato'];
		
			$sql="SELECT MAX(tavolo) AS 'max_tav' FROM `brdg_cop_scores` WHERE  `torneoID`=$ID" ;			   
			$dati_0= $connessione->query($sql); 
			$row_0 = $dati_0->fetch_assoc();		
			$NumTav= $row_0['max_tav'];
//echo "sql=  ".$sql;			   
//echo "<br>";

		if($Stato==0) {
			if($TurnoAttuale==0) {
				$FaseTorneo="Iscriz";
			}else{
				$FaseTorneo="Gioco";			
			}
		}else{   //stato = 1
			$FaseTorneo="Chiuso";					
		}

		
		echo"<tr align=\"center\">";
		echo"<td width=\"10%\" align=\"center\"><b>".$ID."</b></td>";
		echo"<td width=\"50%\" align=\"center\"><b>".$NomeTorneo."</b></td>";
		echo"<td width=\"10%\" align=\"center\"><b>".$TurnoAttuale."</b></td>";
		echo"<td width=\"8%\" align=\"center\"><b>".$NumTurni."</b></td>";
		echo"<td width=\"8%\" align=\"center\"><b>".$NumTav."</b></td>";
		echo"<td width=\"14%\" align=\"center\"><b>".$FaseTorneo."</b></td>";
		echo"</tr>";
		
		$row = $dati->fetch_assoc();
	}	
	echo"</table>"	;
	
echo "</td>";
echo "</tr>";
//echo "</table>";  

	if($Origine=="admin") {
		// RICAVA LA PSWD
		$dati = $connessione->query("SELECT * FROM brdg_cop_pswd WHERE 1");
		$row = $dati->fetch_assoc(); 
		$PSWD= $row['password'];		
		//$PSWDcri=~$PSWD;
		
		//$URL= "TorneoCopAdmin.php?torneo=@&password=$PSWDcri";
		//$AzioneBottone="\"window.location.href ='$URL'\"";	
		$AzioneBottone="\"document.forms[0].submit()\"";	
	}else{
		$AzioneBottone="\"window.history.go(-1)\"";			
	}

					
    // chiusura della connessione      
    $connessione->close();
		
?>
	<form action="TorneoCopAdmin.php?torneo=*" method="POST">
		<input type="hidden" name="password" value="<?php echo $PSWD ?>"/>
	</form>
	
	<!--<table style="HEIGHT: 10px" width="10" align="center" border="1">-->
     <tbody style="background-color: #FF9933;" >
        <tr align="center">
          <td ><button style="background-color:LightGray;align:center;" onclick=<?php echo $AzioneBottone; ?>; >Indietro</button></td>
        </tr>
     </tbody>
    <!--</table>-->
</table>  


</body> 
