<?php

/**
 * Description of outTools
 *
 * @author fpiraneo
 */
class outTools {
    /**
     * Generate a thumbs grid
     * first array format has to be:
     * array(
     *          filePath => Full actual file path,
     *          fileName => Actual file name,
     *          SHA1 => SHA1 signature of file
     *          MIME => Argued MIME type
     *          tags => array() - Empty array ready for the tags
     *      )
     * 
     * @param array $fileList List of file to insert into grid
     * @param integer $thumbsPerRow Number of thumbs for each row
     * @param string $cssName Name of CSS of the table
     */
    public function genFileTable($fileList, $thumbsPerRow, $cssName) {
        // Check for first value as array
        if(!is_array($fileList)) {
            return $this->prepareError(__FUNCTION__, 1, 'First parameter must be an array');
        }
        
        // Check for second value as an int
        if(!is_int($thumbsPerRow)) {
            return $this->prepareError(__FUNCTION__, 2, 'Second parameter must be an int');
        }
        
        // Check for third value as a string
        if(!is_string($cssName)) {
            return $this->prepareError(__FUNCTION__, 3, 'Third parameter must be a string');
        }
        
        // Build table
        $outTable = '<table class="' . $cssName . '"><tr>';
        $thumbCounter = 0;
        
        foreach ($fileList as $image) {
            // Out the item
            $outTable .= '<td class="' . $cssName . '">';
            $outTable .= '<a class = "' . $cssName . '" href="[siteAddress]fullImage/'. $image['fileName'] . '">';
            $outTable .= '<img class="' . $cssName . '" src="[siteAddress]thumbnail/' . $image['fileName'] . '" />';
            $outTable .= '<div>' . $image['fileName'] . '</div>';
            $outTable .= '</a>';
            $outTable .= '</td>';
            
            // Increment number of item on the row; if over the item number, generate a new row
            if(++$thumbCounter >= $thumbsPerRow) {
                $thumbCounter = 0;
                $outTable .= '</tr><tr>';
            }
        }
        
        // Correctly close the table
        for(; $thumbCounter < $thumbsPerRow; $thumbCounter++) {
            $outTable .= '<tr>&nbsp;</tr>';
        }
        
        $outTable .= '</table>';
        
        return $outTable;
    }
        
    /**
     * Generate error array
     * @param string $function
     * @param int $code
     * @param string $text
     * @return array
     */
    private function prepareError($function, $code, $text) {
        $firstLevel = array(
            'code' => $code,
            'text' => $text
        );
        
        return array($function => $firstLevel);
    }
}
