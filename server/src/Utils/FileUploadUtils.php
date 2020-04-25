<?php

namespace Cora\Utils;

class FileUploadUtils
{
    /**
     * Get the error message corresponding to an UPLOAD_ERR_xxx code
     * @param  int $errorCode The UPLOAD_ERR_xxx code
     * @return string         The error message
     */
    public static function getErrorMessage(int $errorCode)
    {
        $errors = array(
            UPLOAD_ERR_OK           => "Success.",
            UPLOAD_ERR_INI_SIZE     => "The uploaded file exceeds the max allowed file size.",
            UPLOAD_ERR_FORM_SIZE    => "The uploaded file exceeds the max allowed size specified in the form.",
            UPLOAD_ERR_PARTIAL      => "The file was only partially uploaded.",
            UPLOAD_ERR_NO_FILE      => "No file was uploaded.",
            UPLOAD_ERR_NO_TMP_DIR   => "Missing temporary folder.",
            UPLOAD_ERR_CANT_WRITE   => "Failed to write to disk.",
            UPLOAD_ERR_EXTENSION    => "Upload stopped by extension."
        );
        if(array_key_exists($errorCode, $errors))
            return $errors[$errorCode];
        return "Unknown error";
    }
}
