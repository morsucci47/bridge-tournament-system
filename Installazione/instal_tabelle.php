<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Untitled</title>
</head>

<body>
<?php 
	include "../dbConnessione.php";


	

	
	$sql= "CREATE TABLE IF NOT EXISTS `brdg_cop_coppie` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `torneoID` int DEFAULT NULL,
  `coppiaID` smallint DEFAULT NULL,
  `nome1ID` smallint DEFAULT NULL,
  `nome2ID` smallint DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;
";
	$dati = $connessione->query($sql);
    	if (!$dati) {
		   // chiusura della connessione
		   exit("<body bgcolor=\"#a0eea0\"><b><center>Errore</center></b></body>");
	 	}

	

/*	
	$sql= "CREATE TABLE IF NOT EXISTS `brdg_cop_licita` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `licitaID` int DEFAULT NULL,
  `bid` char(8) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;
";
	$dati = $connessione->query($sql);
    	if (!$dati) {
		   // chiusura della connessione
		   exit("<body bgcolor=\"#a0eea0\"><b><center>Errore tabella Licita</center></b></body>");
	 	}
*/
	

	
	$sql= "CREATE TABLE IF NOT EXISTS `brdg_cop_scores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `torneoID` int NOT NULL,
  `tavolo` tinyint DEFAULT NULL,
  `turno` tinyint DEFAULT NULL,
  `board` tinyint DEFAULT NULL,
  `coppiaNS` int DEFAULT NULL,
  `coppiaEW` int DEFAULT NULL,
  `score` int DEFAULT NULL,
  `licitaID` int DEFAULT NULL,
  `Contratto` varchar(6) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `GiocatoDa` varchar(1) NOT NULL,
  `Prese` tinyint NOT NULL,
  `Attacco` varchar(5) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `secondi` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;
";
	$dati = $connessione->query($sql);
    	if (!$dati) {
		   // chiusura della connessione
		   exit("<body bgcolor=\"#a0eea0\"><b><center>Errore tabella scores</center></b></body>");
	 	}

	

/*	
	$sql= "CREATE TABLE IF NOT EXISTS `brdg_cop_tavoli` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `tavolo` tinyint DEFAULT NULL,
  `licitaID` int DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
";
	$dati = $connessione->query($sql);
    	if (!$dati) {
		   // chiusura della connessione
		   exit("<body bgcolor=\"#a0eea0\"><b><center>Errore</center></b></body>");
	 	}
*/
	
	$sql= "CREATE TABLE IF NOT EXISTS `brdg_cop_tornei` (
  `ID_torneo` int NOT NULL AUTO_INCREMENT,
  `NomeTorneo` varchar(32) DEFAULT NULL,
  `TurnoAttuale` tinyint NOT NULL,
  `Stato` tinyint(1) DEFAULT NULL,
  `Turni` tinyint DEFAULT NULL,
  `BoardsXturno` tinyint DEFAULT NULL,
  `Punteggio` tinyint DEFAULT NULL,
  `Tipo` tinyint(1) DEFAULT NULL,
  `Data` date NOT NULL,
  `Ora` text,
  `Inizio` int DEFAULT NULL,
  PRIMARY KEY (`ID_torneo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;";
	$dati = $connessione->query($sql);
    	if (!$dati) {
		   // chiusura della connessione
		   exit("<body bgcolor=\"#a0eea0\"><b><center>Errore tabella tornei</center></b></body>");
	 	}

	$sql= "CREATE TABLE IF NOT EXISTS `brdg_ind_rubrica` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(24) DEFAULT NULL,
  `nick` varchar(12) DEFAULT NULL,
  `telefono` varchar(16) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `associazione` varchar(24) DEFAULT NULL,
  `categoria` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=50 ;";
	$dati = $connessione->query($sql);
    	if (!$dati) {
		   // chiusura della connessione
		   exit("<body bgcolor=\"#a0eea0\"><b><center>Errore tabella rubrica</center></b></body>");
	 	}

	$sql= "INSERT INTO `brdg_ind_rubrica` (`ID`, `nome`, `nick`, `telefono`, `email`, `associazione`) VALUES
(1, 'Antonio Orsucci', 'morsucci', '3292571847', 'marzio.orsucci@gmail.com', 'mongolfiera'),
(9, 'Sergio Bonafous', 'higuma', '+333', '---', ''),
(7, 'Franco Sestan', 'francosest', '+333', '---', ''),
(8, 'Paolo Moretti', 'more777', '-----', '----', 'mongolfiera'),
(10, 'Samuele Stucchi', 'plinioj', '-----', '----', 'mongolfiera'),
(11, 'Beppe Montevecchi', 'beppebridg', '+333', '---', ''),
(12, 'Ettore Malpezzi', 'malpett09', '+333', '---', ''),
(13, 'Mauro Di Gioia', 'deejay50', '+333', '---', ''),
(24, 'Sergio Bassi', 'grest50', '3420573676', 'sergio.bassi@50gmail.com', 'ArcaBridge'),
(25, 'Benedetta Ragazzi', 'dindili', '335384148', 'beneragazzi@me.com', 'Arca<<Bridge'),
(26, 'fulvia panarari', 'guerra45', '', 'fulvia.panarari@libero.it', 'arcabridge'),
(27, 'achille valenzini', 'xxxxx11', '33917177', 'valenziniachille@libero.it', 'Arca Bridge'),
(28, 'Franca Scendrate', '18franca51', '3490501500', 'franca.scendrate@gmail.com', 'Bridge Institute 2000'),
(29, 'giuliano scaglia', 'vittoria70', '3403001634', 'gscaglia6@gmail.com', 'lamongolfiera'),
(30, 'marcello scotti', 'Zaza195', '3357463653', 'marcello.scotti.ms@gmail.com', 'Bridge institut 2000'),
(31, 'Patrizia', 'pepepavel', '3388271433', '', ''),
(32, 'Michele Tonon', 'micheleton', '3333022599', 'mictonon@libero.it', 'mongolfiera'),
(34, 'luciana de georgio', 'briluna', '025462197', 'fosfora@libero.it', ''),
(44, 'Giorgio ferrari', NULL, NULL, NULL, NULL),
(45, 'Elisa ferrari', NULL, NULL, NULL, NULL);
";
	$dati = $connessione->query($sql);
    	if (!$dati) {
		   // chiusura della connessione
		   exit("<body bgcolor=\"#a0eea0\"><b><center>Errore insert rubrica</center></b></body>");
	 	}

	
	$sql= "CREATE TABLE IF NOT EXISTS `brdg_cop_pswd` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `password` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1  ;";
	$dati = $connessione->query($sql);
    	if (!$dati) {
		   // chiusura della connessione
		   exit("<body bgcolor=\"#a0eea0\"><b><center>Errore tabella pswd</center></b></body>");
	 	}

	
	

		
		
   $connessione->close();

 ?>		


</body>
</html>

