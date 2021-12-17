<?php

namespace Sterzik\ChristmasTreeClient;

class ChristmasTreeClient
{
    const TREE_CONTROL_URL = "https://marvin.milimetr.org/christmas-tree/proxy.php";

    private $proxyUrl;

    public function __construct(string $proxyUrl = self::TREE_CONTROL_URL)
    {
        $this->proxyUrl = $proxyUrl;
    }

    public function putChain(?int $color = null): self
    {
        $this->invoke('putChain', $color);
        return $this;
    }

    public function putChainPart(int $partNumber, ?int $color = null): self
    {
        $this->invoke('putChainPart', $partNumber, $color);
        return $this;
    }

    public function removeChain(): self
    {
        $this->invoke('removeChain');
        return $this;
    }

    public function removeChainPart(int $partNumber): self
    {
        $this->invoke('removeChainPart', $partNumber);
        return $this;
    }

    public function putGlassBalls(?int $color = null): self
    {
        $this->invoke('putGlassBalls', $color);
        return $this;
    }

    public function putGlassBall(int $ballNumber, ?int $color = null): self
    {
        $this->invoke('putGlassBall', $ballNumber, $color);
        return $this;
    }

    public function removeGlassBalls(): self
    {
        $this->invoke('removeGlassBalls');
        return $this;
    }

    public function removeGlassBall(int $ballNumber): self
    {
        $this->invoke('removeGlassBall', $ballNumber);
        return $this;
    }

    public function putSweets(?int $color = null): self
    {
        $this->invoke('putSweets', $color);
        return $this;
    }

    public function putSweet(int $sweetNumber, ?int $color = null): self
    {
        $this->invoke('putSweet', $sweetNumber, $color);
        return $this;
    }

    public function removeSweets(): self
    {
        $this->invoke('removeSweets');
        return $this;
    }

    public function removeSweet(int $sweetNumber): self
    {
        $this->invoke('removeSweet', $sweetNumber);
        return $this;
    }

    public function putLamps(?int $color = null): self
    {
        $this->invoke('putLamps', $color);
        return $this;
    }

    public function putLamp(int $lampNumber, ?int $color = null): self
    {
        $this->invoke('putLamp', $lampNumber, $color);
        return $this;
    }

    public function removeLamps(): self
    {
        $this->invoke('removeLamps');
        return $this;
    }

    public function removeLamp(int $lampNumber): self
    {
        $this->invoke('removeLamp', $lampNumber);
        return $this;
    }

    public function putStar(?int $color = null): self
    {
        $this->invoke('putStar', $color);
        return $this;
    }

    public function removeStar(): self
    {
        $this->invoke('removeStar');
        return $this;
    }

    public function putGift(string $label, ?int $packageColor = null, ?int $labelColor = null): self
    {
        $this->invoke('putGift', $label, $packageColor, $labelColor);
        return $this;
    }

    public function removeGifts(): self
    {
        $this->invoke('removeGifts');
        return $this;
    }

    public function removeGift(int $giftNumber): self
    {
        $this->invoke('removeGift', $giftNumber);
        return $this;
    }

    private function invoke(...$arguments)
    {
        $command = json_encode($arguments);
        $delim = (strpos($this->proxyUrl, '?') === false) ? '?' : '&';
        $url = $this->proxyUrl . $delim . "write=".urlencode($command);
        @file_get_contents($url);
    }

}

