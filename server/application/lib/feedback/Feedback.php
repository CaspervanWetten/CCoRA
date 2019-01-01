<?php

namespace Cora\Feedback;

use \Ds\Map as Map;
use \Ds\Set as Set;

class Feedback
{
    public $general;
    public $specific;

    public function __construct()
    {
        $this->general = new Set();
        $this->specific = new Map();
    }

    public function addFeedback($code, $element = NULL)
    {
        if(is_null($element)) {
            $this->general->add($code);
        }
        else {
            if(!$this->specific->hasKey($element)) {
                $this->specific->put($element, new Set());
            }
            $this->specific->get($element)->add($code);
        }
    }

    public function getFeedback($element = NULL)
    {
        if(is_null($element)) {
            return $this->general;
        }
        else if($this->specific->hasKey($element)) {
            return $this->specific->get($element);
        }
        else {
            $this->specific->put($element, new Set());
            return $this->getFeedback($element);
        }
    }

    public function removeFeedback($code, $element)
    {
        if($this->specific->hasKey($element)) {
            $f = $this->specific->get($element);
            if($f->contains($code)) {
                $f->remove($code);
            }
        }
    }
}

?>
