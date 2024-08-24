<?php

/**
 * Based on the String Loader
 * will work with any template that uses cs-template:
 */

namespace Cornerstone\TwigIntegration;

use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

use function md5;
use function sprintf;


class ThemeOptionTemplateLoader implements LoaderInterface
{
    public function getSourceContext(string $name): Source
    {
        if (! $this->exists($name)) {
            throw new LoaderError(sprintf('Template "%s" is not defined.', $name));
        }

        return new Source($name, $name);
    }

    public function getCacheKey(string $name): string
    {
        if (! $this->exists($name)) {
            throw new LoaderError(sprintf('Template "%s" is not defined.', $name));
        }

        return md5($name);
    }

    public function isFresh(string $name, int $time): bool
    {
        if (! $this->exists($name)) {
            throw new LoaderError(sprintf('Template "%s" is not defined.', $name));
        }

        return true;
    }

    /** @phpstan-param string $name */
    public function exists(string $name): bool
    {
        if (strpos($name, '/cs-template:/') === 0) {
            return true;
        }

        return false;
    }
}
