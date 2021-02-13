<?php

namespace Zenstruck\Filesystem\Adapter\Factory;

use Zenstruck\Filesystem\Adapter\Factory;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait ProvideFactory
{
    private ?Factory $factory = null;

    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;
    }

    protected function factory(): Factory
    {
        if (!$this->factory) {
            throw new \LogicException('Factory not set.');
        }

        return $this->factory;
    }
}
