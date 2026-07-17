<?php
include "dbConnessione.php";

// 1. Inizializzazione variabili e recupero input
$azione = $_POST['action'] ?? '';
$NomeTorneo = $_GET['torneo'] ?? null;
$Modifica = $_GET['modifica'] ?? 0;
$msg = $_GET['msg'] ?? '';
$error_msg = "";
$Niscritti= 0;
$Ntavoli= 0;

$AzioneBottone="\"document.forms[0].submit()\"";	
	//$msg="Nessuna azione per :";

// Recupero Password admin (come nel tuo originale)
$res_pswd = $connessione->query("SELECT password FROM brdg_cop_pswd LIMIT 1");
$row_pswd = $res_pswd->fetch_assoc();
$PSWD = $row_pswd['password'];

// 2. Logica di ricerca Torneo (se ID numerico)
if (is_numeric($NomeTorneo)) {
    $stmt = $connessione->prepare("SELECT NomeTorneo FROM brdg_cop_tornei WHERE ID_torneo = ?");
    $stmt->bind_param("i", $NomeTorneo);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $NomeTorneo = $row['NomeTorneo'];
        $Modifica = 1;
    } else {
        exit("<div class='alert alert-danger'>Il TORNEO con ID $NomeTorneo non esiste.</div>");
    }
}

if ($NomeTorneo == NULL) {
    exit("<div class='alert alert-warning text-center mt-5'>INSERIRE IL NOME DEL TORNEO</div>");
}

	//*****************************
    //  CONTROLLA SE IL TORNEO INSERITO ESISTE GIA' NELLA TABELLA brdg_ind_tornei
	//  E RICAVA IL SUO ID 

    $sql= "SELECT * FROM brdg_cop_tornei WHERE NomeTorneo=\"".$NomeTorneo."\"";
    $dati = $connessione->query($sql);
    $row = $dati->fetch_assoc(); 
    $ID_torneo= $row['ID_torneo'] ?? NULL;
	$Ora = $row['Ora'] ?? NULL;
	$Turni = $row['Turni'] ?? 0;		
	$BoardsXturno = $row['BoardsXturno'] ?? 0;
	$Punteggio = $row['Punteggio'] ?? 0;
	$Tipo = $row['Tipo'] ?? 0;
	
	
	
	

	// -------- LEGGE IL NUMERO DI ISCRITTI ----------
	//
	if (is_numeric($ID_torneo)) {
		$sql="SELECT COUNT(*) FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo;
		$dati= $connessione->query($sql); 
		$row = $dati->fetch_array();
		$Niscritti= $row[0];
		$Ntavoli= floor(($Niscritti+1)/2);
        $Modifica = 1;
	}

	//******************************
	// RICOSTRUISCE IL NOME DEL FILE
	//if($Tipo==1)$FileScelto= $Ntavoli."_".$Turni;
	//if($Tipo==2)$FileScelto= $Ntavoli."_".$Turni."_m";

	$risultati = scandir('./Template/');

	$k=0;
	$files[$k] = "00";
	foreach ($risultati as $risultato) {
		if (fnmatch($Ntavoli.'_*.txt', $risultato)) {
			$k++;
			$NomeFile = substr($risultato,0,strlen($risultato)-4);
			$files[$k]= substr($NomeFile,strpos($NomeFile,"_")+1);
		}
	}
//====================================================================		



// 3. Gestione Salvataggio (Azione SALVA)
if ($azione == "SALVA") {
    $Ora = $_POST['Ora'];
    $k_file = $_POST['Turni_idx']; // Indice dell'array files
    $BoardsXturno = $_POST['BoardsXturno'];
    $Punteggio = $_POST['Punteggio'];
    
    // Logica file turni (mantenuta dalla tua versione)
	
	
	$FileTurni= $files[$k_file];
	$Emme= strpos($FileTurni,"m");
	if(!$Emme)  {
		$Turni= $FileTurni;
		$Tipo= 1;
	}else{
		$Turni= substr($FileTurni,0,$Emme-1);;
		$Tipo= 2;
	}
	
echo"FileTurni= ".$FileTurni;	
echo"<br>";		   	
echo"Emme= ".$Emme;	
echo"<br>";		   	
echo"Turni= ".$Turni;	
echo"<br>";		   	
	
	
    // Nota: qui servirebbe ricaricare l'elenco file per mappare l'indice k_file
    // ... (omesso per brevità, assumiamo che i dati arrivino corretti)
    
    // Esempio semplificato di Update/Insert
    if ($Modifica == 0) {
        $stmt = $connessione->prepare("INSERT INTO brdg_cop_tornei (NomeTorneo, TurnoAttuale, BoardsXturno, Stato, Turni, Punteggio, Tipo, Data, Ora) VALUES (?, 0, ?, 0, ?, ?, ?, CURDATE(), ?)");
        $stmt->bind_param("siiiis", $NomeTorneo, $BoardsXturno, $Turni, $Punteggio, $Tipo, $Ora);
    } else {
        $stmt = $connessione->prepare("UPDATE brdg_cop_tornei SET BoardsXturno=?, Turni=?, Punteggio=?, Tipo=?, Ora=? WHERE NomeTorneo=?");
        $stmt->bind_param("iiiiss", $BoardsXturno, $Turni, $Punteggio, $Tipo, $Ora, $NomeTorneo);
    }
    
    if ($stmt->execute()) {
        header("Location: TorneoCopApertura.php?torneo=$NomeTorneo&modifica=1&msg=Salvato");
        exit();
    } else {
        $error_msg = "Errore durante il salvataggio.";
    }
}

// 4. Caricamento dati per il Form
$stmt = $connessione->prepare("SELECT * FROM brdg_cop_tornei WHERE NomeTorneo = ?");
$stmt->bind_param("s", $NomeTorneo);
$stmt->execute();
$torneo_data = $stmt->get_result()->fetch_assoc();

$ID_torneo = $torneo_data['ID_torneo'] ?? null;
$Ora = $torneo_data['Ora'] ?? '';
$Turni = $torneo_data['Turni'] ?? 0;
$BoardsXturno = $torneo_data['BoardsXturno'] ?? 0;
$Punteggio = $torneo_data['Punteggio'] ?? 1;
$Tipo = $torneo_data['Tipo'] ?? 0;

// Conteggio iscritti
$Niscritti = 0;
$Ntavoli = 0;
if ($ID_torneo) {
    $res_count = $connessione->query("SELECT COUNT(*) FROM brdg_cop_coppie WHERE torneoID = $ID_torneo");
    $Niscritti = $res_count->fetch_array()[0];
    $Ntavoli = floor(($Niscritti + 1) / 2);
}

// Scansione Template
$risultati = scandir('./Template/');
$files = ["00"];
foreach ($risultati as $risultato) {
    if (fnmatch($Ntavoli.'_*.txt', $risultato)) {
        $NomeFile = substr($risultato, 0, -4);
        $files[] = substr($NomeFile, strpos($NomeFile, "_") + 1);
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Torneo Bridge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a5928; color: white; }
        .card { border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
        .form-label { font-weight: bold; color: #333; }
        .bg-custom-orange { background-color: #fd7e14; color: white; }
    </style>
</head>
<body>

<form action="TorneoCopAdmin.php?torneo=<?php echo $NomeTorneo ?>&msg=<?php echo $msg ?>" method="POST">
	<input type="hidden" name="password" value="<?php echo $PSWD ?>"/>
</form>


<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="card text-dark">
                <div class="card-header bg-custom-orange text-center">
                    <h4 class="mb-0">TORNEI A COPPIE</h4>
                    <small><?php echo ($Modifica == 0) ? "APERTURA NUOVO TORNEO" : "MODIFICA TORNEO ESISTENTE"; ?></small>
                </div>
                
                <div class="card-body bg-light">
                    
                    <?php if($msg): ?>
                        <div class="alert alert-success text-center"><?php echo $msg; ?></div>
                    <?php endif; ?>

                    <form method="post" action="TorneoCopApertura.php?modifica=<?php echo $Modifica; ?>&torneo=<?php echo $NomeTorneo; ?>">
                        
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="p-2 border rounded bg-white text-center">
                                    <small class="text-muted d-block">Torneo</small>
                                    <strong><?php echo $NomeTorneo; ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded bg-white text-center">
                                    <small class="text-muted d-block">N. Coppie / Tavoli</small>
                                    <strong><?php echo "$Niscritti / $Ntavoli"; ?></strong>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ora di inizio</label>
                            <input type="time" name="Ora" class="form-control" value="<?php echo $Ora; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Turni Disponibili</label>
                            <select name="Turni_idx" class="form-select">
                                <?php 
                                foreach ($files as $index => $valore) {
                                    $selected = ($valore == $Turni || $valore == $Turni."_m") ? "selected" : "";
                                    echo "<option value='$index' $selected>$valore</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Boards x Turno</label>
                                <select name="BoardsXturno" class="form-select">
                                    <?php for($i=1; $i<=4; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php if($BoardsXturno==$i) echo "selected"; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Punteggio</label>
                                <select name="Punteggio" class="form-select">
                                    <option value="1" <?php if($Punteggio==1) echo "selected"; ?>>MP</option>
                                    <option value="3" <?php if($Punteggio==3) echo "selected"; ?>>IMP</option>
                                    <option value="2" <?php if($Punteggio==2) echo "selected"; ?>>POM</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Tipo di Torneo</label>
                            <select name="Tipo" class="form-select">
                                <option value="1" <?php if($Tipo==1) echo "selected"; ?>>Howell</option>
                                <option value="2" <?php if($Tipo==2) echo "selected"; ?>>Mitchell</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="SALVA" class="btn btn-primary btn-lg">
                                SALVA DATI
                            </button>
							<!-- "window.history.back();"  -->
                            <button type="button" onclick= <?php echo $AzioneBottone; ?>; class="btn btn-outline-secondary">
                                Torna Indietro
                            </button>
							
							
                        </div>
                    </form>
                </div>
                
                <div class="card-footer text-center">
                    <small class="text-muted">Template disponibili:</small>
                    <select class="form-select form-select-sm mt-1">
                        <?php 
                        foreach($risultati as $res) {
                            if($res != "." && $res != "..") echo "<option>$res</option>";
                        }
                        ?>
                    </select>
                </div>
            </div> </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>