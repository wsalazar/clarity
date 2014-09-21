<?php
/**
 * Created by PhpStorm.
 * User: willsalazar
 * Date: 9/21/14
 * Time: 1:05 AM
 */

namespace FileParser\File\Model;


class CSV extends AbstractFile {

    protected $_fieldRow = [];

    public function __construct( $filePath, $mode = Null )
    {
        parent::__construct($filePath, $mode);
    }

    public function populate($param = array())
    {
        $i = 0;
        while ( !$this->_fileHandler->eof() ) {
            $explodedRow = explode(',',$this->_fileHandler->current());
            foreach ( $explodedRow as $key => $row ){
                $this->_fieldRow[$i][$param[$key]] = trim($row);
            }
            $i++;
            $this->_fileHandler->next();
        }
//        var_dump($this->_fieldRow);
        return $this->_fieldRow;
    }

} 