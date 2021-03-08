<?php

namespace Zenstruck\Collection\Doctrine\ORM\Result;

use Zenstruck\Collection\Doctrine\ORM\Result;

/**
 * Enables your results to be deleted from the db.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait Deletable
{
    final public function delete(?callable $callback = null): int
    {
        if (!$this instanceof Result) {
            throw new \BadMethodCallException(); // todo
        }

        $callback = $callback ?? static function() {};
        $count = 0;

        foreach ($this->batchProcess() as $entity) {
            $this->em()->remove($entity);
            $callback($entity);
            ++$count;
        }

        $this->resetCount();

        return $count;
    }
}
