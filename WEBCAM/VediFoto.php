<!DOCTYPE html>
<head>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>

	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title>Ingresso scores</title>
	<meta http-equiv="X-UA-Compatible" content="IE=10" />
	<meta name="description" content="mostra tornei aperti per iscrizione">
	<meta name="author" content="ORMA">

	<!-- Mobile Specific Metas
  ================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=10">

	<!-- CSS
  ================================================== 
	<link rel="stylesheet" href="css/base.css">
	<link rel="stylesheet" href="css/skeleton.css">
	<link rel="stylesheet" href="css/layout.css">
-->
</head>
<?php
  $torneo = $_GET['torneo'];
  $board  = $_GET['board'];
  
  $turno = $_GET['turno'];
  $NumTavolo  = $_GET['tavolo'];
  $Orig  = $_GET['orig'];


echo "Torneo: ".$torneo." - Board: ".$board  ;
echo  "<br>";

	if(strlen($board)==1) {
		$board_ = "0".$board;
	}else{
		$board_ = $board;
	}
 
	$filename= './upload/tornei/'.$torneo.'/N_'.$board_.'.jpeg?r='.time();
	//<button style="background-color:LightGray;align:center; font-size:16px;" onclick="history.go(-1);">Indietro</button>
?>



<p><img src="<?php echo $filename; ?>" border="1" align="center" ></p>
<br>
	<script>
    function torna() {
	   window.location.href = document.referrer+'&NomeTorneo=<?php echo $torneo; ?>&NumTurno=<?php echo $turno; ?>&tavolo=<?php echo $NumTavolo; ?>&orig=VediFoto';
    }
	</script>
 <button style="background-color:LightGray;align:center; font-size:32px;" onclick="torna()">Indietro</button>
 <br>
 <br>

<?php
 if($Orig!="Giocatore")  {   // l'amministratore puo rifare la foto
	echo"
	<form action=\"./Gestfoto.php?torneo=$torneo&turno=$turno&board=$board&tavolo=$NumTavolo\"  method=\"post\">
		<input style=\"background-color:powderblue;\" type=\"submit\" value=\"RIFARE FOTO\" name=\"action\">
	</form>
	";
 }

?>
</html>

<?php

/*
if (isset($_POST['cancella_file'])) {
    $file_da_cancellare = $_POST['file_da_cancellare']; // Assumiamo che questo valore arrivi da un form
    if (unlink($file_da_cancellare)) {
        echo "File cancellato con successo!";
    } else {
        echo "Errore durante la cancellazione del file.";
    }
}
*/
?>