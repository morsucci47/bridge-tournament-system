<?php
  $torneo = $_GET['torneo'];
  $board  = $_GET['board'];
echo "torneo: ".$torneo ;
echo  "<br>";
echo "board: ".$board ;
echo  "<br>";
//  controlla esistenza della cartella torneo 
//  e se non esiste la ccrea
if (!is_dir('./upload/tornei/'.$torneo)) {
  mkdir('./upload/tornei/'.$torneo);
}else{
}



// new filename  date('YmdHis')
$filename = 'pic_'.date('YmdHis').'.jpeg';
// file rinominato
$fileout = 'N_'.$board.'.jpeg';

$url = '';
if( move_uploaded_file($_FILES['webcam']['tmp_name'],'upload/'.$filename) ){
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/upload/' . $filename;
}
rename('./upload/'.$filename, './upload/tornei/'.$torneo.'/'.$fileout);
// Return image url
echo $url;
?>