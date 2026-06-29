<?php
/**
 * Bridge PBN Analyzer
 * Legge file PBN e lancia l'applicazione di analisi bridge online
 * 
 * Utilizzo: bridge_analyzer.php?Torneo=nome_torneo&Board=numero_board
 */

// Verifica che i parametri richiesti siano presenti
if (!isset($_GET['Torneo']) || !isset($_GET['Board'])) {
    die("Errore: Parametri Torneo e Board sono obbligatori.\nUtilizzo: bridge_analyzer.php?Torneo=nome_torneo&Board=numero_board");
}

$torneo = $_GET['Torneo'];
$board = $_GET['Board'];

if($board<10) {
	$board_ = "0".$board;
}else{
	$board_ = $board;			
}

// Costruisce il percorso del file PBN
$filePath = "./upload/tornei/$torneo/Board_$board_.pbn";

// Verifica che il file esista
if (!file_exists($filePath)) {
    die("Errore: File {$filePath} non trovato.");
}

/**
 * Funzione per parsare il file PBN
 */
function parsePBNFile($filePath) {
    $content = file_get_contents($filePath);
    if ($content === false) {
        die("Errore: Impossibile leggere il file {$filePath}");
    }
    
    $lines = explode("\n", $content);
    $boardData = array();
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || $line[0] === '%') {
            continue; // Salta righe vuote e commenti
        }
        
        // Parsing dei tag PBN
        if (preg_match('/^\[(\w+)\s+"(.*)"\]$/', $line, $matches)) {
            $tag = $matches[1];
            $value = $matches[2];
            
            switch ($tag) {
                case 'Board':
                    $boardData['board'] = $value;
                    break;
                case 'Dealer':
                    $boardData['dealer'] = strtolower($value);
                    break;
                case 'Vulnerable':
                    //$boardData['vul'] = parseVulnerability($value);
                    $boardData['vul'] = $value;
                    break;
                case 'Deal':
                    $hands = parseDeal($value);
                    $boardData = array_merge($boardData, $hands);
                    break;
            }
        }
    }
    
    return $boardData;
}

/**
 * Converte la vulnerabilità dal formato PBN al formato richiesto
 */
function parseVulnerability($vul) {
    switch ($vul) {
        case 'None':
            return 'none';
        case 'NS':
            return 'ns';
        case 'EW':
            return 'ew';
        case 'All':
            return 'both';
        default:
            return 'none';
    }
}

/**
 * Parsing delle carte dal formato PBN
 */
function parseDeal($deal) {
    $hands = array();
    
    // Il formato Deal può essere "N:KQJ4.A32.987.T65 A987.KQ4.QJT.432 T632.JT98.A32.Q9 5.765.K654.AKJ87"
    // oppure "W:..." dove la prima lettera indica il dealer
    
    if (preg_match('/^[NESW]:(.+)$/', $deal, $matches)) {
        $cardsString = $matches[1];
    } else {
        $cardsString = $deal;
    }
    
    $handsArray = explode(' ', trim($cardsString));
    
    // L'ordine standard è N E S W
    $positions = array('north', 'east', 'south', 'west');
    
    for ($i = 0; $i < min(4, count($handsArray)); $i++) {
        //$hands[$positions[$i]] = formatHand($handsArray[$i]);
        $hands[$positions[$i]] = $handsArray[$i];
    }
    
    return $hands;
}

/**
 * Formatta una singola mano per l'URL
 
function formatHand($hand) {
    // Sostituisce i simboli delle carte per l'URL
    $hand = str_replace('T', '10', $hand);
    return urlencode($hand);
}
*/
// Parsing del file PBN
try {
    $boardData = parsePBNFile($filePath);
    
    // Verifica che tutti i dati necessari siano presenti
    $requiredFields = array('board', 'dealer', 'vul', 'north', 'east', 'south', 'west');
    foreach ($requiredFields as $field) {
        if (!isset($boardData[$field])) {
            die("Errore: Campo {$field} non trovato nel file PBN.");
        }
    }
    
    // Costruisce l'URL per l'applicazione online
    $baseUrl = "https://dds.bridgewebs.com/bsol1/ddummy.htm";
    $params = array(
        'club' => 'hr_bkpula',
        'analyse' => 'true',
        'board' => $boardData['board'],
        'dealer' => $boardData['dealer'],
        'vul' => $boardData['vul'],
        'north' => $boardData['north'],
        'east' => $boardData['east'],
        'south' => $boardData['south'],
        'west' => $boardData['west']
    );
    
    $queryString = http_build_query($params);
    $finalUrl = $baseUrl . '?' . $queryString;
    
   
 ?>
    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bridge Analyzer - Torneo <?php echo htmlspecialchars($torneo); ?> Board <?php echo htmlspecialchars($board); ?></title>
    </head>
    <body>
        <script type="text/javascript">
            window.location.href = "<?php echo $finalUrl; ?>"
        </script>
               
           
    </body>
    </html>
<?php
  /*
  
            <a href="<?php echo $finalUrl; ?>" class="button">
                🚀 Vai all'Analisi
            </a>
 		header("Location: TorneoCopDettagli_1.php?torneo=".$torneo."&turno=".$turno);
		exit();
  
  
  */  
} catch (Exception $e) {
    die("Errore nell'elaborazione del file: " . $e->getMessage());
}
?>