<?php

namespace Cora\Domain\Systems\Tokens;

use Ds\Hashable;

interface TokenCountInterface extends Hashable {
    public function add(TokenCountInterface $rhs): TokenCountInterface;
    public function subtract(TokenCountInterface $rhs): TokenCountInterface;
    public function greater(TokenCountInterface $rhs): bool;
    public function geq(TokenCountInterface $rhs): bool;
}
