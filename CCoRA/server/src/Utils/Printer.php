<?php
namespace Cora\Utils;

class Printer{
    public function terminalLog($message) {
        $STDERR = fopen("php://stderr", "w");
            fwrite($STDERR, "".$message."\n");
            fclose($STDERR);
    }
}


