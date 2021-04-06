<?php

namespace Zenstruck\Collection\Tests\Doctrine\Fixture;

/**
 * @Entity
 * @Table(name="relations")
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Relation
{
    public const TABLE = 'relations';

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    public ?int $id;

    /**
     * @Column(type="string")
     */
    public int $value;

    public function __construct(int $value, ?int $id = null)
    {
        $this->id = $id;
        $this->value = $value;
    }
}
