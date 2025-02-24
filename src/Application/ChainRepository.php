<?php

namespace ChainCurrency\Application;

use ChainCurrency\Domain\Chain;

interface ChainRepository
{
    public function get(): Chain;
    public function append(Chain $chain): void;
}