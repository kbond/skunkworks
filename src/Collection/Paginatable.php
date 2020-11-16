<?php

namespace Zenstruck\Collection;

use Zenstruck\Collection;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait Paginatable
{
    public function paginate(int $page = 1, int $limit = Page::DEFAULT_LIMIT): Page
    {
        if (!$this instanceof Collection) {
            throw new \BadMethodCallException(); // todo
        }

        return new Page($this, $page, $limit);
    }
}
