<?php
include_once "dbConnessione.php";

// Recupera il parametro Torneo dalla query string
$torneo = isset($_GET['Torneo']) ? $_GET['Torneo'] : '';

// Validazione base del parametro torneo
if (empty($torneo)) {
    die('<p style="color: red;">Errore: Parametro Torneo mancante. Usa: ?Torneo=nome_torneo</p>');
}

// Sanitizza il nome del torneo per evitare path traversal
$torneo = basename($torneo);

// Definisce il percorso della directory
$dirPath = $home_archive."/upload/tornei/$torneo/";

// Verifica che la directory esista
if (!is_dir($dirPath)) {
    die("<p style='color: red;'>Errore: La directory del torneo '$dirPath' non esiste.</p>");
}

// Array per contenere i file PBN trovati
$pbnFiles = [];

// Scansiona la directory
$files = scandir($dirPath);

// Filtra i file che corrispondono al pattern Board_*.pbn
foreach ($files as $file) {
    if (preg_match('/^Board_(\d+)_?\.pbn$/i', $file, $matches)) {
        $boardNumber = $matches[1];
        $pbnFiles[$boardNumber] = $file;
		
		//echo"boardNumber =  ".$boardNumber."------>file ".$file;
    }
}

// Ordina i board per numerof0e090
ksort($pbnFiles, SORT_NUMERIC);

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selezione Board - <?php echo htmlspecialchars($torneo); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #005500;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .board-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }
        .board-button {
            padding: 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .board-button:hover {
            background-color: #45a049;
        }
        .info {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .no-files {
            color: #d32f2f;
            font-weight: bold;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Torneo: <?php echo htmlspecialchars($torneo); ?></h1>
         
        <div class="info">
            <strong>Boards trovate:</strong> <?php echo count($pbnFiles); ?>
        </div>

        <?php if (empty($pbnFiles)): ?>
            <div class="no-files">
                Nessun file Board_*.pbn trovato nella directory del torneo.
            </div>
        <?php else: ?>
            <p>Seleziona un board da analizzare:</p>
            <div class="board-list">
                <?php foreach ($pbnFiles as $boardNumber => $fileName): ?>
                    <form method="GET" action="<?php echo $home_archive; ?>/leggi_pbn.php" style="display: inline;">
                        <input type="hidden" name="Torneo" value="<?php echo htmlspecialchars($torneo); ?>">
                        <input type="hidden" name="Board" value="<?php echo ltrim($boardNumber,"0"); ?>">
                        <button type="submit" class="board-button">
                            Board <?php echo  htmlspecialchars($boardNumber); ?>
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>