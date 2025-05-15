<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Statistics\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\Registry\OrdersTotalsProviderRegistryInterface;
use Sylius\Component\Core\Statistics\Registry\StatisticsProviderRegistryInterface;
use Webmozart\Assert\Assert;

final class SalesStatisticsProvider implements SalesStatisticsProviderInterface
{
    /** @var array<string, string> */
    private array $formatsMap = [];

    /** @var array<StatisticsProviderRegistryInterface> */
    private array $statisticsProviderRegistries = [];

    /**
     * @param array<string, array{interval: string, period_format: string}> $intervalsMap
     * @param iterable<StatisticsProviderRegistryInterface> $statisticsProviderRegistries
     */
    public function __construct(
        private OrdersTotalsProviderRegistryInterface $ordersTotalsProviderRegistry,
        array $intervalsMap,
        iterable|null $statisticsProviderRegistries = null,
    ) {
        foreach ($intervalsMap as $type => $intervalMap) {
            $this->formatsMap[$type] = $intervalMap['period_format'];
        }

        if ($statisticsProviderRegistries === null) {
            trigger_deprecation(
                'sylius/core',
                '2.1',
                'Not passing $statisticsProviderRegistries through constructor is deprecated and will be prohibited in Sylius 3.0.',
            );

            return;
        }

        Assert::allIsInstanceOf(
            $statisticsProviderRegistries,
            StatisticsProviderRegistryInterface::class,
            sprintf('All statistics provider registries should implement the "%s" interface.', StatisticsProviderRegistryInterface::class),
        );
        $this->statisticsProviderRegistries = $statisticsProviderRegistries instanceof \Traversable ? iterator_to_array($statisticsProviderRegistries) : $statisticsProviderRegistries;
    }

    public function provide(string $intervalType, \DatePeriod $datePeriod, ChannelInterface $channel): array
    {
        $format = $this->getPeriodFormat($intervalType);

        if ($this->statisticsProviderRegistries == []) {
            $sales = $this->ordersTotalsProviderRegistry
                ->getByType($intervalType)
                ->provideForPeriodInChannel($datePeriod, $channel)
            ;

            return $this->withFormattedDates($sales, $format);
        }

        $data = [];
        foreach ($this->statisticsProviderRegistries as $statisticsProviderRegistry) {
            $result = $statisticsProviderRegistry
                ->getByType($intervalType)
                ->provideForPeriodInChannel($datePeriod, $channel)
            ;

            if ($data === []) {
                $data = $result;

                continue;
            }

            $data = $this->mergeArraysByPeriod($data, $result);
        }

        return $this->withFormattedDates($data, $format);
    }

    /**
     * @param array<array{period: \DateTimeInterface, ...}> $sales
     *
     * @return array<array{period: string, ...}>
     */
    private function withFormattedDates(array $sales, string $format): array
    {
        return array_map(
            fn(array $entry) => array_merge(
                ['period' => $entry['period']->format($format)],
                array_diff_key($entry, ['period' => true])
            ),
            $sales
        );
    }

    private function getPeriodFormat(string $intervalType): string
    {
        Assert::keyExists($this->formatsMap, $intervalType);

        return $this->formatsMap[$intervalType];
    }

    /**
     * @param array<array{period: \DateTimeInterface, ...}> $firstArray
     * @param array<array{period: \DateTimeInterface, ...}> $secondArray
     *
     * @return array<array{period: \DateTimeInterface, ...}>
     */
    private function mergeArraysByPeriod(array $firstArray, array $secondArray): array
    {
        $indexByPeriod = function(array $items) {
            $result = [];
            foreach ($items as $item) {
                if (!isset($item['period']) || !$item['period'] instanceof \DateTimeInterface) {
                    continue;
                }
                $key = $item['period']->format('c');
                $result[$key] = $item;
            }

            return $result;
        };

        $indexedFirstArray = $indexByPeriod($firstArray);
        $indexedSecondArray = $indexByPeriod($secondArray);

        $allKeys = array_unique(array_merge(array_keys($indexedFirstArray), array_keys($indexedSecondArray)));

        $result = [];
        foreach ($allKeys as $key) {
            $itemFromFirstArray = $indexedFirstArray[$key] ?? [];
            $itemFromSecondArray = $indexedSecondArray[$key] ?? [];

            $period = $itemFromFirstArray['period'] ?? $itemFromSecondArray['period'] ?? null;
            if (!$period instanceof \DateTimeInterface) {
                continue;
            }

            unset($itemFromFirstArray['period'], $itemFromSecondArray['period']);

            $result[] = array_merge(
                ['period' => $period],
                $itemFromFirstArray,
                $itemFromSecondArray,
            );
        }

        return $result;
    }
}
