<?php

namespace Zenstruck\Collection\Doctrine\ORM\Repository;

use Zenstruck\Collection\Doctrine\ORM\Repository;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait Writable
{
    final public function add(object $item, bool $flush = true): self
    {
        if (!$this instanceof Repository) {
            throw new \BadMethodCallException(); // todo
        }

        if (!\is_a($item, $this->getClassName())) {
            throw new \InvalidArgumentException(); // todo
        }

        $this->em()->persist($item);

        if ($flush) {
            $this->em()->flush();
        }

        return $this;
    }
}
