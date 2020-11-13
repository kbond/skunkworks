<?php

namespace Zenstruck\Utilities\Dsn\Parser;

use Zenstruck\Utilities\Dsn\Exception\UnableToParse;
use Zenstruck\Utilities\Dsn\Parser;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ChainParser implements Parser
{
    /** @var Parser[] */
    private array $parsers;

    /**
     * @param Parser[] $parsers
     */
    public function __construct(iterable $parsers)
    {
        $this->parsers = $parsers;
    }

    public function parse(string $value): \Stringable
    {
        foreach ($this->parsers as $parser) {
            if ($parser instanceof ParserAware) {
                $parser->setParser($this);
            }

            try {
                return $parser->parse($value);
            } catch (UnableToParse $e) {
                continue;
            }
        }

        throw new UnableToParse($value);
    }
}
