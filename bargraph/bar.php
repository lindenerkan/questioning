<?php // content="text/plain; charset=utf-8"
// Example for use of JpGraph, 
// ljp, 01/03/01 20:32
require_once ('jpgraph.php');
require_once ('jpgraph_bar.php');

// We need some data
//$datay=array(10,5,3,8,4,1);
//$datax=array("Jan","Feb","Mar","Apr","May","June");

$count=$_GET["count"];
for ($i=1;$i<=$count;$i++)
{
	$datay[]=$_GET["d".$i];
	$datax[]=$_GET["t".$i];
}


// Setup the graph. 
$graph = new Graph(450,300);	
$graph->img->SetMargin(80,40,45,100);
$graph->SetScale("textlin");
$graph->SetMarginColor("silver");
$graph->SetShadow();

// Set up the title for the graph
$graph->title->Set($_GET["title"]);
$graph->title->SetFont(FF_VERDANA,FS_NORMAL,18);
$graph->title->SetColor("#3F5666");

// Setup font for axis
$graph->xaxis->SetFont(FF_VERDANA,FS_NORMAL,12);
$graph->xaxis->SetColor("black","black");
$graph->yaxis->SetFont(FF_VERDANA,FS_NORMAL,8);

// Show 0 label on Y-axis (default is not to show)
$graph->yscale->ticks->SupressZeroLabel(false);

// Setup X-axis labels
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetLabelAngle(0);

// Create the bar pot
$bplot = new BarPlot($datay);
$bplot->SetWidth(0.5);

// Setup color for gradient fill style 
$bplot->SetFillGradient("#3F5666","#3F5666",GRAD_MIDVER);

// Set color for the frame of each bar
$bplot->SetColor("black");
$bplot->SetColor("red");
$graph->Add($bplot);

// Finally send the graph to the browser
$graph->Stroke();
?>
