<?php
/**
 * Created by PhpStorm.
 * User: willsalazar
 * Date: 9/21/14
 * Time: 1:14 AM
 */

namespace FileParser\File\Model;

use SplFileObject;


abstract class AbstractFile {

    protected $_file;

    protected $_fileHandler;

    public function __construct( $filePath, $mode = Null )
    {
        $this->_file = $filePath;
        if ( is_null( $mode ) ) {
            $mode = 'r';
        }
        $this->_fileHandler = new SplFileObject($filePath, $mode);
    }

} 