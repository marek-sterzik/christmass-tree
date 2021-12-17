<?php

namespace Sterzik\ChristmassTree;

class ChristmassTreeDrawer
{
    const GLASSBALL_COORDINATES = [[11, 21], [11, 15], [16, 9], [18, 4], [18, 20], [21, 6], [23, 11], [27, 19]];
    const SWEETS_COORDINATES = [[7, 22], [9, 19], [14, 10], [15, 7], [22, 12], [16, 14], [23, 17], [28, 22]];
    const LAMP_L_COORDINATES = [[14, 5], [12, 8], [10, 11], [8, 14], [6, 17], [4, 20]];
    const LAMP_R_COORDINATES = [[22, 5], [24, 8], [26, 11], [28, 14], [30, 17], [32, 20]];

    const TREE_OFFSET = 10;

    private $canvas;

    public function __construct(AACanvas $canvas)
    {
        $this->canvas = $canvas;
    }
    

    public function drawTree(): self
    {
        $this->canvas->writeText(self::TREE_OFFSET + 18, 1, "|");
        $this->canvas->writeText(self::TREE_OFFSET + 17, 2, "/\0\\");
        for ($i = 0; $i < 7; $i++) {
            $this->canvas->writeText(self::TREE_OFFSET + 16 - 2*$i, 3*$i + 3, "/");
            $this->canvas->writeText(self::TREE_OFFSET + 15 - 2*$i, 3*$i + 4, "/");
            $this->canvas->writeText(self::TREE_OFFSET + 14 - 2*$i, 3*$i + 5, "/_");
            
            $this->canvas->writeText(self::TREE_OFFSET + 20 + 2*$i, 3*$i + 3, "\\");
            $this->canvas->writeText(self::TREE_OFFSET + 21 + 2*$i, 3*$i + 4, "\\");
            $this->canvas->writeText(self::TREE_OFFSET + 21 + 2*$i, 3*$i + 5, "_\\");
        }
        $this->canvas->writeText(self::TREE_OFFSET + 2, 23, "/".str_repeat(":", 31)."\\");
        return $this;
    }

    public function drawRoot(): self
    {
        $this->canvas->writeText(self::TREE_OFFSET + 15, 24, "\\".str_repeat("_", 4)."/");
        return $this;
    }

    private function drawCharAtTree(string $text, array $positions): self
    {
        foreach ($positions as list($x, $y)) {
            $this->canvas->writeText(self::TREE_OFFSET + $x, $y, $text);
        }

        return $this;
    }

    public function drawChain(int $level): self
    {
        switch ($level) {
        case 0:
            $this
                ->drawCharAtTree("~", [[20, 4], [17, 5]])
                ->drawCharAtTree("~", [[19, 4], [16, 5]])
                ->drawCharAtTree("\"", [[18, 5]])
            ;
            break;
        case 1:
            $this
                ->drawCharAtTree('~', [[18, 7], [21, 8]])
                ->drawCharAtTree('.', [[16, 6], [19, 7], [22, 8]])
                ->drawCharAtTree('"', [[15, 6], [17, 7], [20, 8]])
            ;
            break;
        case 2:
            $this
                ->drawCharAtTree('~', [[14, 11], [15, 11], [19, 10], [20, 10], [22, 9]])
                ->drawCharAtTree('.', [[12, 11], [13, 11], [18, 10]])
                ->drawCharAtTree('"', [[16, 11], [17, 11], [21, 10], [23, 9]])
            ;
            break;
        case 3:
            $this
                ->drawCharAtTree('~', [[17, 13], [18, 13], [23, 14], [24, 14]])
                ->drawCharAtTree('.', [[13, 12], [14, 12], [19, 13], [20, 13], [25, 14], [26, 14]])
                ->drawCharAtTree('"', [[12, 12], [15, 13], [16, 13], [21, 14], [22, 14]])
            ;
            break;
        case 4:
            $this
                ->drawCharAtTree('~', [[10, 17], [11, 17], [16, 16], [17, 16], [18, 16], [24, 15], [25, 15]])
                ->drawCharAtTree('.', [[8, 17], [9, 17], [14, 16], [15, 16], [22, 15], [23, 15]])
                ->drawCharAtTree('"', [[12, 17], [13, 17], [19, 16], [20, 16], [21, 16], [26, 15], [27, 15]])
            ;
            break;
        case 5:
            $this
                ->drawCharAtTree('~', [[10, 18], [11, 18], [12, 18], [18, 19], [19, 19], [26, 20], [27, 20]])
                ->drawCharAtTree('.', [[13, 18], [14, 18], [20, 19], [21, 19], [22, 19], [28, 20], [29, 20], [30, 20]])
                ->drawCharAtTree('"', [[8, 18], [9, 18], [15, 19], [16, 19], [17, 19], [23, 20], [24, 20], [25, 20]])
            ;
            break;
        case 6:
            $this
                ->drawCharAtTree('~', [[7, 23], [8, 23], [9, 23], [16, 22], [17, 22], [18, 22], [25, 21], [26, 21], [27, 21]])
                ->drawCharAtTree('.', [[4, 23], [5, 23], [6, 23], [13, 22], [14, 22], [15, 22], [22, 21], [23, 21], [24, 21]])
                ->drawCharAtTree('"', [[10, 23], [11, 23], [12, 23], [19, 22], [20, 22], [21, 22], [28, 21], [29, 21], [30, 21]])
            ;
            break;
        }
        return $this;
    }

    public function getNumberOfChains(): int
    {
        return 7;
    }

    public function drawGlassBalls(?int $number = null): self
    {
        return $this->drawItems(self::GLASSBALL_COORDINATES, 'o', $number);
    }

    public function getNumberOfGlassBalls(): int
    {
        return count(self::GLASSBALL_COORDINATES);
    }

    public function drawSweets(?int $number = null): self
    {
        return $this->drawItems(self::SWEETS_COORDINATES, 'J', $number);
    }

    public function getNumberOfSweets(): int
    {
        return count(self::GLASSBALL_COORDINATES);
    }

    public function drawLamps(?int $number = null): self
    {
        $this->drawItems(self::LAMP_L_COORDINATES, '!', $number);
        $this->drawItems(self::LAMP_R_COORDINATES, '!', $number);
        return $this;
    }

    public function getNumberOfLamps(): int
    {
        return max(count(self::LAMP_L_COORDINATES), count(self::LAMP_R_COORDINATES));
    }

    private function drawItems(array $coordinates, string $char, ?int $number = null): self
    {
        if ($number !== null) {
            if (isset($coordinates[$number])) {
                $coordinates = [$coordinates[$number]];
            } else {
                $coordinates = [];
            }
        }
        $this->drawCharAtTree($char, $coordinates);
        return $this;
    }

    public function drawStar(): self
    {
        $this->canvas->writeText(self::TREE_OFFSET + 16, 0, "_.|._");
        $this->canvas->writeText(self::TREE_OFFSET + 16, 1, " '|' ");
        return $this;
    }

    public function getNumberOfGifts(): int
    {
        return 6;
    }

    private function getGiftBase(int $giftNumber): array
    {
        if ($giftNumber < 0 || $giftNumber >= $this->getNumberOfGifts()) {
            return [null, null];
        }
        $baseX = ($giftNumber % 3) * 20;
        $baseY = 25 + intdiv($giftNumber, 3) * 6;
        return [$baseX, $baseY];
    }

    public function drawGift(int $giftNumber): self
    {
        list($baseX, $baseY) = $this->getGiftBase($giftNumber);

        if ($baseX !== null && $baseY !== null) {
            $this->canvas->writeText($baseX+6, $baseY, "_   _");
            $this->canvas->writeText($baseX+5, $baseY+1, "((\\o/))");
            $this->canvas->writeText($baseX, $baseY+2, ".".str_repeat("-", 5)."//^\\\\".str_repeat("-", 5).".");
            $this->canvas->writeText($baseX, $baseY+3, "|".str_repeat(" ", 4)."/ | | \\".str_repeat(" ", 4)."|");
            $this->canvas->writeText($baseX, $baseY+4, "|".str_repeat(" ", 6)."| |".str_repeat(" ", 6)."|");
            $this->canvas->writeText($baseX, $baseY+5, "'".str_repeat("-", 6)."===".str_repeat("-", 6)."'");
        }

        return $this;
    }

    public function drawGiftLabel(string $name, int $giftNumber): self
    {
        list($baseX, $baseY) = $this->getGiftBase($giftNumber);

        if ($baseX !== null && $baseY !== null) {
            if ($this->canvas->getTextLength($name) <= 6) {
                $this->canvas->writeTextLimit($baseX + 1, $baseY + 4, 6, $name, true);
            } else {
                $this->canvas->writeTextLimit($baseX + 1, $baseY + 4, 15, $name, true);
            }
        }

        return $this;
    }
}

