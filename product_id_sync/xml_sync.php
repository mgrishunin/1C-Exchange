<?
// заказчивает внутренний код 1С на сайт в колонку  code_1C
	ini_set("max_execution_time", "600"); 
	
// подключаем подключение к СУБД	
	include ("../system/conf.php");
	include ("../system/connect_db.php");
	error_reporting(E_ALL | E_STRICT) ;
	ini_set('display_errors', 'On');
	
	
// путь к папке с файлами обмена,  выгруженными из 1С 	
$dir = 'obmen';

// считываем содержимое папки (имена файлов)
$files = scandir($dir);


echo $files[2];
// формируем пруть к первому файлу в папке
$filen=$dir."/".$files[2];
// если длина файла с расширением более 5 символов то загружаем XML из него
if(strlen($files[2])>5) {

$xml = simplexml_load_file($filen);

//print_r($xml);


// перебираем товары в XML
foreach ($xml->Каталог->Товары->Товар as $ttt)

{

//print_r($ttt);
	$art=$ttt->Артикул;
	$base_code=$ttt->Ид;
	$product_name=$ttt->Наименование;
$product_name=iconv('utf-8','windows-1251', $product_name);	


// ищем товар из XML по артикулу в табилце product СУБД сайта
  $sql_request2 = "select id from product where articul='$art'";
  $rc2=mysql_query($sql_request2,$dbh);
  $n_rows=mysql_num_rows($rc2);
	
	
// если находим - апдейтим знасчение 	code_1C для этого товара	
		if($n_rows>0) { 
			
	$sql_request = "update product set code_1C='$base_code' WHERE articul='$art'";
 echo $sql_request."<br>";

   $rc = mysql_query($sql_request,$dbh);
   if (!$rc) {
        print "cant update table";
        exit;
	}		
			
			
		}

	
}
// удаляем обработанный файл, чтобы при следующей итерации обработать джругой
unlink($filen);
echo "Удален файл ".$filen;
}
?>
