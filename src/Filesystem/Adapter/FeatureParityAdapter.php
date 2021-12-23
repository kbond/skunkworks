<?php

namespace Zenstruck\Filesystem\Adapter;

use Zenstruck\Filesystem\Adapter;

/**
 * Adapter wrapper that sends "support" checks to a "check adapter".
 *
 * In production, say you're using a "cloud" adapter that has a reduced
 * feature set. It's likely that in your dev/test environments, you'll
 * use an in-memory or local adapter that has more features. Use this
 * adapter to wrap your dev/test adapter and your real "production"
 * adapter. The production adapter won't be used for operations but is
 * used for any feature checks. This ensures you don't accidentally use
 * a feature in dev/tests that your production adapter doesn't support.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FeatureParityAdapter extends AdapterWrapper
{
    private AdapterWrapper $check;

    /**
     * @param Adapter $adapter The adapter to use for operations
     * @param Adapter $check   The adapter to use for checking features
     */
    public function __construct(Adapter $adapter, Adapter $check)
    {
        parent::__construct($adapter);

        $this->check = AdapterWrapper::wrap($check);
    }

    public function supports(string $feature): bool
    {
        return $this->check->supports($feature);
    }
}
