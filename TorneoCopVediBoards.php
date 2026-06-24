
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2">
</head>


<?php  

// inclusione del file di connessione
include "dbConnessione.php";
echo "<body bgcolor=\"green\">";

//  RICEVE IL PARAMETRO TORNEO 
	$azione = $_POST['action'];
	$NomeTorneo= $_GET['NomeTorneo'];
	//$turnoOsservato= $_GET['turno'];
	// riceve da se stesso il tavolo
	$board= $_POST['board'];
echo"<div align=\"center\">";	
echo" NomeTorneo: ".$NomeTorneo;
echo"</div>";
echo"<br>";


	
//  CONTROLLA LA PRESENZA DEL TORNEO INSERITO NELLA TABELLA brdg_ind_tornei
	//  E RICAVA IL SUO ID
	$sql= "SELECT * FROM `brdg_cop_tornei` WHERE `NomeTorneo`= '".$NomeTorneo."'";
	$dati = $connessione->query($sql);
	$row = $dati->fetch_assoc(); 
	$ID_torneo= $row['ID_torneo'];
	//$turno= $row['TurnoAttuale'];	
	//$NboardsXturno= $row['BoardsXturno'];		
    if (!is_numeric($ID_torneo)) {
		// chiusura della connessione
		$connessione->close();
		exit("<body bgcolor=\"#a0eea0\"><b><center>Il torneo \"". $torneo."\" non esiste</center></b></body>");
	 }
	 
	//  ESEGUE LE AZIONI
	//****************************************
	if($azione=="Vai")  {
		//if(strlen($board)==1)	$board= "0".$board;
	  	header("Location:".$home_archive."/VediFoto.php?torneo=$NomeTorneo&board=$board");
		exit();
	}



//ççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççç


// Funzione per creare un'anteprima ridimensionata mantenendo le proporzioni
function createThumbnail($source_path, $max_width, $max_height) {
    list($width, $height) = getimagesize($source_path);
    
    // Calcola le nuove dimensioni mantenendo le proporzioni
    $ratio = min($max_width/$width, $max_height/$height);
    $new_width = $width * $ratio;
    $new_height = $height * $ratio;
    
    // Crea l'immagine ridimensionata
    $thumb = imagecreatetruecolor($new_width, $new_height);
    
    // Determina il tipo di immagine e carica quella originale
    $image_info = getimagesize($source_path);
    switch ($image_info[2]) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($source_path);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($source_path);
            break;
    }
    
    // Ridimensiona l'immagine
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, 
                       $new_width, $new_height, $width, $height);
    
    return $thumb;
}

// HTML e CSS per la galleria
echo '<style>
    .gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 2px;

  background-color: #C0C0C0; 
  border: 2px solid #A9A9A9; 
  border-radius: 8px;        
  box-sizing: border-box;   
  max-width: 690px; 

    }
    .thumb-container {
        border: 1px solid #ddd;
        padding: 5px;
        text-align: center;
    }
    .thumb-container a {
        text-decoration: none;
        color: #333;
    }
    .thumb-container img {
        display: block;
        max-width: ' . $thumb_width . 'px;
        max-height: ' . $thumb_height . 'px;
        transition: opacity 0.3s;
    }
    .thumb-container img:hover {
        opacity: 0.8;
    }
    .thumb-container .filename {
        margin-top: 5px;
        font-size: 0.9em;
    }
</style>';

// Directory contenente le immagini

$image_dir = $home_archive.'/upload/tornei/'.$NomeTorneo.'/';
// Dimensione massima delle anteprime
$thumb_width = 300;
$thumb_height = 200;

// Estensioni di file immagine consentite
$allowed_types = array('jpg', 'jpeg', 'png', 'gif');

// Ottieni tutti i file nella directory
$files = scandir($image_dir);



echo '<div class="gallery">';

foreach ($files as $file) {
    // Ignora . e ..
    if ($file == '.' || $file == '..') continue;
    
    // Verifica l'estensione del file
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_types)) continue;
    
    $filepath = $image_dir . $file;
    
    // Crea l'anteprima
    $thumb = createThumbnail($filepath, $thumb_width, $thumb_height);
    
    echo '<div class="thumb-container">';
    // Crea il link all'immagine originale
    echo '<a href="' . htmlspecialchars($filepath) . '" target="_blank">';
    
    // Inizia il buffer di output
    ob_start();
    // Output dell'immagine direttamente nel buffer
    imagejpeg($thumb);
    // Prendi il contenuto del buffer e codificalo in base64
    $image_data = ob_get_clean();
    $base64_image = base64_encode($image_data);
    // Mostra l'immagine
    echo '<img src="data:image/jpeg;base64,' . $base64_image . '" alt="' . htmlspecialchars($file) . '">';
    echo '<div class="filename">' . htmlspecialchars($file) . '</div>';
    echo '</a>';
    echo '</div>';
    
    // Libera la memoria
    imagedestroy($thumb);
}

//echo '</div>';

//ççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççççç

	//*******************************

/*	if($azione=="Indietro")  {
    	// RICAVA LA PSWD
    	$dati = $connessione->query("SELECT * FROM brdg_cop_pswd WHERE 1");
      	$row = $dati->fetch_assoc(); 
       	$PSWD= $row['password'];		
    	$PSWDcri=~$PSWD;
	  	header("Location: TorneoCopAdmin.php?torneo=$NomeTorneo&password=$PSWDcri&msg=Torneo: ");
		exit();
	}
*/	
	
//------------------------------------------------------
	//echo "Turno in esame->".$turnoOsservato;
	echo  "<br>";
	echo  "<br>";
	echo  "<br>";
//------------------------------------------------------

	// RICAVA LA PSWD
	$dati = $connessione->query("SELECT * FROM brdg_cop_pswd WHERE 1");
	$row = $dati->fetch_assoc(); 
	$PSWD= $row['password'];		
	
    // chiusura della connessione
    $connessione->close();
	$AzioneBottone="\"document.forms[0].submit()\"";	
//<td style="align:center;"><input style="background-color:lightgrey;font-size:16px;" name="action" type="submit" value="Indietro"></td>
?>
 	<form action="TorneoCopAdmin.php?torneo=<?php echo $NomeTorneo ?>" method="POST">
		<input type="hidden" name="password" value="<?php echo $PSWD ?>"/>
	</form>
	
  <form action="TorneoCopVediBoards.php?NomeTorneo=<?php echo $NomeTorneo; ?>" method="post">	
	<table style="width:320px;" align="center" border="1">
        <tr align="center">
          <td style="width:220px;background-color:orange;"><b>Controlla la Board: <b></td>
          <td style="font-size:16px;"><input  name="board" size="1" type="numeric" value=""></td>
	      <td style="text-align: center;"><input style="background-color:lightgrey;font-size:16px;" name="action" type="submit" value="Vai"></td>
        </tr>
    </table>	
  </form>	
  	
	<table style="HEIGHT: 10px" width="10" align="center" border="1">
      <tbody>
        <tr align="center">
         <td ><button style="background-color:LightGray;align:center;" onclick=<?php echo $AzioneBottone; ?>; ">Indietro</button></td>		           
        </tr>
      </tbody>
    </table>
</div>	
	
