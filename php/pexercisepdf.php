<?php
if(!$glob) 
{
	if(!empty($ld)) $glob = $ld;
}

require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
	public function Header() {
	}
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-22);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
		 $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }}


$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetMargins(18, 18, -1, true);
$pdf->SetFont('helvetica', '', 8);
$pdf->setPrintFooter(true);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);

$tagvs = array('h1' => array(0 => array('h' => 0,'n' => 0),1 => array('h' => 0,'n' => 0)),'h2' => array(0 => array('h' => 0,'n' => 0),1 => array('h' => 1,'n' => 2)));
$pdf->setHtmlVSpace($tagvs);

$pdf->AddPage();

$html = include_once('php/pexercisepdfbody.php');
//var_dump($html);exit;
$pdf->writeHTML($html, true, false, true, false, '');

$pdf->lastPage();

if($glob['pag']=='pexercisepdf') $pdf->Output('pdf/exercisepdf.pdf', 'I');
else $pdf->Output('pdf/exercisepdf.pdf', 'F');
