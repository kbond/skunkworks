<?php

namespace Zenstruck\Collection\Tests\Doctrine\Fixture;

/**
 * @Entity
 * @Table(name="entities")
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Entity
{
    public const TABLE = 'entities';

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    public ?int $id;

    /**
     * @Column(type="string")
     */
    public string $value;

    public function __construct(string $value, ?int $id = null)
    {
        $this->id = $id;
        $this->value = $value;
    }
}
