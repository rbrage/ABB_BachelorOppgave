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
	$pdf->SetFont('Times','',12);
	
	$pdf->Output();
?>


