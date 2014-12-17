<?php

/**
 * DocumentManager - stores & manages documents uploaded from users to AWS global storage solutions.
 *   Enforces user security and does not allow non-authenticated visitors to download files.
 *
 * @author kimsreng, Rich@RichieBartlett.com
 */

namespace DocumentManager\Controller;

use DocumentManager\Model\ImgManager;
use DocumentManager\Model\PathManager;
use Zend\Mvc\Controller\AbstractActionController;

class DocumentManagerController extends AbstractActionController {

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

// ################################################################################ //

    /**
     * Only authenticated users are allowed to download protected FILES
     * @param \Zend\Mvc\MvcEvent $e
     * @return type
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {

//TODO: add webMetrics here... check if http_ref is FILE or webpage; if image, record metrics
        //TODO: redirect only if IP is banned or is a restricted resource; Otherwise, we should freely display images for ideas and users.
        if (!$this->laIdentity()->hasIdentity()) {
            //TODO: log this attempt for later investigation
            return $this->redirect()->toRoute('user', array('action' => 'signin'));
        }
        return parent::onDispatch($e);
    }

    /**
     * Action to stream out any image based on resource type
     * 
     */
    public function processAction() {

        $this->filepath = PathManager::decodePath($this->params()->fromRoute('path'));
        list($resource, $type, $fileName) = explode('/', $this->filepath);
        $this->fullpath = self::getPath($resource, $type) . $fileName;

        if (file_exists($this->fullpath)) {
            self::setFilename($this->filepath);
            self::setFileExtension($this->filepath);
            self::setContentType($this->filepath);
            self::setMemSize($this->fullpath);
            self::getFileData($this->fullpath);

//var_dump($this);

            self::createFileHeader();

            self::streamFile();
        } else {
            self::imageNotFound();
        }

        exit();
    }

    /**
     * Method to get path for a given resource
     * 
     * @param string $resource
     * @param string $type
     * @return string filePath
     */
    private function getPath($resource, $type) {
        if ($resource == 'idea') {
            return PathManager::ideaFilePath($type);
        }
        if ($resource == "project") {
            return PathManager::projectFilePath($type);
        }
        if ($resource == "user") {
            return PathManager::userFilePath($type);
        }
    }

    /**
     * Creates the HTTP header for the DOC file
     * 
     * @param bool $dl
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
     */
    private function imageNotFound() {
        header("Content-Type: text/plain");
        header("HTTP/1.1 404 DOC file not found");
        header("Content-Disposition: attachment; filename=errorMessage");
        $response = "{imageFound:false, success:false, error:\"Original Image not found on server...<BR> Contact the systems administrator for assistance...  \"}";
        header("Content-Length: " . strlen($response));
        echo $response;
        exit();
    }

    /**
     * Returns header and AJAX info for Server memory error
     * 
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
     * Returns the extention of the filename
     * 
     */
    protected function streamFile() {

        echo $this->imageData;
    }

    /**
     * Extracts the FILE Data and size
     * 
     * @param string $file
     */
    private function getFileData($file) {
        $type = self::getFileExtension($file);

        ob_clean();

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
        }//end switch imgType
    }

    /**
     * Returns the extention of the filename
     * 
     * @return file extension
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
            default:
                $this->contentType = "text/plain";
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

}
