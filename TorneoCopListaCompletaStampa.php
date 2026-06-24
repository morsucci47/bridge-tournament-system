<?php  
// inclusione del file di connessione
include "dbConnessione.php";

//  RICEVE IL PARAMETRO TURNO IN ESAME
	$NomeTorneo= $_GET['torneo'];
	$Origine= $_GET['orig'];

    /*
	$NumTurno= $_GET['turno'];
	if (!is_numeric($NumTurno)) {
	    exit("Manca il numero del turno");
	}	
	*/
//  CONTROLLA LA PRESENZA DEL TORNEO INSERITO NELLA TABELLA brdg_ind_tornei
	//  E RICAVA IL SUO ID
	$sql= "SELECT * FROM `brdg_cop_tornei` WHERE `NomeTorneo`= '".$NomeTorneo."'";
	$dati = $connessione->query($sql);
	$row = $dati->fetch_assoc(); 
	$ID_torneo= $row['ID_torneo'];
	//$Tipo= $row['Tipo'];
	//$turno= $row['turno'];	
    if (!is_numeric($ID_torneo)) {
		// chiusura della connessione
		$connessione->close();
		exit("<body bgcolor=\"#a0eea0\"><b><center>Il torneo \"". $torneo."\" non esiste</center></b></body>");
	 }


//------------------------------------------------------
	// -------- LEGGE IL NUMERO DI PARTECIPANTI ----------
	//               NON SERVE
/*	
	$sql="SELECT COUNT(*) FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo;
	$dati= $connessione->query($sql); 
	$row = $dati->fetch_array();
	$Niscritti= $row[0];
*/
//echo "  Tipo ----->".$Tipo;
//echo "<br>";

//------------------------------------------------------
	
	// ##############################################
	// STAMPA LA LISTA DEGLI ISCRITTI 
	// ##############################################
	

	$sql="SELECT * FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo." ORDER BY `coppiaID`" ;			   
//echo "sql=  ".$sql;			   
//echo "<br>";
	$dati= $connessione->query($sql); 
	$row = $dati->fetch_assoc();		
		
	///////////////////////////////////////////////////////////////////////////////

    require('./fpdf186/fpdf.php');
	
// Creazione del documento PDF
	$pdf = new FPDF();
	$pdf->AddPage();
// Titolo
	$pdf->SetFont('Arial','B',16);

	$pdf->Cell(0,10,'ELENCO ISCRITTI AL TORNEO:'.$NomeTorneo,0,1,'C');

//$pdf->Ln();
// Intestazione della tabella
	$pdf->Cell(30,10,'Coppia N.',1,0,'C');
	$pdf->Cell(75,10,'Giocatore 1',1,0,'C');
	$pdf->Cell(75,10,'Giocatore 2',1,1,'C');

 /**/

	$k= 0;
	
	while($row) {
		$k++;
		$coppiaID= $row['coppiaID'];
		//  CASO DI TORNEO MITCHELL
		//$coppiaID_= $coppiaID;		
		//if($coppiaID > $Niscritti/2 && $Tipo==2) $coppiaID_= $coppiaID-$Niscritti/2 + 100;
		
		$dati1= $connessione->query("SELECT * FROM `brdg_cop_coppie` WHERE `coppiaID`=".$coppiaID." and `torneoID`=".$ID_torneo); 
    	$row1 = $dati1->fetch_assoc();
		$nome1ID= $row1['nome1ID'];
		$nome2ID= $row1['nome2ID'];
	
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE `ID`=".$nome1ID); 
    	$row1 = $dati1->fetch_assoc();
		$Nome1= $row1['nome'];
		
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE `ID`=".$nome2ID); 
    	$row1 = $dati1->fetch_assoc();
		$Nome2= $row1['nome'];

		$row = $dati->fetch_assoc();

		//$k1= $k+1;	
		
		//$pdf->Cell(15,10,$k,1,0,'C');
		$pdf->Cell(30,10,$coppiaID,1,0,'C');
		$pdf->Cell(75,10,$Nome1,1,0,'L');
		$pdf->Cell(75,10,$Nome2,1,1,'L');
	}
	
/////////////////////////////////////////////////////////////////////////////////	
$pdf->Output();
// chiusura della connessione
$connessione->close();
	
	
	
