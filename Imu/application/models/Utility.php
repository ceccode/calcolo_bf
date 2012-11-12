<?php
/**
 * Description of Utility
 * in questa classe sono presenti delle funzioni di
 * utilizzo generale
 *
 * @author francesco
 */
class Utility {
	public static function formattaDataOra($data) {
		$ret = null;
		$data = explode ( "-", $data );
		$data_new = explode ( " ", $data [2] );
		$time = mktime ( 0, 0, 0, $data [1], $data_new [0], $data [0] );
		$ora = $data_new [1];
		$ret = date ( "d/m/Y", $time );
		$ret .= ", " . $ora;
		return $ret;
	}
	
	/**
	 * formatta la data dal formato dd/mm/yy nel formato yy-mm-dd (MYSQL) per il
	 * salvataggio nel db
	 */
	public static function formattaDataPerSalvataggioDb($data) {
		$dataNuova = null;
		$data = explode ( "/", $data );
		if (count ( $data ) == 3) { // se ho la data nel formato corretto
			$giorno = $data [0];
			$mese = $data [1];
			$anno = $data [2];
			$dataNuova = date ( "Y-m-d", mktime ( 0, 0, 0, $mese, $giorno, $anno ) );
		}
		
		return $dataNuova;
	}
	/**
	 * formatta la data dal formato yy-mm-dd (MYSQL) al formato dd/mm/yy per la
	 * visualizzazione
	 */
	public static function formattaDataPerVisualizzazione($data) {
		$dataNuova = null;
		$data = explode ( "-", $data );
		if (count ( $data ) == 3) { // se ho la data nel formato corretto
			$anno = $data [0];
			$mese = $data [1];
			$giorno = $data [2];
			$dataNuova = date ( "d/m/Y", mktime ( 0, 0, 0, $mese, $giorno, $anno ) );
		}
		return $dataNuova;
	}
	
	/**
	 * converte il numero da formato con , a formato con .
	 *
	 *
	 * @param
	 *        	float numero
	 */
	public static function formattaNumeroPerSalvataggioDb($numero) {
		$numeroNuovo = floatVal ( $numero );
		return str_replace ( ",", ".", $numero );
	}
	
	/**
	 * converte il nomero da formato con .
	 * a formato con ,
	 * 
	 * @param float $numero        	
	 */
	public static function formattaNumeroPerVisualizzazione($numero) {
		return str_replace ( ".", ",", $numero );
	}
	
	/**
	 * formatta il numero dal formato con la , al formato per la stampa
	 * numero standard italiano con separatore , per le migliaia e .
	 * per i decimali
	 *
	 * @param
	 *        	numero numero da stampare
	 *        	@patam numeroGiaFormatoPunto true se il numero ha il . normale
	 *        	invece della virgola come separatore
	 */
	public static function formattaNumeroPerStampa($numero, $numeroGiaFormatoPunto = false) {
		if ($numeroGiaFormatoPunto)
			return number_format ( (float) $numero , 2, ",", "." );
		else	
			return number_format ( Utility::formattaNumeroPerSalvataggioDb ( $numero ), 2, ",", "." );
	}
}

?>
