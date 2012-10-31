<?php
// module/Editions/src/Editions/Controller/EditionsController.php:
namespace Editions\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Editions\Form\CoverForm;       // <-- Add this import
use DOMPDFModule\View\Model\PdfModel;

class EditionsController extends AbstractActionController
{
	public $isBackgroundPdfCustomized;
	public $backgroundPdfFile;
	public $backgroundPdfCapture;
	
	public function __construct()
	{
		$urlPath = "files/editions/backgroundpdf";
		$diskPath = "/Users/gmichelin/Sites/zf2-tutorial/public/".$urlPath;
		
		if(is_file($diskPath . "/custom.pdf") && is_readable($diskPath . "/custom.pdf")) {
			$this->backgroundPdfFile = $urlPath . "/custom.pdf";
			$this->isBackgroundPdfCustomized = TRUE;
		} else {
			$this->backgroundPdfFile = $urlPath . "/default.pdf";
			$this->isBackgroundPdfCustomized = FALSE;
		}

		if ($this->isBackgroundPdfCustomized) {
			if (is_file($diskPath . "/custom.png") && is_readable($diskPath . "/custom.png")) {
				$this->backgroundPdfCapture = $urlPath . "/custom.png";
			} else {
				// génération de la vignette
				die("génération de la vignette...");
			}
		} else {
			$this->backgroundPdfCapture = $urlPath . "/default.png";
		}
	
	}
	
	public function coverAction()
	{
		return new ViewModel(array("backgroundPdfFile" =>$this->backgroundPdfFile ));
	}
	
	public function indexAction()
	{
		return new ViewModel(
				array(
						"backgroundPdfCapture" => $this->backgroundPdfCapture,
						"isBackgroundPdfCustomized" => $this->isBackgroundPdfCustomized,
						)
				);
	}

	public function testPdfAction()
	{
        $pdf = new PdfModel();
        $pdf->setOption('filename', 'inventaire-contenu'); // Triggers PDF download, automatically appends ".pdf"
        $pdf->setOption('paperSize', 'a4'); // Defaults to "8x11"
        $pdf->setOption('paperOrientation', 'portrait'); // Defaults to "portrait"
        
        return $pdf;
	}
	
	public function testPdf2Action()
	{
		// Désactivation du layout
		$result = new ViewModel();
		$result->setTerminal(true);
		//$result->setVariables(array());
		return $result;
	}
	
	
	
}
?>