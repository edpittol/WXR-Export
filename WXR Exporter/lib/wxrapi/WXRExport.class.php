<?php

require_once 'WXRDocument.class.php';

class WXRExport {

	/**
	 * Path of the document that will be saved.
	 * 
	 * @var string
	 */
	private $file;

	/**
	 * The size to split xml in files. 0 to create only one file.
	 * 
	 * @var string
	 */
	private $file_size;
	
	/**
	 * The documents created.
	 * 
	 * @var array
	 */
	private $documents;

	/**
	 * WXR construction. Initialize a WXR object.
	 * - Define file;
	 * - Create Document;
	 * - Create the rss and channel element.
	 * 
	 * @param string $file The path of file. If not set, create a file with the atual time.
	 * @param int $file_size The file size (in KB) to split. If not set, create one file with all content.
	 */
	function __construct($file = '', $file_size = 0) {
		$this->file = $file ? $file : 'wxr_export.zip';
		$this->file_size = $file_size * 1024; // transform to bytes
		
		$this->documents[0] = new WXRDocument();
		
		// verify if file_size is bigger than xml header
		if( $this->file_size > 0 && $this->file_size < $this->currentDoc()->getDocSize() ) {
			throw new Exception('File size smaller than the XML default values');
		}
	}
	
	function addItem( $item, $metakeys ) {
		$element = $this->currentDoc()->addItem( $item, $metakeys );
		$this->insertElement( $element );
	}
	
	function insertElement( $element ) {
		$element_size = strlen( $element->ownerDocument->saveXML( $element ) );
		$new_size = $this->getDocSize() + $element_size;
		if($new_size > $this->file_size) {
			$this->documents[] = new WXRDocument();
		}
		$this->currentDoc()->insertChannelChild( $element );
	}
	
	function getDocSize() {
		return strlen( $this->currentDoc()->saveXML() );
	}
	
	/**
	 * Retrieves the current manipulate document.
	 * 
	 * @return WXRDocument The current document.
	 */
	function currentDoc() {
		return end($this->documents);
	}
	
	function create_zip() {
		$destination = sys_get_temp_dir() . "wrxexport_" . time() . ".zip";
		//$destination = "/var/www/wxrapi/exporter/zip/wrxexport_" . time() . ".zip";
		$zip = new ZipArchive();
		
		if( $zip->open( $destination, ZIPARCHIVE::OVERWRITE ) !== true ) {
			throw new Exception('Error to create zip file.');
		}
		
		foreach($this->documents as $i =>$doc) {
			$zip->addFromString( "wxr_" . str_pad( ++$i, 3, 0, STR_PAD_LEFT ) . ".xml", $doc->saveXML() );
		}

		$zip->addFromString( "log.txt", ob_get_clean() );
		
		$zip->close();
		
		// TODO implement file size
		header('Content-type: application/zip');
		header('Content-Disposition: attachment; filename="' . $this->file . '"');
		
		echo file_get_contents( $destination );
		exit;		
	}

}
