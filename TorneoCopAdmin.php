
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Selezioniamo il tag select usando il suo ID
    const selectTorneo = document.getElementById('select-torneo');
    const inputDestinazione = document.getElementById('input-torneo');

    // Aggiungiamo l'evento direttamente al select
    selectTorneo.addEventListener('change', function() {
        // Recupera l'opzione attualmente selezionata
        const selectedOption = this.options[this.selectedIndex]; 
        
        // Recupera il valore dal data-nome
        const nomeTorneo = selectedOption.getAttribute('data-nome');
        
        if (nomeTorneo) {
            inputDestinazione.value = nomeTorneo;
            console.log("Torneo selezionato:", nomeTorneo);
        }
    });
});

/*

document.addEventListener('DOMContentLoaded', () => {

    const options = document.querySelectorAll('.select-torneo');
 	//const torneoSelezionato = document.querySelector('.select-torneo:selected');
    const inputDestinazione = document.getElementById('input-torneo');

	options.forEach(option => {
        option.addEventListener('change', function() {

        const selectedOption = this.options[this.selectedIndex];       
        // Recupera il valore dal data-nome
        const nomeTorneo = selectedOption.getAttribute('data-nome');
        
			if (nomeTorneo) {
				inputDestinazione.value = nomeTorneo;
				console.log("Torneo selezionato:", nomeTorneo);
			}
		});
    });
});
*/
</script>


<?php 
// INTERFACCIA DI AMMINISTRAZIONE DEI TORNEI DI BRIDGE A COPPIE
// ACCESSO AL DATABASE
include "dbConnessione.php";

//=================================================
// INIZIALIZZAZIONE
//=================================================
$NomeTorneo = $_GET['torneo'];
if(!$NomeTorneo)  $NomeTorneo=  $_POST['torneo'];
$NomeTorneoInput= $NomeTorneo;

$TipoTorneo= ["-","Howell","Mitchell"];

$azione = $_POST['action'] ?? null;
$pswd = $_POST['Login']  ?? null;

//if($NomeTorneo && $NomeTorneo != "*")  {
if($NomeTorneo)  {
	// RICAVA I DATI DEL TORNEO DAL DATABASE
	$sql = "SELECT * FROM `brdg_cop_tornei` WHERE `NomeTorneo`= '".$NomeTorneo."'";
	$dati = $connessione->query($sql);
	if($dati)  {
		$row = $dati->fetch_assoc(); 
		$ID_torneo = $row['ID_torneo'];
		$turno = $row['TurnoAttuale'];
		$NumTurni = $row['Turni'];
		$Stato = $row['Stato'];
		$Tipo = $row['Tipo'];

		// LEGGE IL NUMERO DI ISCRITTI
		if (is_numeric($ID_torneo)) {
			$sql = "SELECT COUNT(*) FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo;
			$dati = $connessione->query($sql); 
			$row = $dati->fetch_array();
			$Niscritti = $row[0];
			$NumTavoli = floor(($Niscritti+1)/2);
		}
	}	
}else{
	
	$NomeTorneo ="Non selezionato";
	$turno = 0;
	$NumTurni = 0;
	$Stato = 99;
	$Tipo = 0;
	$Niscritti = 0;
	
}
//=================================================
// Recupero Password dal DB
//=================================================
$dati = $connessione->query("SELECT * FROM brdg_cop_pswd WHERE 1");
$row = $dati->fetch_assoc(); 
$PSWD = $row['password'];

//=================================================
/*
echo"PSWD -----> ".$PSWD;
echo"<br>";
echo"pswd 1-----> ".$pswd;
echo"<br>";

echo"NomeTorneo -----> ".$NomeTorneo;
echo"<br>";
echo"NomeTorneoInput -----> ".$NomeTorneoInput;
echo"<br>";
echo"ID_torneo-----> ".$ID_torneo;
echo"<br>";
*/

//=================================================
// IL CONTROLLO VIENE DA LOGIN
//=================================================
if ($azione == NULL && $pswd != NULL) {

	//  CONTROLLA LA PSWD
	if ($pswd != $PSWD) {
		exit("
		<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
		<div class='container mt-5'><div class='alert alert-danger text-center'><h4>PASSWORD ERRATA</h4><a href='TorneoCopLogin.html' class='btn btn-outline-danger mt-3'>Torna al Login</a></div></div>");
	}

	$msg = "Selezionare il torneo o l' Archivio e APRIRE";
	
}
//=================================================

//=================================================
// IL  CONTROLLO VIENE DALLE PROCEDURE ESTERNE RICHIAMATE 
// (Apertura, Inserimento, NuovoTurno, Lista....)
//=================================================
if ($azione == NULL && $pswd == NULL) {

	// RECUPERA LA PSWD TRASMESSA E LA CONTROLLA
	$NuovaPassword = $_POST['nuovapassword'] ?? null;	// DA MODIFICA PSWD
    $pswd = $_POST['password'] ?? null;					// DALLE ALTRE PROCEDURE
	if(!$pswd)  $pswd = $NuovaPassword;
	if ($pswd != $PSWD) {
		exit("
		<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
		<div class='container mt-5'><div class='alert alert-danger text-center'><h4>ERRORE INTERNO</h4></div></div>");
	}

}
//=================================================

//=================================================
// IL CONTROLLO VIENE DAL FORM INTERNO -- GESTIONE DELLE AZIONI
//=================================================
if ($azione != NULL) {
    if ($azione == "Cancellazione Turno") {
        //$NomeTorneo = $_GET['torneo'];
        $sql = "SELECT * FROM `brdg_cop_tornei` WHERE `NomeTorneo`= '".$NomeTorneo."'";
        $dati = $connessione->query($sql);
        if (!$dati) {
            echo "<script>alert('ATTENZIONE: TORNEO NON ESISTENTE');</script>";
            goto modulo;
        }
        $row = $dati->fetch_assoc(); 
        $ID_torneo = $row['ID_torneo'];
        $turnoAtt = $row['TurnoAttuale'];
        $Stato = $row['Stato'];
        $Tipo = $row['Tipo'];
/*
echo "turnoAtt: ".$turnoAtt ;
echo  "<br>";
echo "Stato: ".$Stato ;     
echo  "<br>";
*/        
        if (!is_numeric($ID_torneo)) {
			//echo "<script>alert('ATTENZIONE: INSERIRE IL NOME DEL TORNEO');</script>";
			//goto modulo;
            $connessione->close();
            exit('<div class="container"><div class="content"><div class="alert alert-warning">Il torneo "'. $NomeTorneo.'" non esiste</div></div></div>');
        }

        // Cancella il Turno Attuale se non chiuso
        if ($Stato == 0) {
            if ($turnoAtt > 0) {
                $sql = "UPDATE brdg_cop_scores SET `score`=NULL,`licitaID`=NULL WHERE torneoID=".$ID_torneo." AND turno=".$turnoAtt;
                $dati = $connessione->query($sql);
                $turnoCanc = $turnoAtt;
                if ($turnoAtt > 1)  {
					$turnoAtt--;
					$turno = $turnoAtt;
					$dati = $connessione->query("UPDATE `brdg_cop_tornei` SET `Stato`=0,`TurnoAttuale`=".$turnoAtt." WHERE `ID_torneo`=".$ID_torneo);
					$msg = "Cancellato il Turno: ".$turnoCanc." di: ".$NomeTorneo;
				} else  {   // $turnoAtt == 1
					//$dati = $connessione->query("DELETE FROM `brdg_cop_scores` WHERE torneoID=".$ID_torneo);
					//$dati = $connessione->query("UPDATE `brdg_cop_tornei` SET `Stato`=0,`TurnoAttuale`=0 WHERE `ID_torneo`=".$ID_torneo);
					$msg = "Cancellati i risultati del turno 1";
				}
            }
        } else {
			echo "<script>alert('ATTENZIONE: IL TORNEO RISULTA CHIUSO');</script>";
			/*
            $dati = $connessione->query("UPDATE `brdg_cop_tornei` SET `Stato`=0 WHERE `ID_torneo`=".$ID_torneo);
            $msg = "Torneo riaperto: ";
			$Stato= 0;
			*/
        }				
    }  
//**********************************************************************************

    if ($azione == "Cancellazione TORNEO") {
//echo"ID_torneo-----> ".$ID_torneo;
//echo"<br>";
		//$sql = "DELETE FROM `brdg_cop_tornei` WHERE `NomeTorneo`= '".$NomeTorneo."'";
        $sql = "DELETE FROM `brdg_cop_tornei` WHERE `ID_torneo`= $ID_torneo";
        $msg = "Torneo cancellato: $NomeTorneo ";
        $dati = $connessione->query($sql);
        if (!$dati) {
            echo "<script>alert('ATTENZIONE: TORNEO NON APERTO');</script>";
        }
		//  !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		// deve cancellare anche tutti i dati relativi: coppie, scores...
        $dati = $connessione->query("DELETE FROM `brdg_cop_scores` WHERE torneoID=".$ID_torneo);
        $dati = $connessione->query("DELETE FROM `brdg_cop_coppie` WHERE torneoID=".$ID_torneo);
		
     }
//**********************************************************************************

    if ($azione == "Nuovo Turno") {
        //FORZATURA NUOVO TURNO
        header("Location: TorneoCopNuovoTurno.php?torneo=".$NomeTorneo."&turno=".$turno);
        exit();
    }
//**********************************************************************************


	if ($azione == "Apertura") {
		if (str_starts_with($NomeTorneo, "Archivio")) {
			header("Location: TorneoCopListaTornei.php?orig=admin&torneo=".$NomeTorneo);
			exit();
		}

			//******************************
			// CONTROLLA L' ESISTENZA DI "NOME TORNEO"
		if ($NomeTorneo == NULL) {
			echo "<script>alert('ATTENZIONE: INSERIRE IL NOME DEL TORNEO');</script>";
			goto modulo;
		}

			//*****************************
			//  CONTROLLA SE IL TORNEO INSERITO ESISTE GIA' NELLA TABELLA brdg_ind_tornei
	   if (is_numeric($ID_torneo)) {
			header("Location: TorneoCopApertura.php?torneo=".$NomeTorneo."&modifica=1");
			exit();
		} else {
			//*****************************
			//  APERTURA NUOVO TORNEO
			header("Location: TorneoCopApertura.php?torneo=".$NomeTorneo."&modifica=0");
			exit();
		}
	}

	if ($azione == "Controllo iscritti") {
		header("Location: TorneoCopListaCompletaIscrizione.php?torneo=".$NomeTorneo."&orig=admin");
		exit();
	}


// ===================   RIORDINO   ==============================
// 

	if ($azione == "Riordino iscritti") {
	
		if (isset($ID_torneo) && !empty($ID_torneo)) {

			// 1. SELEZIONA LE COPPIE ORDINATE PER LA SOMMA DEI PP DEI GIOCATORI
			// Uniamo la tabella delle coppie con la rubrica (due volte, una per giocatore)
			$sql_seleziona = "SELECT c.coppiaID, c.nome1ID, c.nome2ID, 
									(COALESCE(r1.PP, 0) + COALESCE(r2.PP, 0)) AS somma_pp
							FROM `brdg_cop_coppie` c
							LEFT JOIN `brdg_ind_rubrica` r1 ON c.nome1ID = r1.ID
							LEFT JOIN `brdg_ind_rubrica` r2 ON c.nome2ID = r2.ID
							WHERE c.torneoID = $ID_torneo
							ORDER BY somma_pp DESC, c.coppiaID ASC";

			$risultato = $connessione->query($sql_seleziona);

			if ($risultato && $risultato->num_rows > 0) {
				
				// Disabilitiamo temporaneamente i controlli sulle chiavi se necessario, 
				// o procediamo direttamente se coppiaID non è una chiave primaria rigida non modificabile.
				// Iniziamo un contatore per assegnare i nuovi ID sequenziali (1, 2, 3...)
				$nuovo_id = 1;

				echo "<p style='color:blue; text-align:center;'><strong>Riordinamento coppie in corso...</strong></p>";

				while ($coppia = $risultato->fetch_assoc()) {
					$vecchio_id = $coppia['coppiaID'];
					$n1 = $coppia['nome1ID'];
					$n2 = $coppia['nome2ID'];

					// 2. AGGIORNA LA COPPIA CON IL NUOVO ID SEQUENZIALE
					// Usiamo nome1ID e nome2ID nella WHERE per essere sicuri di identificare la riga corretta 
					// anche mentre cambiamo il coppiaID.
					$sql_update = "UPDATE `brdg_cop_coppie` 
								SET `coppiaID` = $nuovo_id 
								WHERE `torneoID` = $ID_torneo 
									AND `nome1ID` = $n1 
									AND `nome2ID` = $n2";

					if (!$connessione->query($sql_update)) {
						echo "Errore durante il riordino della coppia (ID Vecchio: $vecchio_id): " . $connessione->error . "<br>";
					}

					$nuovo_id++;
				}

				echo "<p style='color:green; text-align:center;'><strong>Riordinamento completato! " . ($nuovo_id - 1) . " coppie ordinate per PP decrescenti.</strong></p>";
			} else {
				echo "<p style='color:orange; text-align:center;'>Nessuna coppia trovata per questo torneo o errore di lettura.</p>";
			}
		} else {
			echo "<p style='color:red; text-align:center;'>ID Torneo non valido.</p>";
		}
	}
//======================================  FINE  RIORDINO ===================================================

	if ($azione == "Inizio Turni") {
		if ($turno == $NumTurni) {
			echo "<script>alert('ATTENZIONE: I TURNI SONO TERMINATI');</script>";
			goto modulo;
		}
		
		$sql = "SELECT COUNT(*) FROM `brdg_cop_scores` WHERE `torneoID`=$ID_torneo and `turno`=$turno and `score` IS NULL";
		$dati = $connessione->query($sql);
		if (!$dati) {
			echo "<script>alert('ATTENZIONE: TORNEO NON APERTO');</script>";
			goto modulo;
		}
		
		$row = $dati->fetch_array();
		$Nnulli = $row[0];
		
		if ($Nnulli != 0) {
			echo "<script>alert('ATTENZIONE: TURNO NON COMPLETATO');</script>";
			goto modulo;
		}
		
		if ($Stato == 1) {
			echo "<script>alert('ATTENZIONE: IL TORNEO È CHIUSO');</script>";
			goto modulo;
		}

		header("Location: TorneoCopNuovoTurno.php?torneo=".$NomeTorneo."&turno=".$turno);
		exit();
	}


	if ($azione == "Chiusura") {
		$sql = "UPDATE `brdg_cop_tornei` SET `Stato`=1 WHERE `NomeTorneo`= '".$NomeTorneo."'";
		$dati = $connessione->query($sql);
		$msg = "Il torneo è stato chiuso: ";
		$Stato= 1;
		goto modulo;
	}
	
	if ($azione == "Riapertura") {
		if($Stato == 0  && $turno==0)  {
			$msg = "Il torneo  '$NomeTorneo' è già aperto";
			goto modulo;
			
		}
		if($Stato == 0  && $turno > 1)  {
			$msg = "Il torneo  '$NomeTorneo' è in corso al turno $turno";
			goto modulo;
			
		}
		if($turno==1)  {
			$turno= 0;					
			// deve cancellare anche tutti i dati inseriti in scores
			$dati = $connessione->query("DELETE FROM `brdg_cop_scores` WHERE torneoID=".$ID_torneo);

		}

		$sql = "UPDATE `brdg_cop_tornei` SET `TurnoAttuale`=$turno , `Stato`=0 WHERE `ID_torneo`= ".$ID_torneo;
		$dati = $connessione->query($sql);
		$msg = "Il torneo  '$NomeTorneo' è stato riaperto alle iscrizioni";
		$Stato= 0;
		goto modulo;
	}

	if ($azione == "Classifica") {
		header("Location: TorneoCopClassifica.php?torneo=".$NomeTorneo."&turno=".$turno."&orig=admin");
		exit();
	}

	if ($azione == "Risultati") {
		header("Location: TorneoCopDettagli_1.php?torneo=".$NomeTorneo."&turno=".$turno."&orig=admin");
		exit();
	}

	if ($azione == "Scores") {
		header("Location: TorneoCopDettagli_2.php?torneo=".$NomeTorneo."&turno=".$turno."&orig=admin");
		exit();
	}

	if ($azione == "HOME") {
		header("Location: /..");
		exit();
	}

	if ($azione == "Cambia PSWD") {
		
		header("Location: TorneoCopCambiaPSWD.php");
		exit();
	}

	if ($azione == "Controllo Turno") {
		header("Location: TorneoCopControllo.php?torneo=".$NomeTorneo."&turno=".$turno);
		exit();
	}

	if ($azione == "Controllo Boards") {
		header("Location: TorneoCopVediBoards.php?NomeTorneo=".$NomeTorneo);
		exit();
	}

	if ($azione == "Analisi Boards") {
		header("Location: TorneoCopListaFilesPBN.php?Torneo=".$NomeTorneo);
		exit();
	}

	if ($azione == "Controllo Tempi") {
		header("Location: TorneoCopControlloTempi.php?torneo=".$NomeTorneo."&orig=admin");
		exit();
	}

	if ($azione == "Turno Manuale") {
		if ($Stato == 1) {
			echo "<script>alert('ATTENZIONE: IL TORNEO È CHIUSO');</script>";
			goto modulo;
		}
		$msg= "Nuovo turno ".$turno." forzato per : ".$NomeTorneo ;
		header("Location: TorneoCopNuovoTurno.php?torneo=".$NomeTorneo."&turno=".$turno);
		exit();	
	}
		
	if ($azione == "Inserimento Manuale") {
		if ($Stato == 1) {
			echo "<script>alert('ATTENZIONE: IL TORNEO È CHIUSO');</script>";
		}
		header("Location: TorneoCopControlloTotale.php?torneo=".$NomeTorneo);
		exit();
	}
		



}
modulo:
//echo"stato=".$Stato;
//=================================================
// GRAFICA E FORM DI INGRESSO DATI
//=================================================
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestione Torneo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-header { background: #2c3e50; color: white; padding: 20px 0; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .btn-custom { border-radius: 8px; padding: 10px 15px; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s; }
        .btn-custom:hover { transform: translateY(-2px); }
        .status-badge { font-size: 0.9rem; padding: 8px 15px; border-radius: 50px; }
        .section-title { font-size: 1.1rem; font-weight: 600; color: #555; margin-bottom: 15px; border-left: 4px solid #3498db; padding-left: 10px; }
    </style>
</head>
<body>

<header class="admin-header text-center">
    <div class="container">
        <h1 class="h3 mb-0 text-uppercase">Gestione Tornei a Coppie</h1>
    </div>
</header>

<div class="container pb-5">
    
    <?php if($msg): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Messaggio:</strong> <?php echo $msg ; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4">
            <div class="card p-4">
                <!--<div class="section-title">Torneo Attuale</div>
                <div class="mb-3">-->
									<!--<label class="form-label text-muted small">Nome Torneo</label>-->
                    <!--<div class="h5"><?php echo $NomeTorneo ?: '---'; ?></div>
                </div>-->
                
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <!--<small class="text-muted d-block">Stato del Torneo Attuale</small>-->
                        <label class="form-label font-weight-bold">Stato del Torneo Attuale:</label>
                        <?php if($Stato == 99): ?>
                            <span class="badge bg-danger status-badge">NON APERTO</span>
                        <?php endif; ?>
                        <?php if($Stato == 1): ?>
                            <span class="badge bg-danger status-badge">CHIUSO</span>
                        <?php elseif($Stato == 0 && $turno == 0): ?>
                            <span class="badge bg-success status-badge">APERTO</span>
                        <?php elseif($Stato == 0 && $turno > 0): ?>
                            <span class="badge bg-warning status-badge">IN CORSO</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Turno</small>
                        <span class="h5 text-primary"><?php echo $turno; ?> / <?php echo $NumTurni; ?></span>
                    </div>
                </div>



                <form method="post" action="TorneoCopAdmin.php">
                    <input type="hidden" name="password" value="<?php echo $pswd; ?>">
                    <div class="mb-3">
<!--                        <label class="form-label font-weight-bold">Seleziona Torneo (ID o Nome)</label>
<input type="text" id="input-torneo" name="nome_torneo_input" readonly>
-->			
					<input id="input-torneo" type="text" style="font-size:24px;"  name="torneo" class="form-control form-control-lg fw-bold" value="<?php echo $NomeTorneoInput; ?>" >
<!-- 						
					<small class="text-muted italic">Usa '**' per la lista completa</small>
-->	                </div>


              <hr>
 
<?php



	$sql="SELECT * FROM `brdg_cop_tornei` WHERE `Stato` != 1 ORDER BY ID_torneo DESC LIMIT 10" ;				
	$dati= $connessione->query($sql); 
	$row = $dati->fetch_assoc();		
	echo"<select id=\"select-torneo\" 
		name=\"tavolo\"  
		class=\"form-select form-select-sm\" 
		style=\"font-size: 14px;\"
		>
	";
//style=\"font-size:24px; \"
	echo  "<option  >Seleziona Torneo o Archivio</option>";
	echo  "<option  data-nome='Archivio'>Archivio</option>";
	echo  "<option  data-nome='Archivio Completo'>Archivio Completo</option>";
	if($row) {
		$NomeTorneo= $row['NomeTorneo'];
		if(!$NomeTorneoInput) $NomeTorneoInput= $NomeTorneo ;
		echo  "<option  value='".$NomeTorneo."' data-nome='".$NomeTorneo."'>".$NomeTorneo."</option>";
		$row = $dati->fetch_assoc();
	}
	while($row) {
		$NomeTorneo= $row['NomeTorneo'];
		echo  "<option  value='".$NomeTorneo."' data-nome='".$NomeTorneo."'>".$NomeTorneo."</option>";
		$row = $dati->fetch_assoc();
	}

	echo "</select>	";
	
echo"<br>";

?>	


                    <div class="d-grid gap-2">
                        <button type="submit" name="action" value="Apertura" class="btn btn-primary btn-custom">
                            <span class="material-icons">play_circle</span> Apri / Crea Torneo
                        </button>
                        <button type="submit" name="action" value="Chiusura" class="btn btn-outline-secondary btn-custom" onclick="return confirm('Chiudere il torneo?')">
                            <span class="material-icons">lock</span> Chiudi Torneo
                        </button>
                        <button type="submit" name="action" value="Riapertura" class="btn btn-outline-secondary btn-custom" onclick="return confirm('Riaprire il torneo?')">
                            <span class="material-icons">lock_open</span> Riapri Torneo
                        </button>
                    </div>
                </form>
            </div>

            <div class="card p-4 border-start border-danger border-4">
                <div class="section-title text-danger">Zona Pericolo</div>
                <form method="post">
                    <input type="hidden" name="password" value="<?php echo $pswd; ?>">
                    <input type="hidden" name="torneo" value="<?php echo $NomeTorneo; ?>">
                    <button type="submit" name="action" value="Cancellazione TORNEO" class="btn btn-danger w-100 btn-custom" onclick="return confirm('ATTENZIONE: Azione irreversibile. Cancellare il torneo ?')">
                        <span class="material-icons">delete_forever</span> ELIMINA TORNEO
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center p-3 bg-white">
                        <small class="text-muted">Tipo</small>
                        <div class="fw-bold"><?php echo $TipoTorneo[$Tipo] ?? 'N/D'; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center p-3 bg-white">
                        <small class="text-muted">Iscritti</small>
                        <div class="fw-bold"><?php echo $Niscritti; ?> Coppie</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center p-3 bg-white">
                        <small class="text-muted">Tavoli</small>
                        <div class="fw-bold"><?php echo $NumTavoli; ?></div>
                    </div>
                </div>
            </div>

            <!--<form method="post"> e9ecef d1d4d7-->
			<style>
				.btn-grigio-chiaro {
					background-color: #b0c4de !important;
					border: 1px solid #dee2e6 !important;
					color: #495057 !important;
				}
				.btn-grigio-chiaro:hover {
					background-color: #778899 !important;
				}
			</style>

			<form action="TorneoCopAdmin.php?torneo=<?php echo $NomeTorneoInput; ?>" method="POST">
				<input type="hidden" name="password" value="<?php echo $pswd; ?>">
				<input type="hidden" name="torneo" value="<?php echo $NomeTorneo; ?>">

				<div class="section-title">Operazioni Turni</div>
				<div class="row g-3 mb-4">
					<div class="col-md-6">
						<button type="submit" name="action" value="Inizio Turni" class="btn btn-success w-100 btn-custom shadow-sm py-3">
							<span class="material-icons">skip_next</span> AVVIA NUOVO TURNO
						</button>
					</div>
					<div class="col-md-6">
						<button type="submit" name="action" value="Cancellazione Turno" class="btn btn-danger w-100 btn-custom py-3" onclick="return confirm('Cancellare l\'ultimo turno?')">
							<span class="material-icons">undo</span> CANCELLA ULTIMO TURNO
						</button>
					</div>
				</div>

				<div class="section-title">Controlli e Analisi</div>
				<div class="row g-2 mb-4 flex-nowrap">
					<div class="col">
						<button type="submit" name="action" value="Controllo iscritti" class="btn btn-grigio-chiaro w-100 btn-custom h-100">Iscritti</button>
					</div>
					<div class="col">
						<button type="submit" name="action" value="Controllo Turno" class="btn btn-grigio-chiaro w-100 btn-custom h-100">Controllo Turno</button>
					</div>
					<div class="col">
						<button type="submit" name="action" value="Controllo Tempi" class="btn btn-grigio-chiaro w-100 btn-custom h-100">Tempi</button>
					</div>
					<div class="col">
						<button type="submit" name="action" value="Controllo Boards" class="btn btn-grigio-chiaro w-100 btn-custom h-100">Foto Boards</button>
					</div>
					<div class="col">
						<button type="submit" name="action" value="Analisi Boards" class="btn btn-grigio-chiaro w-100 btn-custom h-100">Analisi Boards</button>
					</div>
				</div>

				<div class="section-title">Gestione Manuale</div>
				<div class="row g-2 mb-4">
					<div class="col-md-4">
						<button type="submit" name="action" value="Turno Manuale" class="btn btn-warning w-100 border btn-custom" onclick="return confirm('Sicuro di Forzare?')">
							<span class="material-icons">fast_forward</span>Forza Nuovo Turno
						</button></div>
					<div class="col-md-4">
						<button type="submit" name="action" value="Inserimento Manuale" class="btn btn-warning w-100 btn-custom">
							<span class="material-icons">edit_note</span>Inserimento Manuale
						</button></div>
					<div class="col-md-4">
						<!--<button type="submit" name="action" value="Riordino iscritti" class="btn btn-warning w-100 btn-custom">Riordino -->
						<button type="submit" name="action" value="Riordino iscritti" class="btn btn-warning w-100 btn-custom " onclick="return confirm('Azione irreversibile')">
							<span class="material-icons">sort</span>Riordino iscritti
						</button>
					</div>
					
				</div>

				<div class="section-title">Classifiche e Report</div>
				<div class="row g-2">
					<div class="col-md-4"><button type="submit" name="action" value="Classifica" class="btn btn-info text-white w-100 btn-custom">Classifica</button></div>
					<div class="col-md-4"><button type="submit" name="action" value="Risultati" class="btn btn-info text-white w-100 btn-custom">Risultati</button></div>
					<div class="col-md-4"><button type="submit" name="action" value="Scores" class="btn btn-info text-white w-100 btn-custom">Scores</button></div>
				</div>
			</form>        
		</div>
    </div>
</div>

<footer class="mt-5 py-4 bg-white border-top">
    <div class="container d-flex justify-content-between">
        <form method="post"><input type="hidden" name="password" value="<?php echo $pswd; ?>"><button type="submit" name="action" value="HOME" class="btn btn-link text-decoration-none text-muted">Torna alla Home</button></form>
        <form method="post"><input type="hidden" name="password" value="<?php echo $pswd; ?>"><button type="submit" name="action" value="Cambia PSWD" class="btn btn-outline-secondary btn-sm">Sicurezza Account</button></form>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>