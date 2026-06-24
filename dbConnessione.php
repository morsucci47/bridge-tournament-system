<?php
// VARIABILI DI SISTEMA
	$home_pages="https://MIO-SITO.it/";
	$home_proc="https://MIA-CARTELLA-PROGRAMMA";
	$home_archive="MIA-CARTELLA-ARCHIVIO";

/*
  Connessione al DBMS e selezione del dataabse.
*/
# blocco dei parametri di connessione
// nome di host
$host = "localhost";
// username dell'utente in connessione
$user = "UTENTE-DATABASE";
// password dell'utente
$password = "PSWD-DATABASE";
// nome del database
$db = "my_database";


# stringa di connessione al DBMS
// istanza dell'oggetto della classe MySQLi
$connessione = new mysqli($host, $user, $password, $db);

// verifica su eventuali errori di connessione
if ($connessione->connect_errno) {
    echo "Connessione fallita: ". $connessione->connect_error . ".";
    exit();
}
?>