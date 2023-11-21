<?php
namespace Cora\Utils;

class PdoDebug{
    public function pdoDebugStrParams($stmt) {
        ob_start();
        $stmt->debugDumpParams();
        $r = ob_get_contents();
        ob_end_clean();
        return $r;
      }
}


