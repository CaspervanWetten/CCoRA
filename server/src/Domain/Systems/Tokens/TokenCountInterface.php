<?php

namespace Cora\Domain\Systems\Tokens;

interface TokenCountInterface {
    public function add(TokenCountInterface $rhs): TokenCountInterface;
    public function subtract(TokenCountInterface $rhs): TokenCountInterface;
    public function greater(TokenCountInterface $rhs): bool;
    public function geq(TokenCountInterface $rhs): bool;
}
