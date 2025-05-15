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

namespace Tests\Sylius\Component\Core\Payment;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Payment\InvoiceNumberGeneratorInterface;
use Sylius\Component\Core\Payment\RandomInvoiceNumberGenerator;

final class RandomInvoiceNumberGeneratorTest extends TestCase
{
    private RandomInvoiceNumberGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new RandomInvoiceNumberGenerator();
    }

    public function testShouldImplementRandomInvoiceNumberGenerator(): void
    {
        $this->assertInstanceOf(RandomInvoiceNumberGenerator::class, $this->generator);
    }

    public function testShouldImplementInvoiceNumberGeneratorInterface(): void
    {
        $this->assertInstanceOf(InvoiceNumberGeneratorInterface::class, $this->generator);
    }

    public function testShouldGenerateRandomInvoiceNumber(): void
    {
        $this->assertIsString(
            $this->generator->generate(
                $this->createMock(OrderInterface::class),
                $this->createMock(PaymentInterface::class),
            ),
        );
    }
}
