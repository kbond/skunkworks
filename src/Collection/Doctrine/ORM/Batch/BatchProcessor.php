<?php

namespace Zenstruck\Collection\Doctrine\ORM\Batch;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Internal\Hydration\IterableResult;

/**
 * @author Marco Pivetta <ocramius@gmail.com>
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class BatchProcessor implements \IteratorAggregate
{
    protected iterable $items;
    private EntityManagerInterface $em;
    private int $chunkSize;

    protected function __construct(iterable $items, EntityManagerInterface $em, int $chunkSize = 100)
    {
        if ($items instanceof IterableResult) {
            $items = new IterableResultDecorator($items);
        }

        $this->items = $items;
        $this->em = $em;
        $this->chunkSize = $chunkSize;
    }

    /**
     * @return BatchProcessor|CountableBatchProcessor
     */
    final public static function for(iterable $items, EntityManagerInterface $em, int $chunkSize = 100)
    {
        if (\is_countable($items)) {
            return new CountableBatchProcessor($items, $em, $chunkSize);
        }

        return new self($items, $em, $chunkSize);
    }

    final public function getIterator(): \Traversable
    {
        $logger = $this->em->getConfiguration()->getSQLLogger();
        $this->em->getConfiguration()->setSQLLogger(null);
        $this->em->beginTransaction();

        $iteration = 0;

        try {
            foreach ($this->items as $key => $value) {
                yield $key => $value;

                $this->flushAndClearBatch(++$iteration);
            }
        } catch (\Throwable $e) {
            $this->em->rollback();

            throw $e;
        }

        $this->flushAndClear();
        $this->em->commit();
        $this->em->getConfiguration()->setSQLLogger($logger);
    }

    private function flushAndClearBatch(int $iteration): void
    {
        if ($iteration % $this->chunkSize) {
            return;
        }

        $this->flushAndClear();
    }

    private function flushAndClear(): void
    {
        $this->em->flush();
        $this->em->clear();
    }
}
