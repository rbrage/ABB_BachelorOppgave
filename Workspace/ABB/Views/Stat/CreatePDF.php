<?php
require_once 'Models/fpdf.php';

	$cache = new Cache();
	$pointlist = new CachedArrayList();
	$clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
	$masterlist = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
	$outlierlist = new CachedArrayList(ListNames::OUTLYINGPOINTLISTNAME);
	
	$cacheinfo = $cache->getCacheInfo();
	
	class PDF extends FPDF
	{
	// Page header
	function Header()
	{
		// Logo
		$this->Image('img/ABB.png',10,6,30);
		$this->SetFont('Arial','B',15);
		$this->Cell(80);
		$this->Cell(50,10,'Analyse Raport',1,0,'C');
		$this->Ln(20);
	}

	// Page footer
	function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
	
	//Page chapter
	function ChapterTitle($num, $label)
	{
		$this->Ln(4);
		$this->SetFont('Arial','',12);
		$this->SetFillColor(200,220,255);
		$this->Cell(0,6,"$num : $label",0,1,'L',true);
		$this->Ln(4);
	}
	//Page infomation
	function Information($header, $data){
	
	// Colors, line width and bold font
		$this->SetFillColor(255,220,220);
		$this->SetTextColor(0);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('Arial','',12);;
		// Header
		$w = array(40,40, 50, 45);
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
		$this->Ln();
		// Color and font restoration
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('');
		// Data
		$fill = false;
		for($i=0;$i<count($data);$i++){
			$this->Cell($w[$i],6,$data[$i],'LR',0,'L',$fill);
			$fill = !$fill;
		}
		// Closing line
		$this->Ln();
		$this->Cell(array_sum($w),0,'','T');
	
	}
	// Page cluste header
	function ClusterHeader($clusterID){
		$this->SetFont('Arial','B',16);
		$this->Cell(40,10,"Cluster $clusterID",0,0,'L',false);
		$this->Ln();
	}

	// Page cluster information
	function ClusterTable($header, $data){
		$this->SetFont('Times','',12);
		// Column widths
		$w = array(40, 35, 40, 45);
		// Header
		for($i=0;$i<count($header);$i++){
			$this->Cell($w[$i],7,$header[$i],1,0,'L');
			}
		$this->Ln();
		// Data
		for($i=0;$i<count($data);$i++){
			$this->Cell($w[$i],6,$data[$i],'LR',0,'L',false);
			
		}
		// Closing line
		$this->Ln();
		$this->Cell(array_sum($w),0,'','T');
		$this->Ln(2);
		}
		
	// Page cluster information
	function ClusterTable2($header2, $data2, $distanseMaster){
		$this->SetFont('Times','',12);
		// Column widths
		$w = array(40, 35, 85);
		// Header
		$this->Cell($w[0],7,$header2[0],1,0,'L');
		$this->Cell($w[1],7,"",1,0,'L');
		$this->Cell($w[2],7,"Distance from the master point",1,0,'L');
			
		$this->Ln();
		$once = false;
		// Data
		for($i=0;$i<count($data2);$i++){
		$this->Cell($w[0],6,$header2[$i+1],'LR',0,'R',false);
		$this->Cell($w[1],6,$data2[$i],'LR',0,'L',false);
		if(!$once){
			$this->Cell($w[2],6,$distanseMaster,'1',0,'L',false);
			$once = true;
		}
        $this->Ln();
		}
		
		$this->Cell(array_sum($w)-85,0,'','T');
		// Closing line
		$this->Ln(5);
		
		}	
			
	
}

	// Instanciation of inherited class
	$pdf = new PDF();
	$header = array('Number of points', 'Number of cluster', 'Number of masterpoints', 'Number of outlyers');
	$data = array($pointlist->size(), $clusterlist->size(), $masterlist->size(),$outlierlist->size());
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->ChapterTitle(1,'Infomation');
	$pdf->Information($header,$data);
	
	$pdf->ChapterTitle(2,'Statistics');
	
	$header = array('Points in cluster', 'Max. distance', 'Outlaying Points', 'Average distance');
	$header2 = array('Standard deviation',"x-axis","y-axis","z-axis","average");
	
	$maxDistance = $this->viewmodel->cache->getCacheData(Stat::MAXDISTANCE);
	$outliers = $this->viewmodel->cache->getCacheData(Stat::MASTERPOINTDISTANCE);
	$averageDistance = $this->viewmodel->cache->getCacheData(Stat::AVERAGEDISTANCE);
	$standardDeviation = $this->viewmodel->cache->getCacheData(Stat::STANDARDDEVIATION);
	$masterDistance = $this->viewmodel->cache->getCacheData(Stat::MASTERPOINTDISTANCE); 
	
	for($clusterID=0; $clusterID<$clusterlist->size();$clusterID++){
		$point = $clusterlist->get($clusterID);
		
		$data = array($point->getAdditionalInfo(KMeans::CLUSTERCOUNTNAME), 
							@$maxDistance[$clusterID], 
							@$outliers[$clusterID] . " points > " . $this->viewmodel->settings->getSetting(CachedSettings::OUTLIERCONTROLLDISTANCE),
							@$averageDistance[$clusterID]);
							
		$data2 = array($standardDeviation[$clusterID]["x"],$standardDeviation[$clusterID]["y"],$standardDeviation[$clusterID]["z"],
						round(($standardDeviation[$clusterID]["x"] + $standardDeviation[$clusterID]["y"] + $standardDeviation[$clusterID]["z"])/3, 2));
		$distanseMaster = $masterDistance[$clusterID];
		$pdf->ClusterHeader($clusterID);
		$pdf->ClusterTable($header,$data);
		$pdf->ClusterTable2($header2,$data2, $distanseMaster);
	}
	
	$pdf->SetFont('Times','',12);
	$pdf->Output();

?>
<!--@$standardDeviation[$i]["x"],@$standardDeviation[$i]["y"],@$standardDeviation[$i]["z"],
						round(@($standardDeviation[$i]["x"] + $standardDeviation[$i]["y"] + $standardDeviation[$i]["z"])/3, 2)-->

