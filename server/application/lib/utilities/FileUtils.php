<?php

namespace Cozp\Utils;

class FileUtils
{
    /**
     * Provides an easy interface to the built in mkdir method. It always checks
     * whether the directory to be made already exists. It also determines
     * automatically whether to make the directory recursively.
     * @param  string  $path The path for the directory
     * @param  integer $mode The permission code for the directory
     * @return void
     */
    public static function mkdir($path, $mode = 0777)
    {
        $path = str_ireplace('/', DIRECTORY_SEPARATOR, $path);
        $elems = explode(DIRECTORY_SEPARATOR, $path);

        $recursive = (count($elems) > 0) ? TRUE : FALSE;

        if(!is_dir($path) && !file_exists($path))
        {
            mkdir($path, $mode, $recursive);
        }
    }
}

?>
