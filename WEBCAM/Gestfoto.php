<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to capture picture from webcam with Webcam.js</title>




	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title>Ingresso scores</title>
	<meta http-equiv="X-UA-Compatible" content="IE=10" />
	<meta name="description" content="mostra tornei aperti per iscrizione">
	<meta name="author" content="ORMA">

	<!-- Mobile Specific Metas
  ================================================== -->
	
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">	
<!-- CSS 
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  ================================================== -->
	<link rel="stylesheet" href="css/base.css">
	<link rel="stylesheet" href="css/skeleton.css">
	<link rel="stylesheet" href="css/layout.css">




</head>

<!-- ================================================== -->
<body bgcolor= "green">
<body onLoad="configure();">

	<!-- php -->
<?php

  $torneo = $_GET['torneo'];
  $board  = $_GET['board'];
  $turno = $_GET['turno'];
  $Giocatore  = $_GET['giocatore'];
  $NumTavolo  = $_GET['tavolo'];
 
	
?>			

		
		
<!-- CSS -->
<style>
#video-container {
    position: relative;
    width: 100%;
    height: 80vh;
    margin: 0;
    padding: 0;
}

#my_camera {
    width: 100%;
    height: 100%;
    border: 3px solid black;
    background: #000;
    margin: 0;
    padding: 0;
}

#my_camera video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    transform: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

#results {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 5;
    display: none; /* Inizialmente nascosto */
}

#results img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border: 3px solid black;
}

/* Bottoni */
.button-container {
    position: absolute;
    top: 100px;
    right: 20px;
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.button {
    background-color: orange;
    font-size: 24px;
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-bottom: 10px;
}

/* Titoli */
.title-container {
    position: absolute;
    top: 350px;
    right: 10px;
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    margin: 0;
    padding: 0;
    display: none; /* Inizialmente nascosto */
}

h2 {
    position: relative;
    z-index: 10;
    margin: 10px 0;
    color: red;
}

/* Media query per tablet e desktop */
@media screen and (min-width: 768px) {
    #video-container {
        max-width: 1920px;
        margin: 0 auto;
    }
    
    #my_camera video {
        object-fit: contain !important;
    }
    
    .button {
        font-size: 32px;
        padding: 10px 20px;
    }
}

/* Per schermi molto piccoli */
@media screen and (max-width: 480px) {
    .button {
        font-size: 18px;
        padding: 6px 12px;
    }
}
</style>

<!-- Container del video con il bottone Scatta -->
<div id="video-container">
    <div id="my_camera"></div>
    
    <!-- Area risultati sovrapposta alla camera -->
    <div id="results"></div>
    
    <!-- Bottone Scatta -->
    <div class="button-container" id="captureButton">
 		<br><br><br><br>
        <button class="button" onClick="take_snapshot();">Scatta</button>
    </div>
    
    <!-- Bottoni dopo lo scatto -->
    <div class="button-container" id="afterCaptureButtons" style="display: none;">
        <button class="button" onClick="saveSnap()">Salva</button>
		<br>
       <button class="button" onClick="rifai()">Rifai</button>
		<br>
        <button class="button" onClick="torna_1()">Torna</button>
    </div>
    
    <!-- Titoli informativi -->
    <div class="title-container" id="titleContainer">
        <h2>FOTO DELLA MANO: <?php echo $board;?></h2>
        <h2>SE L'ANTEPRIMA È BUONA -> SALVA</h2>
    </div>
</div>

<!-- Script -->
<script type="text/javascript" src="webcamjs/webcam.min.js"></script>
<script type="text/javascript">
    // Audio per effetti sonori
    var shutter = new Audio();
    shutter.autoplay = false;
    shutter.src = navigator.userAgent.match(/Firefox/) ? 'shutter.ogg' : 'shutter.mp3';

    var capt = new Audio();
    capt.autoplay = false;
    capt.src = navigator.userAgent.match(/Firefox/) ? 'CAPTURE3.ogg' : 'CAPTURE3.mp3';
    
    // Configura e inizializza la webcam all'avvio
    window.onload = configure;
    
    function configure() {
        // Rileva se è tablet
        const isTablet = window.innerWidth >= 768 && window.innerWidth <= 1366;
        
        // Ottiene la larghezza del container
        const containerWidth = document.getElementById('my_camera').offsetWidth;
        
        // Configurazione di base
        const config = {
            width: containerWidth,
            height: containerWidth * (4/3), // mantiene aspect ratio 4:3
            jpeg_quality: 95,
            constraints: {
                facingMode: 'environment', // usa la fotocamera posteriore
                zoom: 1
            }
        };

        // Se è tablet, aumenta la risoluzione
        if (isTablet) {
            config.dest_width = 2000;
            config.dest_height = 2000 * (4/3);
        }

        // Gestione errori
        Webcam.on('error', function(err) {
            console.error('Errore fotocamera:', err);
            alert('Errore nell\'accesso alla fotocamera: ' + err);
        });

        // Applica la configurazione
        Webcam.set(config);
        
        // Attacca la webcam al container
        try {
            Webcam.attach('#my_camera');
        } catch (error) {
            console.error('Errore nell\'inizializzazione della fotocamera:', error);
            alert('Impossibile inizializzare la fotocamera');
        }
    }

    function take_snapshot() {
        try {
            // Riproduci effetto sonoro
            shutter.play().catch(e => console.log('Audio non supportato:', e));
            
            // Scatta la foto
            Webcam.snap(function(data_uri) {
                // Mostra l'anteprima
                document.getElementById('results').innerHTML = `
                    <img id="imageprev" src="${data_uri}" alt="Foto scattata" />`;
                
                // Mostra l'anteprima e nascondi la camera
                document.getElementById('results').style.display = 'block';
                
                // Cambia i bottoni visibili
                document.getElementById('captureButton').style.display = 'none';
                document.getElementById('afterCaptureButtons').style.display = 'flex';
                
                // Mostra i titoli
                document.getElementById('titleContainer').style.display = 'flex';
            });
        } catch (error) {
            console.error('Errore durante lo scatto:', error);
            alert('Errore durante lo scatto della foto');
        }
    }
    
    function rifai() {
        // Nasconde l'anteprima e mostra di nuovo la camera
        document.getElementById('results').style.display = 'none';
        document.getElementById('results').innerHTML = '';
        
        // Ripristina i bottoni iniziali
        document.getElementById('captureButton').style.display = 'flex';
        document.getElementById('afterCaptureButtons').style.display = 'none';
        
        // Nasconde i titoli
        document.getElementById('titleContainer').style.display = 'none';
    }
    
    function saveSnap() {
        // Riproduci suono
        capt.play();

        // Ottieni l'immagine base64
        var base64image = document.getElementById("imageprev").src;
        
        var torneo = <?php echo json_encode($torneo, JSON_HEX_TAG); ?>;    
        var board = <?php echo json_encode($board, JSON_HEX_TAG); ?>;    
        
        // Formatta il numero della board correttamente
        var boardParam = board < 10 ? '0' + board : board;
        
        // Carica l'immagine
        Webcam.upload(base64image, 'upload.php?torneo=' + torneo + '&board=' + boardParam, function(code, text) {
            console.log('Salvataggio completato');
            alert("LA BOARD: " + board + " È STATA SALVATA");
            Webcam.reset();
            torna_1();
        });
    }
    
    function torna_1() {
        window.location.href = document.referrer+'&NomeTorneo=<?php echo $torneo; ?>&NumTurno=<?php echo $turno; ?>&tavolo=<?php echo $NumTavolo; ?>&giocatore=<?php echo $Giocatore;?>&boardAtt=<?php echo $board;?>&orig=GestFoto';
        //window.location.href = '../BridgeCoppieR2/TorneoCopScoresContrattoTav.php?NomeTorneo=<?php echo $torneo;?>&NumTurno=<?php echo $turno;?>&tavolo=<?php echo $NumTavolo;?>&giocatore=<?php echo $Giocatore;?>&boardAtt=<?php echo $board;?>&orig=GestFoto';
    }
</script>
	
</body>
</html>
