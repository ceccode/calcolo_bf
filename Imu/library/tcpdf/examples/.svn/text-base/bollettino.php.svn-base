<?php
//============================================================+
// File name   : stampa_bollettino.php
// Begin       : 2008-03-04
// Last Update : 2009-03-27
// 
// Description : Stampa del bollettino di pagamento prestazioni ASL
// 
// Author: Roberto Cantoni
// 
//============================================================+

/**
 * Creates un bollettino postale congli estremi per il pagamento
 * @package tkg
 * @abstract Bollettino postale di fatturatio
 * @author Roberto Cantoni
 * @link http://www.tkg.it
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @since 2009-03-27
 */

require_once('../config/lang/eng.php');
require_once('../tcpdf.php');

//recupero i valori in ingresso
$numero_fattura = $_REQUEST['numero_fattura'];
$data_fattura = $_REQUEST['data_fattura'];
$scadenza_fattura = $_REQUEST['scadenza_fattura'];
$totale_fattura = str_replace('.',',',$_REQUEST['totale_fattura']);
$conto_corrente = $_REQUEST['conto_corrente'];
$intestazione_ccp = $_REQUEST['intestazione_ccp'];
$causale = $_REQUEST['causale'];
$causale2 = $_REQUEST['causale2'];

$barcode = $_REQUEST['barcode'];
// NB! TOGLIERE LA STRINGA SOTTOSTANTE PER RIABILITARE IL BARCODE!
$barcode = '';
$translitterazione = $_REQUEST['translitterazione'];
$distretto = $_REQUEST['distretto'];
$indirizzo_distretto = $_REQUEST['indirizzo_distretto'];
$telefono = $_REQUEST['telefono'];
$fax = $_REQUEST['fax'];
$cap_comune_distretto = $_REQUEST['cap_comune_distretto'];
$comune_distretto = $_REQUEST['comune_distretto'];
$giorni_pagamento = $_REQUEST['giorni_pagamento'];

$costo_imponibile = str_replace('.',',',$_REQUEST['costo_imponibile']);
$costo_iva = str_replace('.',',',$_REQUEST['costo_iva']);
$spesa_postale = str_replace('.',',',$_REQUEST['spesa_postale']);
$costo_esente_iva = str_replace('.',',',$_REQUEST['costo_esente_iva']);
$costo_enpav = str_replace('.',',',$_REQUEST['costo_enpav']);
$spesa_bollo = str_replace('.',',',$_REQUEST['spesa_bollo']);
$costo_escluso_base_imponibile = str_replace('.',',',$_REQUEST['costo_escluso_base_imponibile']);

//$prestazioni = unserialize(stripslashes($_REQUEST['prestazioni_serializzate']));

$prestazioni = unserialize(str_replace("|","'",$_REQUEST['prestazioni_serializzate']));

$ragione_legale = str_replace("|","'",$_REQUEST['ragione_legale']);
$indirizzo_legale = $_REQUEST['indirizzo_legale'];
$comune_legale = $_REQUEST['comune_legale'];
$cap_legale = $_REQUEST['cap_legale'];
$dato_fiscale = $_REQUEST['dato_fiscale'];
$solo_comune_legale = $_REQUEST['solo_comune_legale'];
$codice_cliente = $_REQUEST['codice_cliente'];
$iban = $_REQUEST['iban'];

$annotazione = str_replace("|","'",$_REQUEST['annotazione']);

//$causale .= ' - codice cliente '.$codice_cliente;

/**
echo '<pre>';
print_r($_REQUEST['ragione_legale']);
echo '<br>';
print_r(stripslashes($_REQUEST['ragione_legale']));
echo '<br>';
exit;
*/

// create new PDF document
$pdf = new TCPDF("P", "pt", PDF_PAGE_FORMAT, true); 

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("Invisiblefarm");
$pdf->SetTitle("Stampa bollettino");
$pdf->SetSubject("Stampa bollettino postale pdf");
$pdf->SetKeywords("bollettino, PDF, fatturatio");

// disable header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale("4.2"); 

//set some language-dependent strings
$pdf->setLanguageArray($l); 

//initialize document
$pdf->AliasNbPages();
/*
// add a page
$pdf->AddPage("P");

// ---------------------------------------------------------

//******************* DATI DISTRETTO *******************
$pdf->SetFont("dejavusans", "B", 9);
$pdf->writeHTMLCell(250,10,'30','50',"DIPARTIMENTO DI PREVENZIONE VETERINARIO","",1,0,true,'L');
$pdf->writeHTMLCell(250,10,'30','65',$distretto,"",1,0,true,'L');
$pdf->SetFont("dejavusans", "", 6);
$pdf->writeHTMLCell(250, 0, '30', '80', $indirizzo_distretto , "", 1, 0, true, 'L');
$pdf->writeHTMLCell(250, 0, '30', '90', $cap_comune_distretto.' '.$comune_distretto, "", 1, 0, true, 'L');
$pdf->writeHTMLCell(250, 0, '30', '100', "Tel. ".$telefono." - Fax. ".$fax, "", 1, 0, true, 'L');

//******************* DATI ASL *******************

$pdf->Image('../images/logo_asl.jpg','400','45',160,50 );
$pdf->SetFont("dejavusans", "", 6);
$pdf->writeHTMLCell(525, 0, '30', '90', "Via Pessina, 6 22100 Como", "", 1, 0, true, 'R');
$pdf->writeHTMLCell(525, 0, '30', '100', "P.IVA e C.F. 02356740130", "", 1, 0, true, 'R');

// print a line using Cell()
$style = array("width" => 0.5, "cap" => "square", "join" => "miter", "dash" => "0", "phase" => 10, "color" => array(0, 0, 0));
$style1 = array("width" => 1.5, "cap" => "square", "join" => "miter", "dash" => "0", "phase" => 10, "color" => array(0, 0, 0));

$pdf->Line(30, 125, 550, 125, $style);

$pdf->Rect(30, 240, 530, 416, $style);
$pdf->Line(31, 262.68, 559, 262.68, $style1);

$pdf->Line(88, 240, 88, 602.832, $style);
$pdf->Line(131, 240, 131, 602.832, $style);
$pdf->Line(366, 240, 366, 602.832, $style);
$pdf->Line(408, 240, 408, 602.832, $style);
$pdf->Line(450, 240, 450, 602.832, $style);
$pdf->Line(487, 240, 487, 602.832, $style);
$pdf->Line(538, 240, 538, 602.832, $style);

$indice=0;
$quota = 291;
while ($indice<12)
{
  $pdf->Line(30, $quota, 560, $quota, $style);
  $indice = $indice + 1;
  $quota = $quota + 28.346;
}


$indice=0;
$quota = 675;
while ($indice<7)
{
  $pdf->Line(350, $quota, 560, $quota, $style);
  $indice = $indice + 1;
  $quota = $quota + 14;
}

$pdf->SetFont("dejavusans", "B", 11);
$pdf->writeHTMLCell(100, 10, '30', '130', "FATTURA N.", "", 1, 0, true, 'C');
$pdf->writeHTMLCell(100, 10, '30', '150', $numero_fattura, "LRTB", 1, 0, true, 'C');

$pdf->writeHTMLCell(100, 10, '140', '130', "Data fattura", "", 1, 0, true, 'C');
$pdf->writeHTMLCell(100, 10, '140', '150', $data_fattura, "LRTB", 1, 0, true, 'C');

$pdf->SetFont("dejavusans", "B", 9);
$pdf->writeHTMLCell(150, 10, '30', '223', "Scadenza fattura", "", 1, 0, true, 'L');
$pdf->SetFont("dejavusans", "B", 11);
$pdf->writeHTMLCell(150, 10, '120', '221', $scadenza_fattura, "", 1, 0, true, 'L');

// modalità di pagamento
$pdf->SetFont("dejavusans", "", 9);
$pdf->writeHTMLCell(200, 10, '30', '605', "MODALITA' DI PAGAMENTO:", "", 1, 0, true, 'L');
$pdf->SetFont("dejavusans", "UB", 10);
$pdf->writeHTMLCell(200, 10, '160', '604', $giorni_pagamento." GIORNI DATA FATTURA", "", 1, 0, true, 'L');
$pdf->SetFont("dejavusans", "", 8);
$pdf->writeHTMLCell(520, 10, '30', '625', "a mezzo di versamento su Conto Corrente Postale n. ".$conto_corrente." o tramite bonifico bancario, IBAN ". $iban ." intestato a ".$intestazione_ccp, "", 1, 0, true, 'L');

// intestazione colonne
$pdf->SetFont("dejavusans", "B", 6);
$pdf->writeHTMLCell(60, 10, '26', '240', "Cod. Prestazione Nota", "", 1, 0, true, 'C');
$pdf->writeHTMLCell(40, 10, '88', '240', "Data Prestaz.", "", 1, 0, true, 'C');
$pdf->writeHTMLCell(100, 10, '380', '244', "Imp. Unit.*", "", 1, 0, true, 'C');
$pdf->writeHTMLCell(100, 10, '462', '240', "IMPONIBILE", "", 1, 0, true, 'C');
$pdf->writeHTMLCell(70, 10, '515', '240', "Cod.", "", 1, 0, true, 'C');
$pdf->writeHTMLCell(70, 10, '515', '249', "IVA", "", 1, 0, true, 'C');
$pdf->SetFont("dejavusans", "", 4.3);
$pdf->writeHTMLCell(100, 10, '462', '249', "(quota min d'accesso)", "", 1, 0, true, 'C');
$pdf->SetFont("dejavusans", "B", 8);
$pdf->writeHTMLCell(200, 10, '135', '243', "DESCRIZIONE PRESTAZIONE", "", 1, 0, true, 'l');
$pdf->writeHTMLCell(50, 10, '363', '243', "U.M.", "", 1, 0, true, 'C');
$pdf->writeHTMLCell(50, 10, '443', '243', "Q.TA'", "", 1, 0, true, 'C');




$quota = 269;
// inserimento valori nelle colonne
foreach ($prestazioni as $prestazione)
{
	$pdf->SetFont("dejavusans", "", 7);
	$pdf->writeHTMLCell(60, 10, '29', $quota, $prestazione['codice_nota'], "", 1, 0, true, 'C');
	$pdf->writeHTMLCell(50, 10, '85', $quota, substr($prestazione['data'],8,2).'/'.substr($prestazione['data'],5,2).'/'.substr($prestazione['data'],0,4), "", 1, 0, true, 'C');
	$pdf->writeHTMLCell(50, 10, '362', $quota, $prestazione['unita_misura'], "", 1, 0, true, 'C');
	$pdf->writeHTMLCell(100, 10, '379', $quota,  number_format($prestazione['costo_netto_totale']/$prestazione['quantita'], 2, ',', ''), "", 1, 0, true, 'C');
	$pdf->writeHTMLCell(100, 10, '418', $quota, $prestazione['quantita'], "", 1, 0, true, 'C');
	$pdf->writeHTMLCell(100, 10, '462', $quota, $prestazione['costo_netto_totale'], "", 1, 0, true, 'C');
	$pdf->writeHTMLCell(70, 10, '515', $quota, $prestazione['codice_iva'], "", 1, 0, true, 'C');
	$pdf->SetFont("dejavusans", "", 6);
	$pdf->writeHTMLCell(230, 10, '132', $quota-6, $prestazione['descrizione_breve'], "", 1, 0, true, 'L');
	 $quota = $quota + 28.346;
}
//******************* STAMPA PARZIALI E TOTALI *******************

$pdf->SetFont("dejavusans", "B", 8);

// etichette dei valori parziali
$pdf->writeHTMLCell(150, 10, '345', '660', "Imponibile 20%", "", 1, 0, true, 'L');
$pdf->writeHTMLCell(150, 10, '345', '674', "Iva 20%", "", 1, 0, true, 'L');
$pdf->writeHTMLCell(150, 10, '345', '688', "Esenti da Iva", "", 1, 0, true, 'L');
$pdf->writeHTMLCell(150, 10, '345', '702', "Esclusi dalla Base Imponibili", "", 1, 0, true, 'L');
$pdf->writeHTMLCell(150, 10, '345', '716', "Bollo (**)", "", 1, 0, true, 'L');
$pdf->writeHTMLCell(150, 10, '345', '730', "Spese Spedizione Fattura", "", 1, 0, true, 'L');
$pdf->writeHTMLCell(150, 10, '345', '744', "Arrotondamento", "", 1, 0, true, 'L');

// valori parziali
$pdf->writeHTMLCell(218, 10, '345', '660', $costo_imponibile, "", 1, 0, true, 'R');
$pdf->writeHTMLCell(218, 10, '345', '674', $costo_iva, "", 1, 0, true, 'R');
$pdf->writeHTMLCell(218, 10, '345', '688', $costo_esente_iva, "", 1, 0, true, 'R');
$pdf->writeHTMLCell(218, 10, '345', '702', $costo_escluso_base_imponibile, "", 1, 0, true, 'R');
$pdf->writeHTMLCell(218, 10, '345', '716', $spesa_bollo, "", 1, 0, true, 'R');
$pdf->writeHTMLCell(218, 10, '345', '730', $spesa_postale, "", 1, 0, true, 'R');
$pdf->writeHTMLCell(218, 10, '345', '744', "0,00", "", 1, 0, true, 'R');

$pdf->SetFont("dejavusans", "B", 12);
$pdf->writeHTMLCell(150, 10, '345', '765', "TOTALE FATTURA", "", 1, 0, true, 'L');
$pdf->writeHTMLCell(218, 10, '345', '765', "€ ".$totale_fattura, "", 1, 0, true, 'R');

//******************************************************************

//******************* ANNOTAZIONI *******************
$pdf->SetFont("dejavusans", "B", 6);
$pdf->writeHTMLCell(60, 10, '30', '654', "ANNOTAZIONI", "", 1, 0, true, 'L');
$pdf->SetFont("dejavusans", "", 6);
$pdf->writeHTMLCell(200, 10, '30', '664', $annotazione, "", 1, 0, true, 'L');

//******************* ENPAV *******************
$pdf->SetFont("dejavusans", "B", 6);
$pdf->writeHTMLCell(80, 10, '262', '656', "2% Contributo ENPAV sul totale Prestazioni soggette", "", 1, 0, true, 'L');
$pdf->SetFont("dejavusans", "B", 7);
$pdf->writeHTMLCell(80, 10, '262', '673', $costo_enpav, "", 1, 0, true, 'R');

//******************* CODICI IVA *******************
$pdf->SetFont("dejavusans", "B", 8);
$pdf->writeHTMLCell(80, 10, '30', '705', "CODICI IVA", "", 1, 0, true, 'L');
$pdf->SetFont("dejavusans", "", 6.5);
$pdf->writeHTMLCell(250, 10, '30', '715', "PRESTAZIONI RESE SU RICHIESTA in carenza di liberi professionisti", "", 1, 0, true, 'L');
$pdf->writeHTMLCell(250, 10, '30', '721', "cod. 20% - soggetti ad IVA", "", 1, 0, true, 'L');
$pdf->writeHTMLCell(250, 10, '30', '729', "PRESTAZIONI RESE D'UFFICIO", "", 1, 0, true, 'L');
$pdf->writeHTMLCell(250, 10, '30', '735', "cod. 1 - Esenti art. 10 p.18 D.L. 633/72", "", 1, 0, true, 'L');
$pdf->writeHTMLCell(250, 10, '30', '741', "cod. 2 - Esclusi dalla Base Imponibile", "", 1, 0, true, 'L');

//******************* RICHIAMI *******************
$pdf->SetFont("dejavusans", "", 6.5);
$pdf->writeHTMLCell(300, 10, '30', '748', "*importo al netto del contributo ENPAV 2% e dell'arrotondamento dove espressamente", "", 1, 0, true, 'L');
$pdf->writeHTMLCell(250, 10, '30', '753', "previsti per legge", "", 1, 0, true, 'L');
$pdf->writeHTMLCell(300, 10, '30', '760', "**Imposta di bollo assolta in modo virtuale - Aut. n. 1051/98 del 23/02/1998", "", 1, 0, true, 'L');

$pdf->writeHTMLCell(350, 10, '30', '767', "PD = Tutte le prestazioni rese in Pronta Disponibilità sono soggette a maggiorazione del 40%", "", 1, 0, true, 'L');

//******************* DATI SEDE LEGALE *******************

$style = array("width" => 0.5, "cap" => "square", "join" => "miter", "dash" => "0", "phase" => 10, "color" => array(0, 0, 0));
$pdf->Rect(320, 135, 230, 90, $style);
$pdf->SetFont("dejavusans", "B", 9);
$pdf->writeHTMLCell(230, 0, '320', '140', "Spett.le ".$ragione_legale, "", 1, 0, true, 'L');
$pdf->SetFont("dejavusans", "", 8);
$pdf->writeHTMLCell(230, 0, '320', '160', $indirizzo_legale, "", 1, 0, true, 'L');
$pdf->writeHTMLCell(230, 0, '320', '170', $comune_legale, "", 1, 0, true, 'L');
$pdf->writeHTMLCell(230, 0, '320', '185', "P.IVA e C.F. ".$dato_fiscale, "", 1, 0, true, 'L');
*/
// add a page
$pdf->AddPage("L");

// ---------------------------------------------------------

// set font
//$pdf->SetFont("ocr-b", "", 15);

// create some HTML content
$htmlcontent = "<img src=\"../images/bollettino_postale.jpg\" alt=\"test alt attribute\" border=\"0\" />";
// output the HTML content
$pdf->writeHTML($htmlcontent, true, 0, true, 0);

//totale della fattura a dx
$pdf->writeHTMLCell(200,0,157,22,$totale_fattura,'LRTB',1,0,true,'R');
//totale della fattura a sx
$pdf->writeHTMLCell(200,0,620,22,$totale_fattura,'LRTB',1,0,true,'R');

//conto corrente dx
$pdf->writeHTMLCell(200,0,115,22,$conto_corrente,'LRTB',1,0,true,'L');
//conto corrente sx
$pdf->writeHTMLCell(200,0,490,22,$conto_corrente,'LRTB',1,0,true,'L');

$pdf->SetFont("dejavusans", "", 10);
//conto corrente dx
$pdf->writeHTMLCell(300,0,105,39,$translitterazione,'LRTB',1,0,true,'L');
//conto corrente sx
$pdf->writeHTMLCell(300,0,520,39,$translitterazione,'LRTB',1,0,true,'L');

$pdf->SetFont("dejavusans", "", 10);

//intestazione sx
$pdf->writeHTMLCell(330, 0, '30', '68', $intestazione_ccp, "LRTB", 1, 0, true, 'L');
//intestazione dx
$pdf->writeHTMLCell(430, 0, '390', '68', $intestazione_ccp, "LRTB", 1, 0, true, 'L');

//casuale sx
$pdf->writeHTMLCell(330, 0, '30', '105', $causale, "LRTB", 1, 0, true, 'L');
//casuale sx sotto
$pdf->writeHTMLCell(330, 0, '30', '120', $causale2, "LRTB", 1, 0, true, 'L');
//casuale dx
$pdf->writeHTMLCell(290, 0, '390', '105', $causale, "LRTB", 1, 0, true, 'L');
//casuale dx sotto
$pdf->writeHTMLCell(290, 0, '390', '120', $causale2, "LRTB", 1, 0, true, 'L');

//ragione dx
$pdf->writeHTMLCell(160, 0, '30', '147', $ragione_legale, "LRTB", 1, 0, true, 'L');

//ragione sx
$pdf->writeHTMLCell(330, 0, '530', '146', $ragione_legale, "LRTB", 1, 0, true, 'L');

//indirizzo dx
$pdf->writeHTMLCell(160, 0, '70', '179', $indirizzo_legale, "LRTB", 1, 0, true, 'L');

//indirizzo sx
$pdf->writeHTMLCell(330, 0, '530', '184', $indirizzo_legale, "LRTB", 1, 0, true, 'L');


//cap dx
$pdf->writeHTMLCell(160, 0, '70', '196', $cap_legale, "LRTB", 1, 0, true, 'L');

//cap sx
$pdf->writeHTMLCell(330, 0, '530', '209', $cap_legale, "LRTB", 1, 0, true, 'L');

//comune dx
$pdf->writeHTMLCell(160, 0, '70', '214', $solo_comune_legale, "LRTB", 1, 0, true, 'L');

//comune sx
$pdf->writeHTMLCell(330, 0, '600', '209', $solo_comune_legale, "LRTB", 1, 0, true, 'L');

$style = array(
	"position" => "S",
	"border" => false,
	"padding" => 4,
	"fgcolor" => array(0,0,0),
	"bgcolor" => false, //array(255,255,255),
	"text" => false,
	"font" => "dejavusans",
	"fontsize" => 8,
	"stretchtext" => 4
);

// CODE 39
$pdf->write1DBarcode($barcode, "C39", '600', '103', 220, 36, 0.4, $style, 'N');
//$pdf->write1DBarcode();
//$pdf->write1DBarcode




// reset pointer to the last page
$pdf->lastPage();



// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Bollettino_'.str_replace('/','_',$causale2).'.pdf', "I", "I");

//============================================================+
// END OF FILE                                                 
//============================================================+
?>

