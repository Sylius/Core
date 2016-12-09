<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProviderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class OrderPaymentProcessor implements OrderProcessorInterface
{
    /**
     * @var OrderPaymentProviderInterface
     */
    private $orderPaymentProvider;

    /**
     * @var string
     */
    private $targetState;

    /**
     * @param OrderPaymentProviderInterface $orderPaymentProvider
     * @param string $targetState
     */
    public function __construct(
        OrderPaymentProviderInterface $orderPaymentProvider,
        $targetState = PaymentInterface::STATE_CART
    ) {
        $this->orderPaymentProvider = $orderPaymentProvider;
        $this->targetState = $targetState;
    }

    /**
     * {@inheritdoc}
     */
    public function process(BaseOrderInterface $order)
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        if (OrderInterface::STATE_CANCELLED === $order->getState()) {
            return;
        }

        $lastPayment = $order->getLastPayment($this->targetState);
        if (null !== $lastPayment) {
            $lastPayment->setCurrencyCode($order->getCurrencyCode());
            $lastPayment->setAmount($order->getTotal());

            return;
        }

        $newPayment = $this->orderPaymentProvider->provideOrderPayment($order, $this->targetState);
        if (null !== $newPayment) {
            $order->addPayment($newPayment);
        }
    }
}
