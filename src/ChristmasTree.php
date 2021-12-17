<?php

namespace Sterzik\ChristmasTree;

class ChristmasTree
{
    private $canvas;

    private $drawer;

    private $chains;
    private $glassBalls;
    private $sweets;
    private $lamps;
    private $starColor;
    private $gifts;

    public function __construct()
    {
        $this->canvas = new AACanvas(60, 37);
        $this->drawer = new ChristmasTreeDrawer($this->canvas);

        $this->chains = $this->initializeObjectList($this->drawer->getNumberOfChains(), false);
        $this->glassBalls = $this->initializeObjectList($this->drawer->getNumberOfGlassBalls(), false);
        $this->sweets = $this->initializeObjectList($this->drawer->getNumberOfSweets(), false);
        $this->lamps = $this->initializeObjectList($this->drawer->getNumberOfLamps(), false);
        $this->starColor = false;
        $this->gifts = [];
        $this->numberOfGifts = $this->drawer->getNumberOfGifts();

        $this->redraw();

    }

    public function putChain(?int $color = null): self
    {
        $this->setObjectList($this->chains, null, $color);
        $this->drawChain(null);
        return $this;
    }

    public function putChainPart(int $partNumber, ?int $color = null): self
    {
        $this->setObjectList($this->chains, $partNumber, $color);
        $this->drawChain($partNumber);
        return $this;
    }

    public function removeChain(): self
    {
        $this->setObjectList($this->chains, null, false);
        $this->redraw();
        return $this;
    }

    public function removeChainPart(int $partNumber): self
    {
        $this->setObjectList($this->chains, $partNumber, false);
        $this->redraw();
        return $this;
    }

    public function putGlassBalls(?int $color = null): self
    {
        $this->setObjectList($this->glassBalls, null, $color);
        $this->drawGlassBalls(null);
        return $this;
    }

    public function putGlassBall(int $ballNumber, ?int $color = null): self
    {
        $this->setObjectList($this->glassBalls, $ballNumber, $color);
        $this->drawGlassBalls($ballNumber);
        return $this;
    }

    public function removeGlassBalls(): self
    {
        $this->setObjectList($this->glassBalls, null, false);
        $this->redraw();
        return $this;
    }

    public function removeGlassBall(int $ballNumber): self
    {
        $this->setObjectList($this->glassBalls, $ballNumber, false);
        $this->redraw();
        return $this;
    }

    public function putSweets(?int $color = null): self
    {
        $this->setObjectList($this->sweets, null, $color);
        $this->drawSweets(null);
        return $this;
    }

    public function putSweet(int $sweetNumber, ?int $color = null): self
    {
        $this->setObjectList($this->sweets, $sweetNumber, $color);
        $this->drawSweets($sweetNumber);
        return $this;
    }

    public function removeSweets(): self
    {
        $this->setObjectList($this->sweets, null, false);
        $this->redraw();
        return $this;
    }

    public function removeSweet(int $sweetNumber): self
    {
        $this->setObjectList($this->sweets, $sweetNumber, false);
        $this->redraw();
        return $this;
    }

    public function putLamps(?int $color = null): self
    {
        $this->setObjectList($this->lamps, null, $color);
        $this->drawLamps(null);
        return $this;
    }

    public function putLamp(int $lampNumber, ?int $color = null): self
    {
        $this->setObjectList($this->lamps, $lampNumber, $color);
        $this->drawLamps($lampNumber);
        return $this;
    }

    public function removeLamps(): self
    {
        $this->setObjectList($this->lamps, null, false);
        $this->redraw();
        return $this;
    }

    public function removeLamp(int $lampNumber): self
    {
        $this->setObjectList($this->lamps, $lampNumber, false);
        $this->redraw();
        return $this;
    }

    public function putStar(?int $color = null): self
    {
        $this->starColor = $color;
        $this->drawStar();
        return $this;
    }

    public function removeStar(): self
    {
        $this->starColor = false;
        $this->redraw();
        return $this;
    }

    public function putGift(string $label, ?int $packageColor = null, ?int $labelColor = null): self
    {
        $this->gifts[] = [
            'label' => $label,
            'packageColor' => $packageColor,
            'labelColor' => $labelColor,
        ];
        while (count($this->gifts) > $this->numberOfGifts) {
            array_shift($this->gifts);
        }
        $this->drawGifts();
        return $this;
    }

    public function removeGifts(): self
    {
        $this->gifts = [];
        $this->redraw();
        return $this;
    }

    public function removeGift(int $giftNumber): self
    {
        if (isset($this->gifts[$giftNumber])) {
            array_splice($this->gifts, $giftNumber, 1);
        }
        $this->redraw();
        return $this;
    }

    private function initializeObjectList(int $number, $initialValue): array
    {
        $list = [];
        for($i = 0; $i < $number; $i++) {
            $list[] = $initialValue;
        }
        return $list;
    }

    private function iterateObjectList(array &$list, ?int $number): iterable
    {
        if ($number === null) {
            foreach ($list as $i => $v) {
                yield $i => $v;
            }
        } else {
            $number = $this->modulo($number, count($list));
            yield $number => $list[$number];
        }
    }

    private function setObjectList(array &$list, ?int $number, $value): array
    {
        if ($number === null) {
            foreach (array_keys($list) as $i) {
                $list[$i] = $value;
            }
        } else {
            $number = $this->modulo($number, count($list));
            $list[$number] = $value;
        }
        return $list;
    }

    private function modulo(int $a, int $b): int
    {
        if ($a < 0) {
            return $b - ((-$a) % $b);
        } else {
            return $a % $b;
        }
    }

    private function getSafeColor(?int $color): ?int
    {
        if ($color === null) {
            return null;
        }
        return $this->modulo($color, 16);
    }


    private function redraw()
    {
        $this->canvas->clear();
        $this->drawTree();
        $this->drawChain();
        $this->drawGlassBalls();
        $this->drawSweets();
        $this->drawLamps();
        $this->drawStar();
        $this->drawGifts();
    }

    private function drawGifts()
    {
        foreach ($this->gifts as $i => $giftDescriptor) {
            $this->canvas->setColor($this->getSafeColor($giftDescriptor['packageColor']));
            $this->drawer->drawGift($i);
            $this->canvas->setColor($this->getSafeColor($giftDescriptor['labelColor']));
            $this->drawer->drawGiftLabel($giftDescriptor['label'], $i);
        }
    }

    private function drawTree()
    {
        $this->canvas->setColor(2);
        $this->drawer->drawTree();
        $this->canvas->setColor(3);
        $this->drawer->drawRoot();
        $this->canvas->setColor(null);
    }

    private function drawChain(?int $number = null)
    {
        foreach($this->iterateObjectList($this->chains, $number) as $i => $color) {
            if ($color !== false) {
                $this->canvas->setColor($this->getSafeColor($color));
                $this->drawer->drawChain($i);
            }
        }
        $this->canvas->setColor(null);
    }

    private function drawGlassBalls(?int $number = null)
    {
        foreach($this->iterateObjectList($this->glassBalls, $number) as $i => $color) {
            if ($color !== false) {
                $this->canvas->setColor($this->getSafeColor($color));
                $this->drawer->drawGlassBalls($i);
            }
        }
        $this->canvas->setColor(null);
    }

    private function drawSweets(?int $number = null)
    {
        foreach($this->iterateObjectList($this->sweets, $number) as $i => $color) {
            if ($color !== false) {
                $this->canvas->setColor($this->getSafeColor($color));
                $this->drawer->drawSweets($i);
            }
        }
        $this->canvas->setColor(null);
    }

    private function drawLamps(?int $number = null)
    {
        foreach($this->iterateObjectList($this->lamps, $number) as $i => $color) {
            if ($color !== false) {
                $this->canvas->setColor($this->getSafeColor($color));
                $this->drawer->drawLamps($i);
            }
        }
        $this->canvas->setColor(null);
    }

    private function drawStar()
    {
        if ($this->starColor !== false) {
            $this->canvas->setColor($this->getSafeColor($this->starColor));
            $this->drawer->drawStar();
            $this->canvas->setColor(null);
        }
    }

    public function render($outputFd = null): self
    {
        $this->canvas->render($outputFd);
        return $this;
    }

    public function clearOutput($outputFd = null): self
    {
        $this->canvas->clearOutput($outputFd);
        return $this;
    }
}
