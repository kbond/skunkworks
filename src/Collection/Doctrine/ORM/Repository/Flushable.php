<?php

namespace Zenstruck\Collection\Doctrine\ORM\Repository;

use Zenstruck\Collection\Doctrine\ORM\Repository;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait Flushable
{
    final public function flush(): self
    {
        if (!$this instanceof Repository) {
            throw new \BadMethodCallException(); // todo
        }

        $this->em()->flush();

        return $this;
    }
}
