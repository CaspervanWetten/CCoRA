<?php

namespace Cora\Views;

class JsonUserView extends AbstractUserView {
    use JsonViewTrait;

    public function toString(): string {
        return json_encode($this->getUser());
    }
}
