<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>

	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title>Tornei Aperti</title>
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

echo"<body bgcolor=#55ee55 text=\"000000\">";

//***************************************************************************
//		cerca I tornei aperti non iniziati															 
//***************************************************************************
include "dbConnessione.php";

	$azione = $_GET['azione'];
	$azione = "Iscrizione";
//echo "azione = ".$azione 	;
//echo"<br>";	
	$VaiTorneo = $_POST['torneo'];
	//$NumGiocatore = $_POST['NumGiocatore'];
	$Nome = $_POST['Nome'];
//echo "Nome = ".$Nome 	;
//echo"<br>";	
	if ($azione!=NULL) {
    	$sql= "SELECT * FROM brdg_cop_tornei WHERE TurnoAttuale=0 AND  Stato=0 ORDER BY `ID_torneo`" ;
    	$dati = $connessione->query($sql);
    	$row = $dati->fetch_assoc();
		$k=1;
		$trovato= false;
		while($row) {	
			$NomeTorneo= $row['NomeTorneo'];
			$CheckTorneo = $_GET['Torneo'.$k];
//echo"Nome	".$NomeTorneo	;	
//echo"CheckTorneo	".$CheckTorneo	;	
//echo"<br>";
			if($CheckTorneo=="on") {
				$trovato= true;
			    break;
			}
		    $row = $dati->fetch_assoc(); 
			$k++;
		}
		if($trovato==false) $NomeTorneo="";
	}

//*********************************************************
//  azione vai
	
//#########################################################
	
	if($azione=="Iscrizione")  {
		if($NomeTorneo!=""){
			if (str_contains($NomeTorneo, "Due Torri")) {
			 	header("Location: ".$home_proc."/TorneoCopIscrizione_res_categ.php?torneo=$NomeTorneo");
			}else{
				header("Location: ".$home_proc."/TorneoCopIscrizione_res.php?torneo=$NomeTorneo");
			}		 
			exit();
		}
	}

//modulo:	

//***************************************************************************
//		cerca I tornei aperti con turno attuale 0																	 
    	$sql= "SELECT * FROM brdg_cop_tornei WHERE TurnoAttuale=0 AND  Stato=0 ORDER BY `ID_torneo` ";
    	$dati = $connessione->query($sql);
    	$row = $dati->fetch_assoc(); 
    	//$ID_torneo= $row['ID_torneo'];
		//$NomeTorneo=
  //<div title="tornei" style="HEIGHT: 30px" align="center"><b>TORNEI A COPPIE</b></div>

//***************************************************************************
 ?>	
 <br>
 <br>
 <br>

<div class="container-tabelle">
   <div  align="center"  bgcolor= "orange"><strong><h3>TORNEI A COPPIE</h3></strong></div> 
	<br>
	<br>
<table > 
<tr align="center">
<td bgcolor= "orange">

    <table  >
    	<tr align="center">
    	<td align="center"><strong>ISCRIZIONE</strong></td>
    	</tr>
    </table>		
    <br>
    <div title="tornei" style="HEIGHT: 30px" align="center"><strong>Tornei in partenza</strong></div>

    <form  action="<?php echo $home_proc;?>/TorneoCopTorneiAperti.php"   method="get" >
       <table border="1">
       <tbody bgcolor="#FFFF00">
<?php 
		 	$k=1;
		 	while($row) {	
				$NomeTorneo= $row['NomeTorneo'];
				$Ora= $row['Ora'];
				echo"<tr align=\"left\">";
            	//echo"<td><input type=\"checkbox\" name=\"Torneo".$k."\"><label for=\"Torneo".$k." \">$NomeTorneo</label></td>";
            	echo"<td border=\"1\"><input type=\"checkbox\" name=\"Torneo".$k."\"><strong>".$NomeTorneo."</strong> </td>";
				echo"<td>Inizio: $Ora</td>";			
        		echo"</tr>";    		
        		echo"<tr>";
				echo"<td>.</td>";    		
				echo"<td>.</td>";    		
        		echo"</tr>";    		
			
				$row = $dati->fetch_assoc(); 
				$k++;
			}

			// chiusura della connessione
			$connessione->close();
?>
       </tbody>
	   </table>
	   <br>
	   <br>
       <div align="center">
            <input style="background-color:powderblue;font-size:16px;" name="azione" type="submit" value="Iscrizione">
       </div>
	</form>	     
</td>
</tr>
</table>  

</div><!-- container -->


</body>
</html>