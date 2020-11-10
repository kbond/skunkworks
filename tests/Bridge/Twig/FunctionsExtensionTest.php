<?php

namespace Zenstruck\Utilities\Tests\Bridge\Twig;

use Twig\Test\IntegrationTestCase;
use Zenstruck\Utilities\Bridge\Twig\FunctionsExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FunctionsExtensionTest extends IntegrationTestCase
{
    protected function getExtensions(): array
    {
        return [new FunctionsExtension()];
    }

    protected function getFixturesDir(): string
    {
        return __DIR__.'/Fixtures/';
    }
}
