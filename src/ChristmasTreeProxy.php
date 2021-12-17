<?php

namespace Sterzik\ChristmasTree;

class ChristmasTreeProxy
{
    private $proxy;

    public function __construct($proxy)
    {
        $this->proxy = $proxy;
    }
    public function putChain(?int $color = null): self
    {
        $this->proxy->putChain($color);
        return $this;
    }

    public function putChainPart(int $partNumber, ?int $color = null): self
    {
        $this->proxy->putChainPart($partNumber, $color);
        return $this;
    }

    public function removeChain(): self
    {
        $this->proxy->removeChain();
        return $this;
    }

    public function removeChainPart(int $partNumber): self
    {
        $this->proxy->removeChainPart($partNumber);
        return $this;
    }

    public function putGlassBalls(?int $color = null): self
    {
        $this->proxy->putGlassBalls($color);
        return $this;
    }

    public function putGlassBall(int $ballNumber, ?int $color = null): self
    {
        $this->proxy->putGlassBall($ballNumber, $color);
        return $this;
    }

    public function removeGlassBalls(): self
    {
        $this->proxy->removeGlassBalls();
        return $this;
    }

    public function removeGlassBall(int $ballNumber): self
    {
        $this->proxy->removeGlassBall($ballNumber);
        return $this;
    }

    public function putSweets(?int $color = null): self
    {
        $this->proxy->putSweets($color);
        return $this;
    }

    public function putSweet(int $sweetNumber, ?int $color = null): self
    {
        $this->proxy->putSweet($sweetNumber, $color);
        return $this;
    }

    public function removeSweets(): self
    {
        $this->proxy->removeSweets();
        return $this;
    }

    public function removeSweet(int $sweetNumber): self
    {
        $this->proxy->removeSweet($sweetNumber);
        return $this;
    }

    public function putLamps(?int $color = null): self
    {
        $this->proxy->putLamps($color);
        return $this;
    }

    public function putLamp(int $lampNumber, ?int $color = null): self
    {
        $this->proxy->putLamp($lampNumber, $color);
        return $this;
    }

    public function removeLamps(): self
    {
        $this->proxy->removeLamps();
        return $this;
    }

    public function removeLamp(int $lampNumber): self
    {
        $this->proxy->removeLamp($lampNumber);
        return $this;
    }

    public function putStar(?int $color = null): self
    {
        $this->proxy->putStar($color);
        return $this;
    }

    public function removeStar(): self
    {
        $this->proxy->removeStar();
        return $this;
    }

    public function putGift(string $label, ?int $packageColor = null, ?int $labelColor = null): self
    {
        $this->proxy->putGift($label, $packageColor, $labelColor);
        return $this;
    }

    public function removeGifts(): self
    {
        $this->proxy->removeGifts();
        return $this;
    }

    public function removeGift(int $giftNumber): self
    {
        $this->proxy->removeGift($giftNumber);
        return $this;
    }

}
