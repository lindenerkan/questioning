<?php // content="text/plain; charset=utf-8"
require_once ('jpgraph.php');
require_once ('jpgraph_pie.php');
require_once ('jpgraph_pie3d.php');

//echo $_POST["d1"];

// Some data

$count=$_GET["count"];
for ($i=1;$i<=$count;$i++)
{
    $data[]=$_GET["d".$i];
    $text[]=$_GET["t".$i];
}
//$data = array($_GET["d1"],$_GET["d2"],$_GET["d3"],$_GET["d4"],$_GET["d5"]);


// Create the Pie Graph.
$graph = new PieGraph(450,400);
$graph->SetShadow();


// Set A title for the plot
$graph->title->Set($_GET["title"]);
$graph->title->SetFont(FF_VERDANA,FS_BOLD,18); 
$graph->title->SetColor("darkblue");
$graph->legend->Pos(0.3,0.9);

// Create 3D pie plot
$p1 = new PiePlot3d($data);
$p1->SetTheme("sand");
$p1->SetCenter(0.5);
$p1->SetSize(0.4);
$p1->SetHeight(10);

// Adjust projection angle
$p1->SetAngle(60);

// You can explode several slices by specifying the explode
// distance for some slices in an array
//$p1->Explode(array(0,40,0,30));

// As a shortcut you can easily explode one numbered slice with
// $p1->ExplodeSlice(3);

//$p1->value->SetFont(FF_ARIAL,FS_NORMAL,10);
$p1->SetLegends($text);

$graph->Add($p1);
//$graph->Stroke('../graphs/img.png');
$graph->Stroke();
?>


