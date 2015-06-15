<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$startdate = $_GET['startdate'];
$enddate = $_GET['enddate'];
$serverip = $_GET['serverip'];

/* Include the pData class */
include 'pChart/pData.class';
include 'pChart/pCache.class';
include 'pChart/pChart.class';

$servername = "localhost";
$username = "mymon";
$password = "chai7EeJ";

//создаем объект данных
$myData = new pData();

/* Коннектимся к MySQL базе данных */
$db = mysql_connect($servername, $username, $password);
if ( $db == "" ) { echo " DB Connection error...\r\n"; exit(); }

mysql_select_db("mymon", $db);

$Requete = "SELECT `la`,`datetime` FROM `logs` WHERE serverip = '" .$serverip. "' AND `datetime` <= '" .$startdate. "' ORDER BY `datetime` DESC LIMIT 100;";
#echo $Requete. "<p>";
$result = mysql_query($Requete, $db);
while($row = mysql_fetch_array($result))
{
    $myData->AddPoint($row["datetime"],"datetime");
    $myData->AddPoint($row["la"],"la");
}

//устанавливаем точки с датами
//на ось абсцисс
$myData->SetAbsciseLabelSerie("datetime");
//помечаем данные как предназначеные для
//отображения
$myData->AddSerie("la");
//устанавливаем имена
$myData->SetSerieName(mb_convert_encoding("la",'utf-8','windows-1251'), "la");
//создаем график шириной в 1000 и высотой в 500 px
$graph = new pChart(1000,600);
//устанавливаем шрифт и размер шрифта
$graph->setFontProperties("Fonts/tahoma.ttf",10);
//координаты левой верхней вершины и правой нижней
//вершины графика
$graph->setGraphArea(85,30,950,400);
//рисуем заполненный четырехугольник
$graph->drawFilledRoundedRectangle(7,7,993,593,5,240,240,240);
//теперь незаполненный для эффекта тени
$graph->drawRoundedRectangle(5,5,995,595,5,230,230,230);
//прорисовываем фон графика
$graph->drawGraphArea(255,255,255,TRUE);
//устанавливаем данные для графиков
$graph->drawScale($myData->GetData(), $myData->GetDataDescription(), SCALE_NORMAL,150,150,150,true,90,2);
//рисуем сетку для графика
$graph->drawGrid(4,TRUE,230,230,230,50);
//прорисовываем линейные графики
$graph->drawLineGraph($myData->GetData(), $myData->GetDataDescription());
// рисуем точки на графике
$graph->drawPlotGraph($myData->GetData(), $myData->GetDataDescription(),3,2,255,255,255);
// пишем в подвале некоторый текст
$graph->setFontProperties("Fonts/tahoma.ttf",10);
$graph->drawTextBox(870,450,990,660,"Powered By pChart", 0,250,250,250,ALIGN_CENTER,TRUE,-1,-1,-1,30);
$graph->drawTextBox(805,470,990,680,"http://pchart.sourceforge.net", 0,250,250,250,ALIGN_CENTER,TRUE,-1,-1,-1,30);
$graph->drawTextBox(15,450,140,660,"Developed By Himaster", 0,250,250,250,ALIGN_CENTER,TRUE,-1,-1,-1,30);
//ложим легенду
$graph->drawLegend(90,35,$myData->GetDataDescription(),255,255,255);
//Пишем заголовок
$graph->setFontProperties("Fonts/tahoma.ttf",10);
$graph->drawTitle(480,22, mb_convert_encoding($serverip. " LA", 'utf-8','windows-1251'), 50,50,50,-1,-1,true);
//выводим в браузер
$graph->render("images/graph.png");
$graph->stroke();
?>