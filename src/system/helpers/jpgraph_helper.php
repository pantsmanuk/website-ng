<?php
function acpstatschart(	$xdata, $title=NULL, $width = 350, $height = 250,
						$legend1, $ydata1, 
						$legend2, $ydata2, 
						$legend3, $ydata3
						)
{
    require_once("jpgraph/jpgraph.php");
    require_once("jpgraph/jpgraph_line.php");    
    
    // Create the graph. These two calls are always required
    $graph = new Graph($width,$height,"auto",60);
    $graph->SetScale("textlin");
    
    $theme_class=new UniversalTheme;
    
    $graph->SetBox(false);
    $graph->img->SetAntiAliasing();
    
    $graph->xgrid->Show();
	$graph->xgrid->SetLineStyle("solid");
    $graph->xaxis->SetTickLabels($xdata);
    $graph->xgrid->SetColor('#E3E3E3');
    
    // Setup title
    if($title != NULL){
    $graph->title->Set($title);   
    }
    
    // Create the first line (Current) red
	$p1 = new LinePlot($ydata1);
	$graph->Add($p1);
	$p1->SetColor("#990000");
	$p1->SetLegend($legend1);
	
	// Create the second line (Year - 1) blue bbddff
	$p1 = new LinePlot($ydata2);
	$graph->Add($p1);
	$p1->SetColor("#bbccff");
	$p1->SetLegend($legend2);
	
	// Create the third line (Year - 2) green bbff99
	$p1 = new LinePlot($ydata3);
	$graph->Add($p1);
	$p1->SetColor("#aaff77");
	$p1->SetLegend($legend3);
	
	$graph->legend->SetFrameWeight(1);
	
	$graph->yaxis->scale->SetAutoMin(0);
    
    
    return $graph; // does PHP5 return a reference automatically?
}

?>