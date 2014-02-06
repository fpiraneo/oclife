<?php

/**
 * Check and browse for new files into infeed directory as passed on constructor.
 * Returned results will have following structure:
 * array(
 *          filePath => Full actual file path,
 *          fileName => Actual file name,
 *          SHA1 => SHA1 signature of file
 *          MIME => Argued MIME type
 *          tags => array() - Empty array ready for the tags
 *      )
 *
 * @author fpiraneo
 */
class fileExplorer {
    private $files;
    
    /**
     * Class constructor - Begin a browsing of the infeed directory
     */
    function __construct() {
        $this->files = array();
    }
    
    /**
     * Get result of last browsing
     * @return array Array with all file data
     */
    public function getArray() {
        return $this->files;
    }

    /**
     * Printable version of data contained into result array
     * @return string Printable data
     */
    public function __toString() {
        $result = '';
        
        foreach ($this->files as $fileData) {
            $result .= sprintf("Name: %s, SHA1: %s, Path: %s\n", $fileData['fileName'], $fileData['SHA1'], $fileData['filePath']);
        }
        
        return $result;
    }

    /**
     * Browse the files into given directory
     * @param string $path Path to look into
     * @param bool $recursive Execute recursive browsing
     * @param bool $genSignature Generate SHA1 signature
     */
    public function browseDirectory($path, $recursive, $genSignature) {
        if(substr($path, -1) != '/') {
            $path = $path . '/';
        }
        
        $flags = FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS;
        
        $ul = new FileSystemIterator($path, $flags);
        
        foreach ($ul as $file) {
            if ($file->isFile()) {
                $fileData['filePath'] = $path . $file->getFilename();
                $fileData['fileName'] = $file->getFilename();
                
                // Generate SHA signature
                if($genSignature) {
                    $fileData['SHA1'] = sha1_file($fileData['filePath']);
                }

                $fileData['MIME'] = $this->getMime($fileData['filePath']);
                $fileData['tags'] = array();
                
                $this->files[] = $fileData;
            } elseif ($file->isDir() && $recursive) {
                $this->browseDirectory($path . $file->getFilename(), TRUE, TRUE);
            }
        }
        
        return $this->files;
    }
    
    
    /**
     * Move from incoming dir to data dir
     * @param array $fileList List of files to be moved
     * @param string $dataDirPath Data dir path
     * @return array File list with results
     */
    public function moveInDataDir($fileList, $dataDirPath) {
        // Add trailing slash if needed
        if(substr($dataDirPath, -1) != '/') {
            $dataDirPath .= '/';
        }
        
        // Check if destination is a directory
        if(!is_dir($dataDirPath)) {
            return 1;
        }
        
        $result = array();
        
        // Loop through files and move them only if it's handable and was inserted
        foreach ($fileList as $file) {
            
            if(isset($file['handable']) && isset($file['result']['doInsert']['code'])) {
                
                if($file['handable'] && $file['result']['doInsert']['code'] == 0) {
                    $fileMove = rename($file['filePath'], $dataDirPath . $file['fileName']);

                    $file['result']['moveInDataDir'] = array(
                        'code' => 0,
                        'text' => 'Success'
                    );
                    
                    $result[] = $file;
                } else {
                    if(!$file['handable']) {
                        $file['result']['moveInDataDir'] = array(
                            'code' => 1,
                            'text' => 'Unhandable file'
                        );
                    } else {
                        $file['result']['moveInDataDir'] = array(
                            'code' => 2,
                            'text' => 'File not inserted in DB'
                        );
                    }
                    
                    $result[] = $file;                    
                }
            } else {
                $file['result']['moveInDataDir'] = array(
                    'code' => 3,
                    'text' => 'Control fields not presents'
                );

                $result[] = $file;
            }
        }
        
        return $result;
    }


    /**
     * Argue MIME type of the given file
     * @param type $filePath
     * @return string
     */
    private function getMime($filePath) {
        switch(substr($filePath, -3)) {
            case 'jpg': {
                return 'image/jpeg';
            }
            
            case 'png': {
                return 'image/png';
            }
            
            default : {
                return 'UNKNOWN';
            }
        }
    }
    
}
