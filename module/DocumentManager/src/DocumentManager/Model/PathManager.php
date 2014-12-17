<?php

/**
 * Class to provide file path for resources
 *
 * @author kimsreng, Rich@RichieBartlett.com
 */

namespace DocumentManager\Model;

use DocumentManager\Model\DbEntity\DocMetaServer;
use DocumentManager\Model\ResourceType as Resource;

class PathManager {

    private static $_instance = NULL;

    /**
     * File path on server
     * 
     * @var string 
     */
    private $rootPath = '';
    private $server = ''; //TODO: need to compare this value with $_SERVER['HTTP_HOST'] to ensure that data is stored on local server. Otherwise, DocMgr/ImgMgr must retrieve file from remote server!

    public static function getInstance(DocMetaServer $server) {
        if (self::$_instance === NULL) {
            self::$_instance = new PathManager();
            self::$_instance->rootPath = $server->docMetaSvr_locPath;
            self::$_instance->server = $server->docMetaSvr_locServer;
        }
        return self::$_instance;
    }

    /**
     * 
     * @param type $path
     * @return type
     */
    public function encodePath($path) {
        return \base64_encode($path);
    }

    /**
     * 
     * @param type $code
     * @return type
     */
    public function decodePath($code) {
        return \base64_decode($code);
    }

    /**
     * 
     * @param type $projectId
     * @param type $resourceType
     * @return string
     */
    public function getProjectPath($projectId, $resourceType = '') {
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'projects';
        if (!is_dir($path)) {
            mkdir($path, 0744);
        }
        $projectPath = $path . DIRECTORY_SEPARATOR . $projectId;
        if (!is_dir($projectPath)) {
            mkdir($projectPath, 0744);
        }
        if ($resourceType == '') {
            return $projectPath;
        }
        $resoucePath = $projectPath . DIRECTORY_SEPARATOR . $resourceType;
        if (!is_dir($resoucePath)) {
            mkdir($resoucePath, 0744);
        }
        return $resoucePath;
    }

    public function getProjectRoutePath($projectId, $resource, $file) {
        return "project/" . $projectId . '/' . $resource . '/' . $file;
    }

    public function buildProjectRoutePath($projectId, $resource, $file) {
        return $this->encodePath($this->getProjectRoutePath($projectId, $resource, $file));
    }

    public function getProjectTempPath() {
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'projects' . DIRECTORY_SEPARATOR . 'temp';
        if (!is_dir($path)) {
            mkdir($path, 0744);
        }
        return $path;
    }

    /**
     * 
     * @param type $ideaId
     * @param type $resourceType
     * @return string
     */
    public function getIdeaPath($ideaId, $resourceType = '') {
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'ideas' ;
        if (!is_dir($path)) {
            mkdir($path, 0744);
        }
        $ideaPath = $path. DIRECTORY_SEPARATOR . $ideaId;
        if (!is_dir($ideaPath)) {
            mkdir($ideaPath, 0744);
        }
        if ($resourceType == '') {
            return $ideaPath;
        }
        $resoucePath = $ideaPath . DIRECTORY_SEPARATOR . $resourceType;
        if (!is_dir($resoucePath)) {
            mkdir($resoucePath, 0744);
        }
        return $resoucePath;
    }

    public function getIdeaRoutePath($ideaId, $resource, $file) {
        return "idea/" . $ideaId . '/' . $resource . '/' . $file;
    }

    public function buildIdeaRoutePath($ideaId, $resource, $file) {
        return $this->encodePath($this->getIdeaRoutePath($ideaId, $resource, $file));
    }

    public function getIdeaTempPath() {
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'ideas' . DIRECTORY_SEPARATOR . 'temp'; //echo $path;
        if (!is_dir($path)) {
            mkdir($path);
        }
        return $path;
    }

    /**
     * 
     * @param type $userId
     * @param type $resourceType
     * @return string
     */
    public function getUserPath($userId, $resourceType = '') {
        $path = $this->rootPath . DIRECTORY_SEPARATOR . 'users';
        if (!is_dir($path)) {
            mkdir($path, 0744);
        }
        $userPath = $path . DIRECTORY_SEPARATOR . $userId;
        if (!is_dir($userPath)) {
            mkdir($userPath, 0744);
        }
        if ($resourceType == '') {
            return $userPath;
        }
        $resoucePath = $userPath . DIRECTORY_SEPARATOR . $resourceType;
        if (!is_dir($resoucePath)) {
            mkdir($resoucePath, 0744);
        }
        return $resoucePath;
    }

    public function getUserRoutePath($userId, $resource, $file) {
        return "user/" . $userId . '/' . $resource . '/' . $file;
    }

    public function buildUserRoutePath($userId, $resource, $file) {
        return $this->encodePath($this->getUserRoutePath($userId, $resource, $file));
    }

    public function buildTmpPath($resource, $file) {
        if ($resource == Resource::IDEA_RESOURCE) {
            return $this->encodePath(Resource::IDEA_RESOURCE . '/' . $file);
        } elseif ($resource == Resource::PROJECT_RESOURCE) {
            return $this->encodePath(Resource::PROJECT_RESOURCE . '/' . $file);
        } else {
            return '';
        }
    }

}
