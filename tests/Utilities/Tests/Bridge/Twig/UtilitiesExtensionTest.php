<?php

namespace Zenstruck\Utilities\Tests\Bridge\Twig;

use Twig\Test\IntegrationTestCase;
use Zenstruck\Utilities\Bridge\Twig\UtilitiesExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UtilitiesExtensionTest extends IntegrationTestCase
{
    protected function getExtensions(): array
    {
        return [new UtilitiesExtension()];
    }

    protected function getFixturesDir(): string
    {
        return __DIR__.'/Fixtures/';
    }
}
