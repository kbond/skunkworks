<?php

namespace Zenstruck\Url\Tests\Bridge\Twig;

use Twig\Test\IntegrationTestCase;
use Zenstruck\Url\Bridge\Twig\UrlExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UrlExtensionTest extends IntegrationTestCase
{
    protected function getExtensions(): array
    {
        return [new UrlExtension()];
    }

    protected function getFixturesDir(): string
    {
        return __DIR__.'/Fixtures/';
    }
}
