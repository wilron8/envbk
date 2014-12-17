<?php

/**
 * ImageController: processes and streams image / resource files; 
 *   Enforces user security and does not allow non-authenticated visitors to download files.
 *
 * @author kimsreng, Rich@RichieBartlett.com
 */

namespace DocumentManager\Controller;

use DocumentManager\Model\ImgManager;
use DocumentManager\Model\PathManager;
use Common\Mvc\Controller\BaseController;

class ImageController extends BaseController {
//TODO: add methods for thumbnail creation and ~~auto-correction/rotation~~
//TODO: add webMetrics here... check if http_ref is image or webpage; if image, record metrics

    /**
     * defines the full system pathway to file
     *
     * @var string
     */
    protected $fullpath = NULL;

    /**
     * defines the filename
     *
     * @var string
     */
    private $filepath = NULL;

    /**
     * defines the filename (without EXT)
     *
     * @var string
     */
    protected $filename = NULL;

    /**
     * defines the file extension
     *
     * @var string
     */
    protected $fileEXT = NULL;

    /**
     * defines the content data length of file
     *
     * @var int
     */
    protected $fileDataSize = 0;

    /**
     * Paramater for instructing the browser on how to handle image file
     * options: inline / attachment
     * @var string
     */
    protected $fileDownload = "inline";

    /**
     * defines the mime used by the broswer to render the image
     *
     * @var string
     */
    protected $contentType = NULL;


    /**
     * defines the filename of the thumbnail image (without EXT)
     *
     * @var string
     */
    protected $imageThumbName = NULL;
    /**
     * defines image data
     *
     * @var binary Object
     */
    private $imageData = NULL;

    /**
     * defines image Index; Used for editing and tracking image undo/redo
     *
     * @var int
     */
    private $imageIndex = 1;

    /**
     * defines image quality (by percentage) on a scale of 10 to 100
     *
     * @var int
     */
    private $imageQuality = 90;

    /**
     * defines the tolerance of image resizing in either dimension.
     *
     * @var double
     */
    private $imageTolerance = 0.10;

    /**
     * defines image Aspect Ratio (width to height)
     *
     * @var int
     */
    private $imageAspectRatio = 1;

    /**
     * defines image resolution in DPI; Web resolution is typically 72 dpi
     *
     * @var int
     */
    private $imageResolution = 72;

    /**
     * defines the Image width in pixels
     *
     * @var int
     */
    protected $imageWidth = 0;

    /**
     * defines the Image height in pixels
     *
     * @var int
     */
    protected $imageHeight = 0;

    /**
     * defines the maximum Image width in pixels
     *
     * @var int
     */
    protected $imageWidthMax = 1024;

    /**
     * defines the maximum Image height in pixels
     *
     * @var int
     */
    protected $imageHeightMax = 768;

    /**
     * defines the Image thumbnail width in pixels
     *
     * @var int
     */
    protected $imageThumbWidth = 64;

    /**
     * defines the Image thumbnail height in pixels
     *
     * @var int
     */
    protected $imageThumbHeight = 64;

    /**
     * defines the Image EXIF data (general)
     *
     * @var object
     */
    protected $exif_ifd0 = NULL;

    /**
     * defines the Image EXIF data (camera specific)
     *
     * @var object
     */
    protected $exif_exif = NULL;

// ################################################################################ //

    /**
     * Only authenticated users are allowed to download protected images
     * @param \Zend\Mvc\MvcEvent $e
     * @return type
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {


//TODO: add webMetrics here... check if http_ref is image or webpage; if image, record metrics
//TODO: redirect only if IP is banned or is a restricted resource; Otherwise, we should freely display images for ideas and users.


    /**TEMPORARY DISABLED: TO SHOW IMAGE TO GUEST USER
        if (!$this->laIdentity()->hasIdentity()) {
            //TODO: log this attempt for later investigation
            return $this->redirect()->toRoute('user', array('action' => 'signin'));
        }
    **/
        return parent::onDispatch($e);
    }


    /**
     * Action to stream out any image based on resource type
     * 
     */
    public function processAction() {

		list($resource, $id, $type, $fileName) = self::initFileInfo();

        if (file_exists($this->fullpath) && isset($fileName) && strlen($fileName) > 4) {
            self::setFilename($this->filepath);
            self::setFileExtension($this->filepath);
			self::setImageDimensions($this->fullpath);
            self::setContentType($this->filepath);
            self::setMemSize($this->fullpath);
            self::getImageData($this->fullpath);

//var_dump($this);

            self::createFileHeader();

            self::streamFile();
        } else {
            self::imageNotFound();
        }

        exit();
    }

    /**
     * place holder for handling temporary image-processing function
     * 
     */
    public function processTmpAction() {
		exit();
    }

    /**
     * Method to get path for a given resource
     * 
     * @param string $resource
     * @param string $type
     * @return string filePath
     */
    private function getPath($id, $resource, $type) {
        if ($resource == 'idea') {
            return $this->getPathManager()->getIdeaPath($id, $type);
        }
        if ($resource == "project") {
            return $this->getPathManager()->getProjectPath($id, $type);
        }
        if ($resource == "user") {
            return $this->getPathManager()->getUserPath($id, $type);
        }
    }

    public function getTmpPath($resource) {
        if ($resource == 'idea') {
            return $this->getPathManager()->getIdeaTempPath();
        }
        if ($resource == "project") {
            return $this->getPathManager()->getProjectTempPath();
        }
    }

    /**
     * Creates the HTTP header for the image file
     * 
     * @return null
     */
    private function createFileHeader() {

        //if ($this->fileDownload ==="inline") {
        //force broswers to load "new" pic based on new filename
        //$imageName.=\rand()."".\date("U").".".$this->getFileExtension($imageName);
        //}//end if inline
        //if( !headers_sent() ){ 
        header("Pragma: no-cache");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . \gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Cache-Control: private", false); // required for certain browsers
        header("Content-Transfer-Encoding: binary");
        header("Content-Type: " . self::getContentType()); //; charset=UTF-8 
        header("Content-Length: " . $this->fileDataSize);
        header("Content-Disposition: " . $this->fileDownload . "; filename=\"" . $this->filename . "." . $this->fileEXT . "\"");
        header("Content-Description: Envitz FileManager - file stream");
        //}
    }

    /**
     * Returns header and AJAX info for missing file error
     * 
     * @return null
     */
    private function imageNotFound() {
        header("Content-Type: text/plain");
        header("HTTP/1.1 404 Image file not found");
        header("Content-Disposition: attachment; filename=errorMessage");
        $response = "{imageFound:false, success:false, error:\"Original Image not found on server...<BR> Contact the systems administrator for assistance...  \"}";
        header("Content-Length: " . strlen($response));
        echo $response;
        exit();
    }

    /**
     * Returns header and AJAX info for Server memory error
     * 
     * @return null
     */
    private function insufficientServerMemory() {
        //TODO: should send notification to team if this error is executed
        header("Content-Type: text/plain");
        header("HTTP/1.1 400 Server memory at maximum");
        header("Content-Disposition: attachment; filename=errorMessage");
        $response = "{success:false, error:\"Not enough server memory to process this image...<BR><BR>memoryLimit=" . self::getByte(ini_get('memory_limit')) . "\"}";
        header("Content-Length: " . strlen($response));
        echo $response;
        exit();
    }

    /**
     * Retrives basic info about the file
     * 
     * @return array
     */
    private function initFileInfo() {
        $this->filepath = $this->getPathManager()->decodePath($this->params()->fromRoute('path'));
        $fileArray = explode('/', $this->filepath);
		list($resource, $id, $type, $fileName) = $fileArray;
        $this->fullpath = self::getPath($id, $resource, $type) . DIRECTORY_SEPARATOR . $fileName;
		$this->imageThumbName = $this->filepath . DIRECTORY_SEPARATOR . "{$this->filename}_THUMB";


		return $fileArray;
    }

    /**
     * Streams the file out to the browser
     * 
     * @return null
     */
    protected function streamFile() {

        echo $this->imageData;
    }

    /**
     * Extracts the image Data and size
     * 
     * @param string $file
     * @return null
     */
    private function getImageData($file) {
        $type = self::getFileExtension($file);

        @ob_clean();

        switch (strtolower($type)) {
            case "jpg":
            case "jpeg":
                $output = imagecreatefromjpeg($file);
                $output = self::imageRotate($output); //auto-correct Photo orientation
                ob_start(); // start a new output buffer
                imagejpeg($output, NULL, $this->imageQuality);
                $this->imageData = ob_get_contents();
                $this->fileDataSize = ob_get_length();
                //imagedestroy($output);
                ob_end_clean(); // stop this output buffer
                break;
            case "gif":
                $output = imagecreatefromgif($file);
                ob_start(); // start a new output buffer
                imagegif($output, NULL, $this->imageQuality);
                $this->imageData = ob_get_contents();
                $this->fileDataSize = ob_get_length();
                ob_end_clean(); // stop this output buffer
                break;
            case "png":
                $output = imagecreatefrompng($file);
                ob_start(); // start a new output buffer
                imagepng($output);
                $this->imageData = ob_get_contents();
                $this->fileDataSize = ob_get_length();
                ob_end_clean(); // stop this output buffer
                break;
            case "bmp"://convert the image to JPEG out!
                //default:
                $output = imagecreatefromwbmp($file);
                ob_start(); // start a new output buffer
                imagejpeg($output, NULL, $this->imageQuality);
                $this->imageData = ob_get_contents();
                $this->fileDataSize = ob_get_length();
                ob_end_clean(); // stop this output buffer
                break;
        }//end switch imgType
    }

    /**
     * Returns the extention of the filename
     * 
     * @return string file extension
     */
    public function getFileExtension() {
        return ($this->fileEXT);
    }

    /**
     * SET extention of the filename (supports MultiByte string characters)
     * 
     * @param string $file
     */
    private function setFileExtension($file) {
        $this->fileEXT = \mb_substr(strrchr($file, "."), 1); //much faster & safer than pathinfo!
    }

    /**
     * Returns filename
     * 
     * @return string
     */
    public function getFilename() {
        return ($this->filename);
    }

    /**
     * SET filename (without EXT)
     * 
     * @param string $file
     */
    private function setFilename($file) {
        $strStart = strrpos($file, "/") + 1;
        $strEnd = strrpos($file, ".");
        $this->filename = \mb_substr($file, $strStart, ($strEnd - $strStart)); //much faster & safer than pathinfo!
    }

    /**
     * Returns the MIME of the filename
     * 
     * @return string
     */
    public function getContentType() {
        return ($this->contentType);
    }

    /**
     * SET MIME of the filename
     * 
     * @param string $file
     */
    private function setContentType($file) {
        $type = self::getFileExtension($file);
        switch (strtolower($type)) {
            case "jpg":
            case "jpeg":
                $this->contentType = "image/jpeg";
                break;
            case "gif":
                $this->contentType = "image/gif";
                break;
            case "png":
                $this->contentType = "image/png";
                break;
            case "bmp"://convert the image to JPEG out!
            default:
                $this->contentType = "image/jpg";
                break;
        }//end switch imgType
    }

    /**
     * Returns the MIME of the filename
     * 
     * @return string
     */
    protected function getFileSize() {
        return ($this->filesize);
    }

    /**
     * Detect the image width and height
     * 
     * @param string $file
     * @return null
     */
    private function setImageDimensions($file) {
		list($this->imageWidth, $this->imageWidth) = getimagesize($file);
    }

    /**
     * Prepare server memory settings.
     * 
     * @param string $file
     * @return null
     */
    private function setMemSize($file) {
        $memStat = false;
        try {
            ini_set('memory_limit', '256M'); //pre-empt the memory setting <-- applies for only this script.
            $memStat = ImgManager::setMemoryForImage($file); // update Memory config!
        } catch (Exception $e) {
            
        }
        if ($memStat === false) {
            self::insufficientServerMemory();
            exit();
        }  //end if memStat  */
        //echo "memStat=$memStat";
    }

    /**
     * Set the image quality percent (10 - 100)
     * 
     * @param int $imgInt
     */
    protected function setImageQuality($imgInt) {
        $this->imageQuality = min(max($imgInt, 10), 100);
    }

    /**
     * get the image EXIF data
     * 
     * @param string $file
     */
    private function setImageEXIF($file) {
        $this->exif_ifd0 = read_exif_data($file, 'IFD0', 0);
        $this->exif_exif = read_exif_data($file, 'EXIF', 0);
    }

    /**
     * get the image orientation from EXIF data
     * 
     * @return int
     */
    public function getEXIForientation() {
        if ($this->exif_ifd0 === NULL)
            self::setImageEXIF($this->fullpath);

        if (@array_key_exists('Orientation', $this->exif_ifd0)) {
            return ( intval($this->exif_ifd0['Orientation']) );
        }
        return (false);
    }

    /**
     * Calculates the image aspect ratio
     * 
     * @return null
     */
    public function calculateAspect($imageWidth, $imageHeight) {
		if($imageHeight < $imageWidth){ //keep the value > 1.00
			$this->imageAspectRatio = $imageWidth / $imageHeight;
		}else{
			$this->imageAspectRatio = $imageHeight / $imageWidth;
		}//end if size
    }

    /**
     * rotates the image based on EXIF orientation
     * 
     * @param object $imgObj
     * @return object
     */
    private function imageRotate($imgObj) {
        $orientation = self::getEXIForientation();
        switch ($orientation) {
            case 3:
                $imgObj = imagerotate($imgObj, 180, 0);
                break;
            case 6:
                $imgObj = imagerotate($imgObj, -90, 0);
                break;
            case 8:
                $imgObj = imagerotate($imgObj, 90, 0);
                break;
        }
        return ($imgObj);
    }

    /**
     * Creates a smaller thumbnail of the original image and saves to the original file location
     * 
     * @param string $file
     * @return object
     */
    private function saveImageThumbnail($file) {
		$imgObj = NULL;
		$out = NULL;
		$fileDest = NULL;
        $type = self::getFileExtension($file);

		try{
			switch (strtolower($type)) {
				case "jpg":
				case "jpeg":
					$imgObj = imagecreatefromjpeg($file);
					break;
				case "gif":
					$imgObj = imagecreatefromgif($file);
					break;
				case "png":
					$imgObj = imagecreatefrompng($file);
					break;
				case "bmp"://convert the image to JPEG out!
				default:
					$imgObj = imagecreatefromjpeg($file);
					break;
			}//end switch imgType
	
			if ($imgObj) {
				//image object created with no apparent issue
				$out = imagecreatetruecolor($this->imageThumbWidth, $this->imageThumbHeight);
				$status = ImgManager::fastimagecopyresampled($out, $imgObj, 0, 0, 0, 0, $this->imageThumbWidth, $this->imageThumbHeight, $this->imageWidth, $this->imageHeight);
				
				$fileDest = $this->imageThumbName . ".{$this->fileEXT}";
				
				switch (strtolower($type)) {
					case "jpg":
					case "jpeg":
						$imgObj = imagejpeg($out, $fileDest, 100);
						break;
					case "gif":
						$imgObj = imagegif($out, $fileDest);
						break;
					case "png":
						$imgObj = imagepng($out, $fileDest);
						break;
					case "bmp"://convert the image to JPEG out!
					default:
						$imgObj = imagejpeg($out, $fileDest, 100);
						break;
				}//end switch imgType
	
				imagedestroy($imgObj); //clean up memory
	
			//} else {
				//error in image object creation
	
			}
	
		}catch(Exception $e){
			$response =  "{success:false, error: \"resize($out_w,$out_h)(*out) function failure...\n PHP Error: ".$e->getMessage()."\"}";
		}//end try..catch

        return ($out);
    }

    /**
     * 
     * @return \DocumentManager\Model\PathManager
     * @return object
     */
    protected function getPathManager() {
        return $this->get('PathManager');
    }

}
