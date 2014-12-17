<?php

/**
 * Log an error to a log file on server
 *
 * @author kimsreng
 */

namespace ErrorManager\Service;

class FileLog extends AbstractLog {

    public function logException($e) {
        if (!is_dir(ROOT_PATH . '/data/log')) {
            mkdir(ROOT_PATH . '/data/log', 774);
        }
        $handle = fopen(ROOT_PATH . '/data/log/application.log', 'a');
        fwrite($handle, '--------------------------**************--------------------------------' . PHP_EOL);
        fwrite($handle, $this->getExceptionContent($e));
        fclose($handle);
    }

}
