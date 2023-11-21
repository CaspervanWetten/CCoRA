<?php
namespace Cora\Utils;

class Paginator {
    protected $limit;
    protected $page;

    public function __construct($limit=1, $page=1) {
        $this->limit = max(1, $limit);
        $this->page = max(1, $page);
    }

    public function limit() {
        return $this->limit;
    }

    public function page() {
        return $this->page;
    }

    public function offset() {
        return ($this->page - 1) * $this->limit;
    }

    public function next() {
        return new Paginator($this->limit, $this->page + 1);
    }

    public function prev() {
        return new Paginator($this->limit, max($this->page - 1, 1));
    }
}

