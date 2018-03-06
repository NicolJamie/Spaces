<?php

namespace NicolJamie\Spaces;


class Space extends Affix
{
    /**
     * Space constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->connect();
    }
}