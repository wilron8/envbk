<?php

/**
 * Description of ResourceType
 *
 * @author kimsreng
 */

namespace DocumentManager\Model;

class ResourceType {

    const IDEA_RESOURCE = 'idea';
    const USER_RESOURCE = 'user';
    const PROJECT_RESOURCE = 'project';
    
    const ICON = 'icon';
    const PRESENTATION = 'presentation';

    public static function getAllowedImages() {
        return ['jpg', 'png', 'gif', 'jpeg'];
    }

    public static function getAllowedFiles() {
        return array_merge(self::getAllowedImages(), ['xls', 'xlsx', 'pdf', 'ppt', 'pptx', 'doc', 'docx']);
    }

}
