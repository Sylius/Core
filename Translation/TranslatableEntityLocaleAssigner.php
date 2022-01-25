<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Translation;

use Sylius\Component\Core\Checker\CommandBasedContextCheckerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Sylius\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;

final class TranslatableEntityLocaleAssigner implements TranslatableEntityLocaleAssignerInterface
{
    public function __construct(
        private LocaleContextInterface $localeContext,
        private TranslationLocaleProviderInterface $translationLocaleProvider,
        private ?CommandBasedContextCheckerInterface $commandBasedChecker = null
    ) { }

    public function assignLocale(TranslatableInterface $translatableEntity): void
    {
        $fallbackLocale = $this->translationLocaleProvider->getDefaultLocaleCode();

        if ($this->commandBasedChecker !== null && $this->commandBasedChecker->isRunningFromCommand()) {
            $translatableEntity->setCurrentLocale($fallbackLocale);
            $translatableEntity->setFallbackLocale($fallbackLocale);

            return;
        }

        try {
            $currentLocale = $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException) {
            $currentLocale = $fallbackLocale;
        }

        $translatableEntity->setCurrentLocale($currentLocale);
        $translatableEntity->setFallbackLocale($fallbackLocale);
    }
}
