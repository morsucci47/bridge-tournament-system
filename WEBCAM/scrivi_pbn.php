<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bridge Hand Editor - PBN Format</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 5px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 5px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .progress-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 0 20px;
        }
        .progress-step {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 20px;
            margin: 0 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .progress-step.completed {
            background-color: #27ae60;
            color: white;
        }
        .progress-step.no-completed {
            background-color: #e74c3c;
            color: white;
        }
        .progress-step.active {
            background-color: #3498db;
            color: white;
        }
        .progress-step.pending {
            background-color: #ecf0f1;
            color: #7f8c8d;
        }
        .hand {
            border: 2px solid #34495e;
            border-radius: 10px;
            padding: 15px;
            background-color: #ecf0f1;
            margin-bottom: 20px;
        }
        .hand.hidden {
            display: none;
        }
        .hand h3 {
            text-align: center;
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-size: 18px;
        }
        .suit {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        .suit-symbol {
            font-size: 25px;
            font-weight: bold;
            margin-right: 10px;
        }
        .spades { color: #2c3e50; }
        .hearts { color: #e74c3c; }
        .diamonds { color: #e74c3c; }
        .clubs { color: #2c3e50; }
        .cards {
            display: flex;
            flex-wrap: wrap;
            gap: 5px 5px;
        }
        .card {
            margin: 10px 2px;
        }
 
		.card input[type="checkbox"] {
            margin-right: 3px;
			width: 28px;
			height: 28px;
			vertical-align: middle;
		}
		.card label {
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 3px;
            background: white;
            border: 1px solid #bdc3c7;
            display: inline-block;
            text-align: center;
            transition: all 0.2s ease;
			margin-left: 5px;
			padding: 2px 6px;
			min-width: 25px;
			vertical-align: middle;
        }
        .card input[type="checkbox"]:checked + label {
            background-color: #3498db;
            color: white;
        }
        .card input[type="checkbox"]:disabled + label {
            background-color: #95a5a6;
            color: #7f8c8d;
            cursor: not-allowed;
            opacity: 0.6;
        }
        .controls {
            text-align: center;
            margin: 20px 0;
        }
        .btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 10px;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .btn-clear {
            background-color: #e74c3c;
        }
        .btn-clear:hover {
            background-color: #c0392b;
        }
        .btn:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
        }
        .btn-save {
            background-color: #27ae60 !important;
            font-size: 18px !important;
            font-weight: bold !important;
            padding: 12px 24px !important;
        }
        .btn-save:hover {
            background-color: #229954 !important;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .btn-save-pulse {
            animation: pulse 1.5s infinite;
        }
        .form-group {
            margin: 10px 0;
            text-align: center;
        }
        .form-group label {
            display: inline-block;
            width: 100px;
            text-align: right;
            margin-right: 10px;
        }
        .form-group input, .form-group select {
            padding: 5px;
            border: 1px solid #bdc3c7;
            border-radius: 3px;
            width: 200px;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            background-color: #d5f4e6;
            border: 1px solid #27ae60;
            border-radius: 5px;
        }
        .error {
            background-color: #fadbd8;
            border-color: #e74c3c;
        }
        .card-count {
            font-size: 20px;
            color: #7f8c8d;
            margin-left: 15px;
            margin-top: 5px;
            align-self: flex-start;
        }
        .hand-total {
            text-align: center;
            font-size: 20px;
 			font-weight: bold;
            color: #7f8c8d;
            margin-top: 10px;
        }
        .west-auto {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }
        .west-auto h3 {
            color: white;
        }
        .west-cards {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            align-items: center;
        }
    </style>
</head>

<?php
    $torneo = $_GET['torneo'] ?? '';
    $board  = $_GET['board'] ?? '';
    $turno = $_GET['turno'] ?? '';
    $dealer  = $_GET['dealer'] ?? '';
    $NumTavolo  = $_GET['tavolo'] ?? '';
    $vulnerability  = $_GET['vulner'] ?? '';
    
    $board_number = $board;
    $save_success = false; // Flag per verificare il successo dell'operazione
?>			

<body>
    <div class="container">
        
        <?php

        function saveToPBN($data, $torneo, $board_number, $dealer, $vulnerability) {
            $suits = ['spades', 'hearts', 'diamonds', 'clubs'];
            $cards = ['A', 'K', 'Q', 'J', '10', '9', '8', '7', '6', '5', '4', '3', '2'];
            $positions = ['north', 'east', 'south', 'west'];

            $all_cards = [];
            $used_cards = [];
            
            foreach ($suits as $suit) {
                foreach ($cards as $card) {
                    $all_cards[] = $suit . '_' . $card;
                }
            }
            
            foreach (['north', 'east', 'south'] as $pos) {
                foreach ($suits as $suit) {
                    foreach ($cards as $card) {
                        $field_name = $pos . '_' . $suit . '_' . $card;
                        if (isset($data[$field_name])) {
                            $used_cards[] = $suit . '_' . $card;
                        }
                    }
                }
            }
            
            $west_cards_keys = array_diff($all_cards, $used_cards);
            $hands = [];
            $total_cards = 0;
            
            foreach ($positions as $pos) {
                $hand_cards = [];
                $pos_total = 0;
                
                foreach ($suits as $suit) {
                    $suit_cards = [];
                    foreach ($cards as $card) {
                        $should_include = false;
                        
                        if ($pos == 'west') {
                            $card_key = $suit . '_' . $card;
                            $should_include = in_array($card_key, $west_cards_keys);
                        } else {
                            $field_name = $pos . '_' . $suit . '_' . $card;
                            $should_include = isset($data[$field_name]);
                        }
                        
                        if ($should_include) {
                            $pbn_card = ($card == '10') ? 'T' : $card;
                            $suit_cards[] = $pbn_card;
                            $pos_total++;
                            $total_cards++;
                        }
                    }
                    $hand_cards[] = empty($suit_cards) ? '' : implode('', $suit_cards);
                }
                
                if ($pos_total != 13) {
                    return [
                        'success' => false, 
                        'message' => "Errore: La mano di " . ucfirst($pos) . " ha $pos_total carte invece di 13."
                    ];
                }
                
                $hands[$pos] = implode('.', $hand_cards);
            }
            
            if ($total_cards != 52) {
                return [
                    'success' => false, 
                    'message' => "Errore: Totale carte = $total_cards invece di 52."
                ];
            }
            
            $pbn_content = "[Event \"Bridge Hand\"]\n";
            $pbn_content .= "[Site \"Hand Editor\"]\n";
            $pbn_content .= "[Date \"" . date('Y.m.d') . "\"]\n";
            $pbn_content .= "[Board \"$board_number\"]\n";
            $pbn_content .= "[West \"West\"]\n";
            $pbn_content .= "[North \"North\"]\n";
            $pbn_content .= "[East \"East\"]\n";
            $pbn_content .= "[South \"South\"]\n";
            $pbn_content .= "[Dealer \"$dealer\"]\n";
            $pbn_content .= "[Vulnerable \"$vulnerability\"]\n";
            $pbn_content .= "[Deal \"N:{$hands['north']} {$hands['east']} {$hands['south']} {$hands['west']}\"]\n";
            $pbn_content .= "[Scoring \"MP\"]\n\n";
            
			if (!is_dir('./upload/tornei/'.$torneo)) {
			  mkdir('./upload/tornei/'.$torneo);
			}

			if($board_number < 10) {
				$board_ = "0".$board_number;
			}else{
				$board_ = $board_number;			
			}
				
            $filename = "upload/tornei/".$torneo."/Board_" . $board_ . ".pbn";

            if (file_put_contents($filename, $pbn_content)) {
                return [
                    'success' => true,
                    'message' => "File PBN salvato con successo: $filename",
                    'pbn' => $pbn_content
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "Errore nel salvataggio del file PBN."
                ];
            }
        }
        ?>

        <form method="post" action="">
 
            <div class="progress-bar">
                <div class="progress-step" id="step-north">Nord</div>
                <div class="progress-step" id="step-east">Est</div>
                <div class="progress-step" id="step-south">Sud</div>
                <div class="progress-step" id="step-west">Ovest</div>
            </div>

            <?php
            $suits = [
                'spades' => ['symbol' => '♠', 'name' => 'Picche'],
                'hearts' => ['symbol' => '♥', 'name' => 'Cuori'], 
                'diamonds' => ['symbol' => '♦', 'name' => 'Quadri'],
                'clubs' => ['symbol' => '♣', 'name' => 'Fiori']
            ];
            
            $cards = ['A', 'K', 'Q', 'J', '10', '9', '8', '7', '6', '5', '4', '3', '2'];
            $positions = ['north' => 'Nord', 'east' => 'Est', 'south' => 'Sud', 'west' => 'Ovest'];
            
            $selected_cards = [];
            foreach ($positions as $pos => $pos_name) {
                foreach ($suits as $suit => $suit_info) {
                    foreach ($cards as $card) {
                        $field_name = $pos . '_' . $suit . '_' . $card;
                        if (isset($_POST[$field_name])) {
                            $selected_cards[$suit . '_' . $card] = $pos;
                        }
                    }
                }
            }
            
            foreach ($positions as $pos => $pos_name) {
                $extra_class = ($pos == 'west') ? 'west-auto' : '';
                echo "<div class='hand $extra_class' id='hand-$pos'>";
				if($pos == 'west')  echo "<h3>$pos_name (Automatico)</h3>";
                
                if ($pos == 'west') {
                    echo "<div class='west-cards'>";
                    echo "<p style='text-align: center; margin-bottom: 15px;'>Le carte di Ovest vengono calcolate automaticamente dalle carte rimanenti</p>";
                    echo "<div id='west-display'></div>";
                    echo "</div>";
					
					echo"				
						<div class=\"navigation\">
							<button type=\"button\" name=\"prevBtn\" class=\"btn prevBtn\" onclick=\"changeHand(-1)\">⬅️ Precedente</button>
						</div>
					";
					
                } else {
                    echo "<div style='display: flex; gap: 20px;'>";
                    
                    foreach ($suits as $suit => $suit_info) {
                        echo "<div style='flex: 1;'>";
                        echo "<div style='text-align: center; margin-bottom: 10px;'>";
                        echo "<span class='suit-symbol $suit'>{$suit_info['symbol']}</span>";
                         
                        $count = 0;
                        foreach ($cards as $card) {
                            $field_name = $pos . '_' . $suit . '_' . $card;
                            if (isset($_POST[$field_name])) $count++;
                        }
                        echo "</div>";
                        
                        foreach ($cards as $card) {
                            $field_name = $pos . '_' . $suit . '_' . $card;
                            $card_key = $suit . '_' . $card;
                            $checked = isset($_POST[$field_name]) ? 'checked' : '';
                            $disabled = '';
                            
                            if (isset($selected_cards[$card_key]) && $selected_cards[$card_key] != $pos) {
                                $disabled = 'disabled';
                            }
                            
							echo "<div class='card' style= 'text-align: center;'>";
							echo "<input type='checkbox' id='$field_name' name='$field_name' $checked $disabled>";
							echo "<label for='$field_name'>$card</label>";
							echo "</div>";
                        }
                        
                        echo "</div>";
                    }
                    
                    echo "</div>";
                    
                    $total = 0;
                    foreach ($suits as $suit => $suit_info) {
                        foreach ($cards as $card) {
                            $field_name = $pos . '_' . $suit . '_' . $card;
                            if (isset($_POST[$field_name])) $total++;
                        }
                    }
					echo"				
						<div class=\"navigation\">
							<button type=\"button\" name=\"prevBtn\" class=\"btn prevBtn\" onclick=\"changeHand(-1)\">⬅️ Precedente</button>
							<div class='hand-total'>".$pos_name.": <span id='total_$pos'>".$total."</span>/13</div>
					";
					
					if($pos_name=="Sud") {
						echo "<div id='south-buttons-container'>";
						echo "<button type=\"submit\" name=\"action\" value=\"save\" id=\"saveBtnSouth\" class=\"btn btn-save btn-save-pulse\" style=\"display:none;\">💾 Salva PBN</button>";
						echo "</div>";
					}else{
						echo "<button type=\"button\" id=\"nextBtn\" class=\"btn\" onclick=\"changeHand(1)\">Successivo ➡️</button>";						
					}	
								
					echo "</div>";

                }
                echo "</div>";
            }
            ?>

             <div class="controls" style="display:none;">
                <button type="submit" name="action" value="save" class="btn" id="mainSaveBtn">💾 Salva in PBN</button>
            </div>
        </form>
<?php


	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	   if (isset($_POST['action']) && $_POST['action'] === 'save') {
			$result = saveToPBN($_POST, $torneo, $board, $dealer, $vulnerability);
			if ($result['success']) {
				$save_success = true; // Impostiamo a true se salvato correttamente
			}
			echo "<div class='result " . ($result['success'] ? '' : 'error') . "'>";
			echo "<strong>" . ($result['success'] ? '✅ ' . $result['message'] : '❌ Errore!') . "</strong><br>";
			//echo "<strong>" . ($result['success'] ? '✅ Successo!' : '❌ Errore!') . "</strong><br>";
			//echo $result['message'];
		   
			if ($result['success'] && isset($result['pbn'])) {
				echo "<br><br><strong>Contenuto PBN generato:</strong><br>";
				echo "<pre style='background:#f8f9fa; padding:10px; border-radius:5px; overflow-x:auto;'>";
				echo htmlspecialchars($result['pbn']);
				echo "</pre>";
			}
			
			echo "</div>";
		}
	}
/*
    echo "<div class='result " . ($result['success'] ? '' : 'error') . "'>";
    //echo "<strong>" . ($result['success'] ? '✅ Successo!' : '❌ Errore!') . "</strong><br>";
    //echo $result['message'];
    if ($result['success'] && isset($result['pbn'])) {
        echo "<br><br><strong>Contenuto PBN generato:</strong><br>";
        echo "<pre style='background:#f8f9fa; padding:10px; border-radius:5px; overflow-x:auto;'>";
        echo htmlspecialchars($result['pbn']);
        echo "</pre>";
    }
    echo "</div>";
*/

?>
    </div>

<script>
        // Rileviamo se il PHP ha completato il salvataggio con successo
        const isSaved = <?php echo $save_success ? 'true' : 'false'; ?>;

        let currentHand = 0;
        const hands = ['north', 'east', 'south', 'west'];

        function tornaIndietro() {
            // Recupera l'URL salvato in precedenza
            var referrer = sessionStorage.getItem('urlProvenienza');
            
            if (!referrer) {
                console.warn("Nessun URL di provenienza trovato, provo il browser history...");
                window.history.back(); // Fallback se la sessione è scaduta
                return;
            }

            var separatore = referrer.includes('?') ? '&' : '?';

            var params = 'NomeTorneo=<?php echo urlencode($torneo); ?>' +
                        '&NumTurno=<?php echo urlencode($turno); ?>' +
                        '&tavolo=<?php echo urlencode($NumTavolo); ?>' +
                        '&giocatore=<?php echo urlencode($Giocatore ?? ''); ?>' +
                        '&boardAtt=<?php echo urlencode($board); ?>' +
                        '&orig=GestFoto';

            window.location.href = referrer + separatore + params;
        }

        function showCurrentHand() {
            hands.forEach((hand, index) => {
                const handElement = document.getElementById(`hand-${hand}`);
                const stepElement = document.getElementById(`step-${hand}`);
                
                if (index === currentHand) {
                    handElement.classList.remove('hidden');
                    stepElement.className = 'progress-step active';
                } else if (index < currentHand) {
                    handElement.classList.add('hidden');
					
					let total = 0; 
					const totalElement = document.getElementById(`total_${hand}`);
					
					if (totalElement) {
						total = parseInt(totalElement.textContent, 10); 
					}
									
					if(total === 13) {
						stepElement.className = 'progress-step completed';
					}else{
						stepElement.className = 'progress-step no-completed';
					} 			
					
                } else {
                    handElement.classList.add('hidden');
                    stepElement.className = 'progress-step pending';
                }
            });
            
            // Gestione dei pulsanti "Precedente" / "Torna" su tutte le mani operative
            const prevButtons = document.querySelectorAll('.prevBtn');
            prevButtons.forEach(btn => {
                if (isSaved) {
                    // Se salvato, diventa stabilmente il tasto "Torna"
                    btn.disabled = false;
                    btn.innerHTML = "↩️ Torna";
                    btn.onclick = tornaIndietro;
                    btn.className = "btn btn-clear";
                } else {
                    // Comportamento standard pre-salvataggio
                    btn.disabled = currentHand === 0;
                }
            });

            const nextBtn = document.getElementById('nextBtn');
            if(nextBtn) {
                nextBtn.disabled = currentHand === 3;
                nextBtn.textContent = currentHand === 2 ? 'Finalizza ➡️' : 'Successivo ➡️';
            }
        }

        function changeHand(direction) {
            const newHand = currentHand + direction;
            if (newHand >= 0 && newHand <= 3) {
                currentHand = newHand;
                showCurrentHand();
                updateWestHand();
            }
        }

        document.addEventListener('change', function(e) {
            if (e.target.type === 'checkbox' && e.target.name.includes('_')) {
                updateCardCounts();
                validateHands();
                updateCardAvailability();
                updateWestHand();
            }
        });

        function updateCardCounts() {
            const suits = ['spades', 'hearts', 'diamonds', 'clubs'];
            const positions = ['north', 'east', 'south'];
            
            positions.forEach(pos => {
                let total = 0;
                suits.forEach(suit => {
                    const checkboxes = document.querySelectorAll(`input[name^="${pos}_${suit}_"]`);
                    checkboxes.forEach(cb => {
                        if (cb.checked) {
                            total++;
                        }
                    });
                });
                const totalElement = document.getElementById(`total_${pos}`);
                if (totalElement) {
                    totalElement.textContent = total;
                    totalElement.style.color = total === 13 ? '#27ae60' : '#e74c3c';
                }
                
                // Gestione del pulsante di salvataggio per Sud
                if (pos === 'south') {
                    const saveBtnSouth = document.getElementById('saveBtnSouth');
                    if (saveBtnSouth) {
                        if (total === 13) {
                            saveBtnSouth.style.display = 'inline-block';
                        } else {
                            saveBtnSouth.style.display = 'none';
                        }
                    }
                }
            });
        }

        function updateCardAvailability() {
            const suits = ['spades', 'hearts', 'diamonds', 'clubs'];
            const cards = ['A', 'K', 'Q', 'J', '10', '9', '8', '7', '6', '5', '4', '3', '2'];
            const positions = ['north', 'east', 'south'];
            
            const selectedCards = {};
            positions.forEach(pos => {
                suits.forEach(suit => {
                    cards.forEach(card => {
                        const fieldName = `${pos}_${suit}_${card}`;
                        const checkbox = document.getElementById(fieldName);
                        if (checkbox && checkbox.checked) {
                            selectedCards[`${suit}_${card}`] = pos;
                        }
                    });
                });
            });
            
            positions.forEach(pos => {
                suits.forEach(suit => {
                    cards.forEach(card => {
                        const fieldName = `${pos}_${suit}_${card}`;
                        const cardKey = `${suit}_${card}`;
                        const checkbox = document.getElementById(fieldName);
                        
                        if (checkbox) {
                            if (selectedCards[cardKey] && selectedCards[cardKey] !== pos) {
                                checkbox.disabled = true;
                            } else {
                                checkbox.disabled = false;
                            }
                        }
                    });
                });
            });
        }

        function updateWestHand() {
            const suits = ['spades', 'hearts', 'diamonds', 'clubs'];
            const suitSymbols = {'spades': '♠', 'hearts': '♥', 'diamonds': '♦', 'clubs': '♣'};
            const cards = ['A', 'K', 'Q', 'J', '10', '9', '8', '7', '6', '5', '4', '3', '2'];
            const positions = ['north', 'east', 'south'];
            
            const usedCards = new Set();
            positions.forEach(pos => {
                suits.forEach(suit => {
                    cards.forEach(card => {
                        const fieldName = `${pos}_${suit}_${card}`;
                        const checkbox = document.getElementById(fieldName);
                        if (checkbox && checkbox.checked) {
                            usedCards.add(`${suit}_${card}`);
                        }
                    });
                });
            });
            
            const westCards = {};
            suits.forEach(suit => {
                westCards[suit] = [];
                cards.forEach(card => {
                    const cardKey = `${suit}_${card}`;
                    if (!usedCards.has(cardKey)) {
                        westCards[suit].push(card);
                    }
                });
            });
            
            let westDisplay = '';
            let westTotal = 0;
            suits.forEach(suit => {
                westDisplay += `<div style="margin-bottom: 10px;">`;
                westDisplay += `<span style="font-size: 24px; font-weight: bold; margin-right: 10px; color: ${suit === 'hearts' || suit === 'diamonds' ? '#e74c3c' : '#2c3e50'};">${suitSymbols[suit]}</span>`;
                westDisplay += `<span style="font-size: 16px;">${westCards[suit].length > 0 ? westCards[suit].join(' ') : 'Nessuna'} (${westCards[suit].length})</span>`;
                westDisplay += `</div>`;
                westTotal += westCards[suit].length;
            });
            westDisplay += `<div style="text-align: center; margin-top: 15px; font-weight: bold; color: ${westTotal === 13 ? '#27ae60' : '#e74c3c'};">Totale: ${westTotal}/13</div>`;
            
            document.getElementById('west-display').innerHTML = westDisplay;
        }

        function validateHands() {
            const positions = ['north', 'east', 'south'];
            let totalUsedCards = 0;
            
            positions.forEach(pos => {
                const totalElement = document.getElementById(`total_${pos}`);
                if (totalElement) {
                    totalUsedCards += parseInt(totalElement.textContent);
                }
            });
            
            const saveButton = document.getElementById('mainSaveBtn');
            if (!saveButton) return;
            
            if (totalUsedCards > 39) {
                saveButton.style.backgroundColor = '#e74c3c';
                saveButton.textContent = '❌ Troppe carte (' + totalUsedCards + '/39)';
            } else if (totalUsedCards < 39) {
                saveButton.style.backgroundColor = '#f39c12';
                saveButton.textContent = '⚠️ Mancano carte (' + totalUsedCards + '/39)';
            } else {
                saveButton.style.backgroundColor = 'green';
                saveButton.textContent = '💾 Salva in PBN';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            showCurrentHand();
            updateCardCounts();
            validateHands();
            updateCardAvailability();
            updateWestHand();
        });
    </script>
</body>
</html>
