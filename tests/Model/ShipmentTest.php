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

namespace Tests\Sylius\Component\Core\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\Shipment;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\Shipment as BaseShipment;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;

final class ShipmentTest extends TestCase
{
    private MockObject&OrderInterface $order;

    private AdjustmentInterface&MockObject $adjustment;

    private Shipment $shipment;

    protected function setUp(): void
    {
        $this->order = $this->createMock(OrderInterface::class);
        $this->adjustment = $this->createMock(AdjustmentInterface::class);
        $this->shipment = new Shipment();
    }

    public function testShouldImplementShipmentInterface(): void
    {
        $this->assertInstanceOf(ShipmentInterface::class, $this->shipment);
    }

    public function testShouldExtendBaseShipment(): void
    {
        $this->assertInstanceOf(BaseShipment::class, $this->shipment);
    }

    public function testShouldNotBelongToOrderByDefault(): void
    {
        $this->assertNull($this->shipment->getOrder());
    }

    public function testShouldAllowAttachItselfToOrder(): void
    {
        $this->shipment->setOrder($this->order);

        $this->assertSame($this->order, $this->shipment->getOrder());
    }

    public function testShouldAllowDetachItselfFromOrder(): void
    {
        $this->shipment->setOrder($this->order);

        $this->shipment->setOrder(null);

        $this->assertNull($this->shipment->getOrder());
    }

    public function testShouldAddAdjustment(): void
    {
        $this->shipment->setOrder($this->order);
        $this->adjustment->expects($this->once())->method('isNeutral')->willReturn(true);
        $this->adjustment->expects($this->once())->method('setShipment')->with($this->shipment);

        $this->shipment->addAdjustment($this->adjustment);

        $this->assertTrue($this->shipment->hasAdjustment($this->adjustment));
    }

    public function testShouldRemoveAdjustment(): void
    {
        $this->shipment->setOrder($this->order);
        $this->shipment->addAdjustment($this->adjustment);
        $this->adjustment->expects($this->once())->method('setShipment')->with(null);
        $this->adjustment->expects($this->once())->method('isLocked')->willReturn(false);

        $this->shipment->removeAdjustment($this->adjustment);

        $this->assertFalse($this->shipment->hasAdjustment($this->adjustment));
    }

    public function testShouldNotRemoveLockedAdjustment(): void
    {
        $this->shipment->setOrder($this->order);
        $this->shipment->addAdjustment($this->adjustment);
        $this->adjustment->expects($this->never())->method('setShipment')->with(null);
        $this->adjustment->expects($this->once())->method('isLocked')->willReturn(true);

        $this->shipment->removeAdjustment($this->adjustment);

        $this->assertTrue($this->shipment->hasAdjustment($this->adjustment));
    }

    public function testShouldHaveCorrectAdjustmentsTotal(): void
    {
//        $this->shipment->setOrder($this->order);
//        $this->order->expects($this->exactly(5))->method('recalculateAdjustmentsTotal');
//        $secondAdjustment = $this->createMock(AdjustmentInterface::class);
//        $thirdAdjustment = $this->createMock(AdjustmentInterface::class);
//        $fourthAdjustment = $this->createMock(AdjustmentInterface::class);
//        $this->adjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
//        $this->adjustment->expects($this->exactly(2))->method('setShipment');
//        $this->adjustment->expects($this->once())->method('isLocked')->willReturn(false);
        ////        $this->adjustment->expects($this->once())->method('setShipment')->with(null);
        ////        $this->adjustment->expects($this->atLeastOnce())->method('getAmount')->willReturn(100);
//        $secondAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
//        $secondAdjustment->expects($this->once())->method('setShipment')->with($this->shipment);
        ////        $secondAdjustment->expects($this->atLeastOnce())->method('getAmount')->willReturn(50);
//        $thirdAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
//        $thirdAdjustment->expects($this->once())->method('setShipment')->with($this->shipment);
        ////        $thirdAdjustment->expects($this->atLeastOnce())->method('getAmount')->willReturn(250);
//        $fourthAdjustment->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);
//        $fourthAdjustment->expects($this->once())->method('setShipment')->with($this->shipment);
        ////        $fourthAdjustment->expects($this->atLeastOnce())->method('getAmount')->willReturn(150);
//
//        $this->shipment->addAdjustment($this->adjustment);
//        $this->shipment->addAdjustment($secondAdjustment);
//        $this->shipment->addAdjustment($thirdAdjustment);
//        $this->shipment->addAdjustment($fourthAdjustment);
//        $this->shipment->removeAdjustment($this->adjustment);
//
//        $this->assertSame(300, $this->shipment->getAdjustmentsTotal());
        $this->shipment->setOrder($this->order);
        $adjustment1 = $this->createMock(AdjustmentInterface::class);
        $adjustment2 = $this->createMock(AdjustmentInterface::class);
        $adjustment3 = $this->createMock(AdjustmentInterface::class);
        $adjustment4 = $this->createMock(AdjustmentInterface::class);
        $adjustment1->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $adjustment1SetShipmentinvokedCount = $this->exactly(2);
        $adjustment1->expects($adjustment1SetShipmentinvokedCount)->method('setShipment')->willReturnCallback(function ($arg) use ($adjustment1SetShipmentinvokedCount, $adjustment1) {
            if ($adjustment1SetShipmentinvokedCount->numberOfInvocations() === 1) {
                $this->assertEquals($adjustment1, $arg);
            }
            $this->assertNull($arg);
        });
        $adjustment1->expects($this->once())->method('isLocked')->willReturn(false);
//        $adjustment1->expects($this->once())->method('setShipment')->with(null);
        $adjustment1->expects($this->atLeastOnce())->method('getAmount')->willReturn(100);
        $adjustment2->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $adjustment2->expects($this->once())->method('setShipment')->with($this->shipment);
        $adjustment2->expects($this->atLeastOnce())->method('getAmount')->willReturn(50);
        $adjustment3->expects($this->atLeastOnce())->method('isNeutral')->willReturn(false);
        $adjustment3->expects($this->once())->method('setShipment')->with($this->shipment);
        $adjustment3->expects($this->atLeastOnce())->method('getAmount')->willReturn(250); // O TEGO CHODZI
        $adjustment4->expects($this->atLeastOnce())->method('isNeutral')->willReturn(true);
        $adjustment4->expects($this->once())->method('setShipment')->with($this->shipment);
        $adjustment4->expects($this->once())->method('getAmount')->willReturn(150);
        $this->order->expects($this->exactly(5))->method('recalculateAdjustmentsTotal');
        $this->shipment->addAdjustment($adjustment1);
        $this->shipment->addAdjustment($adjustment2);
        $this->shipment->addAdjustment($adjustment3);
        $this->shipment->addAdjustment($adjustment4);
        $this->shipment->removeAdjustment($adjustment1);

        $this->assertSame(300, $this->shipment->getAdjustmentsTotal());
    }

    public function testShouldThrowExceptionWhenShipmentUnitsAreNotOrderItemUnitsWhenGettingShippingUnitsTotal(): void
    {
        $unit = $this->createMock(ShipmentUnitInterface::class);
        $unit->expects($this->once())->method('setShipment')->with($this->shipment);
        $this->shipment->addUnit($unit);

        $this->expectException(\InvalidArgumentException::class);

        $this->shipment->getShippingUnitTotal();
    }

    public function testShouldReturnTotalOfAllUnit(): void
    {
        $firstUnit = $this->createMock(OrderItemUnitInterface::class);
        $secondUnit = $this->createMock(OrderItemUnitInterface::class);
        $thirdUnit = $this->createMock(OrderItemUnitInterface::class);

        $firstUnit->expects($this->once())->method('getTotal')->willReturn(1000);
        $firstUnit->expects($this->once())->method('setShipment')->with($this->shipment);
        $secondUnit->expects($this->once())->method('getTotal')->willReturn(2000);
        $secondUnit->expects($this->once())->method('setShipment')->with($this->shipment);
        $thirdUnit->expects($this->once())->method('getTotal')->willReturn(3000);
        $thirdUnit->expects($this->once())->method('setShipment')->with($this->shipment);

        $this->shipment->addUnit($firstUnit);
        $this->shipment->addUnit($secondUnit);
        $this->shipment->addUnit($thirdUnit);

        $this->assertSame(6000, $this->shipment->getShippingUnitTotal());
    }
}
