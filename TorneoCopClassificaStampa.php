
<?php  

include_once "dbConnessione.php";
	  $Punt=0;	
      $azione = $_POST['action'];
      if($azione=="MP") {
	     $Punt= 1;
      }
      if($azione=="POM") {
	     $Punt= 2;
      }   
      if($azione=="IMP") {
	     $Punt= 3;
      }   
      if($azione=="DMP") {
	     $Punt= 4;
      }   

//  RICEVE TORNEO E IL PARAMETRO TURNO IN ESAME
	$NomeTorneo= $_GET['torneo'];
	$NumTurno= $_GET['turno'];
	$Origine= $_GET['orig'];
	if (!is_numeric($NumTurno)) {
	    exit("Manca il numero del turno");
	}	
	

//  RICEVE TORNEO E IL PARAMETRO TURNO IN ESAME
	//$NomeTorneo="Natale22";
	//$NumTurno= 10;
	//$NomeTorneo= $_GET['torneo'];
	//$NumTurno= $turno;
	//$Origine= $_GET['orig'];
	//if (!is_numeric($NumTurno)) {
	  //  exit("Manca il numero del turno");
	//}	
//  CONTROLLA LA PRESENZA DEL TORNEO INSERITO NELLA TABELLA brdg_ind_tornei
	//  E RICAVA IL SUO ID
	$sql= "SELECT * FROM `brdg_cop_tornei` WHERE `NomeTorneo`='".$NomeTorneo."'";
//echo "  sql ----->".$sql;
//echo "<br>";
	$dati = $connessione->query($sql);
	$row = $dati->fetch_assoc(); 
	$ID_torneo= $row['ID_torneo'];
	$NboardsXturno= $row['BoardsXturno'];
	$Nturni= $row['Turni'];
	$Punteggio= $row['Punteggio'];
	$Tipo= $row['Tipo'];
    if (!is_numeric($ID_torneo)) {
		// chiusura della connessione
		$connessione->close();
		exit("<body bgcolor=\"#a0eea0\"><b><center>Il torneo \"". $torneo."\" non esiste</center></b></body>");
	}
	$Nboards= $NboardsXturno * $Nturni;
	
	// -------- LEGGE IL NUMERO DI GIOCATORI ----------
	$sql="SELECT COUNT(*) FROM `brdg_cop_coppie` WHERE `torneoID`=".$ID_torneo;
	$dati= $connessione->query($sql); 
	$row = $dati->fetch_array();
	$Ncoppie= $row[0];

	$NcoppieBRD= $Ncoppie/2;
    $MPteorici= ($Ncoppie/2-1)*2;
/*	
	*/
/*	
echo "  Ncoppie ----->".$Ncoppie;
echo "<br>";
echo "  Nboards ----->".$Nboards;
echo "<br>";
echo "  NcoppieBRD ----->".$NcoppieBRD;
echo "<br>";
echo "  MPteorici -->".$MPteorici;
echo "<br>";
echo "  Punteggio -->".$Punteggio;
echo "<br>";
*/
	
//  CONTROLLA LA COMPLETEZZA DEI RISULTATI PER QUESTO TURNO
	$sql="SELECT * FROM `brdg_cop_scores` WHERE  torneoID= ".$ID_torneo." AND turno = ".$NumTurno." AND score IS NULL" ;
//echo "  sql ----->".$sql;
//echo "<br>";
	$dati= $connessione->query($sql); 
	$row = $dati->fetch_assoc();
	if($row) {
		$completa= false;
	}else{
		$completa= true;
	}	

	//###############################################
	//QUI INSERISCE IL CALCOLO DELLE PERCENTUALI O DEI VOTI
	include "TorneoCopCalcClassifica.php";
/*	
*/		
		
	// ##############################################width=\"600\" width=\"600\"
	// Mostra la tabella della classifica



require('./fpdf186/fpdf.php');


// Creazione del documento PDF
$pdf = new FPDF();
$pdf->AddPage();
// Titolo
$pdf->SetFont('Arial','B',16);

	$pdf->Cell(0,10,'CLASSIFICA DEL TORNEO:'.$NomeTorneo,0,1,'C');
	if($completa==false) {
 		$pdf->Cell(0,10,'I RISULTATI NON SONO COMPLETI',0,1,'C');
	}

// Intestazione della tabella

//$pdf->Ln();
// Intestazione della tabella
	$pdf->Cell(15,10,'Pos',1,0,'C');
	$pdf->Cell(20,10,'Coppia',1,0,'C');
	$pdf->Cell(68,10,'Giocatore 1',1,0,'C');
	$pdf->Cell(68,10,'Giocatore 2',1,0,'C');
	//$pdf->Cell(20,10,'Punti',1,0,'C');

   if($Punteggio==1)  {
	   $pdf->Cell(20,10,'Perc',1,1,'C'); 
   }else if($Punteggio==2)  {
	   $pdf->Cell(20,10,'Voto',1,1,'C'); 
   }else if($Punteggio==3)  {
	   $pdf->Cell(20,10,'IMP',1,1,'C'); 
   }else if($Punteggio==4)  {
	   $pdf->Cell(20,10,'DMP',1,1,'C'); 
   }
/**/
	//  MOSTRA LA CLASSIFICA

	$k= 0;
	
	foreach ($PercMedia as $key => $value) {
		$Coppia[$k]=$key+1;		
		$CopID= $Coppia[$k];
			//  CASO DI TORNEO MITCHELL
		$coppiaID_= $CopID;		
		if($coppiaID_ > $Ncoppie/2 && $Tipo==2) $coppiaID_= $coppiaID_-$Ncoppie/2 + 100;
		
		$sql= "SELECT * FROM brdg_cop_coppie WHERE coppiaID=$CopID and torneoID= $ID_torneo";
		$dati = $connessione->query($sql); 
		$row = $dati->fetch_assoc();	
		$Nome1ID= $row['nome1ID'];
		$Nome2ID= $row['nome2ID'];
		
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$Nome1ID"); 	
		$row1 = $dati1->fetch_assoc();
		$Nome1= $row1['nome'];	
		$dati1= $connessione->query("SELECT * FROM `brdg_ind_rubrica` WHERE ID=$Nome2ID"); 	
		$row1 = $dati1->fetch_assoc();
		$Nome2= $row1['nome'];	

		$k1= $k+1;	
		$PercMedia_2 = number_format($PercMedia[$CopID-1], 2);
		
		$pdf->Cell(15,10,$k1,1,0,'C');
		$pdf->Cell(20,10,$coppiaID_,1,0,'C');
		$pdf->Cell(68,10,$Nome1,1,0,'L');
		$pdf->Cell(68,10,$Nome2,1,0,'L');
		//$pdf->Cell(20,10,round($MP_Media[$CopID-1],2),1,0,'R');
		$pdf->Cell(20,10,$PercMedia_2,1,1,'R');
				
		$k += 1;
	}
/**/

$pdf->Output();
// chiusura della connessione
$connessione->close();

 ?>		


	
	
