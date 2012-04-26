<?php
if(!$glob) 
{
	if(!empty($ld)) $glob = $ld;
}
require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
	public function Header() {
//		$image_file = K_PATH_IMAGES.'pdfheader.jpg';
//		$this->Image($image_file, 10, 10, '', '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
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
//$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A3', true, 'UTF-8', false);

/*
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Adrian Torok');
$pdf->SetTitle('Exercise');
$pdf->SetSubject('Exercise');
$pdf->SetKeywords('Medeeaweb.com');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 061', PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//$pdf->SetHeaderMargin(18);
$pdf->SetHeaderMargin(0);
// remove default header/footer
$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false);
*/

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

$html = include_once('php/exercisepdfbody.php');
//var_dump($html);exit;
//print($html);exit;
//error_reporting(E_ALL);
$pdf->writeHTML($html, true, false, true, false, '');

/*$txt = <<<EOD
Created using www.RehabMyPatient.com
EOD;*/
/*
$pdf->Write($h=0, $txt, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);


// set color for background
$pdf->SetFillColor(255, 255, 255);

// set color for text
$pdf->SetTextColor(0, 0, 0);
*/

// write the second column
//$pdf->writeHTMLCell(0, '', '', '', $txt, 0, 0, 0, true, 'C', true);
$pdf->lastPage();

//$pdf->Output('pdf/exercisepdf.pdf', 'F');
if($glob['pag']=='exercisepdf') $pdf->Output('pdf/exercisepdf.pdf', 'I');
else $pdf->Output('pdf/exercisepdf.pdf', 'F');
