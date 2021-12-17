<?php

namespace Sterzik\ChristmassTree;

class AACanvas
{
    const ATTR_DESCRIPTOR = [
        ["color", null],
        ["bgColor", null],
        ["blink", null],
    ];

    /** @var string */
    private $inputEncoding = 'utf-8';
    
    /** @var string */
    private $outputEncoding = 'utf-8';

    /** @var int|null */
    private $lastWrittenWidth = null;

    /** @var int|null */
    private $lastWrittenHeight = null;

    /** @var int */
    private $width;

    /** @var int */
    private $height;

    /** @var mixed */
    private $canvas;

    /** @var array */
    private $currentAttrs;


    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->clear();
    }

    public function clear(): self
    {
        $this->currentAttrs = [];

        foreach (self::ATTR_DESCRIPTOR as $index => $descriptor) {
            $this->currentAttrs[$index] = $descriptor[1];
        }
        $this->canvas = [];

        $space = mb_ord(" ", $this->inputEncoding);
        if ($space === false) {
            $space = 32;
        }

        for ($i = 0; $i < $this->height; $i++) {
            $this->canvas[$i] = [];
            for ($j = 0; $j < $this->width; $j++) {
                $this->canvas[$i][$j] = [$space, $this->currentAttrs];
            }
        }

        return $this;
    }

    public function setColor(?int $color = null): self
    {
        $this->currentAttrs[0] = $color;
        return $this;
    }

    public function setBgColor(?int $color = null): self
    {
        $this->currentAttrs[1] = $color;
        return $this;
    }

    public function setSlowBlinking(): self
    {
        $this->currentAttrs[2] = 1;
        return $this;
    }

    public function setFastBlinking(): self
    {
        $this->currentAttrs[2] = 2;
        return $this;
    }

    public function setNoBlinking(): self
    {
        $this->currentAttrs[2] = null;
        return $this;
    }

    public function put(int $x, int $y, string $char): self
    {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            return $this;
        }

        $charCode = mb_ord($char, $this->inputEncoding);

        if ($charCode !== false && $charCode !== 0) {
            if ($charCode < 32) {
                $charCode = 32;
            }
            $this->canvas[$y][$x] = [$charCode, $this->currentAttrs];
        }

        return $this;
    }

    public function getTextLength(string $text): int
    {
        $length = @mb_strlen($text, $this->inputEncoding);
        if (!is_int($length)) {
            $length = 0;
        }
        return $length;
    }

    public function writeText(int $x, int $y, string $text): self
    {
        $length = $this->getTextLength($text);
        for ($i = 0; $i < $length; $i++) {
            $this->put($x + $i, $y, mb_substr($text, $i, 1, $this->inputEncoding));
        }
        return $this;
    }

    public function writeTextLimit(int $x, int $y, int $lengthLimit, string $text, bool $center = false): self
    {
        $length = $this->getTextLength($text);
        if ($length > $lengthLimit) {
            $text = mb_substr($text, 0, $lengthLimit, $this->inputEncoding);
            $length = $lengthLimit;
        }

        $space = $center ? intdiv($lengthLimit - $length, 2) : 0;
        return $this->writeText($x + $space, $y, $text);
    }

    public function render($outputFd = null): self
    {
        if ($outputFd === null) {
            $outputFd = STDOUT;
        }
        $redraw = $this->updateTerminalSize($outputFd);

        $params = $this->calcParams();

        $string = "";
        if ($redraw) {
            $string .= $this->escape("2J");
        }

        for($i = $params['yf']; $i < $params['yt']; $i++) {
            $string .= $this->offset($params['xo'], $params['yo'] + $i) . $this->optimizedLineOutput($i, $params['xf'], $params['xt']);
        }

        fputs($outputFd, $string);

        return $this;
    }

    public function clearOutput($outputFd = null): self
    {
        if ($outputFd === null) {
            $outputFd = STDOUT;
        }
        $string = "";
        $string .= $this->escape("2J") . $this->offset(0, 0);
        
        fputs($outputFd, $string);

        return $this;
    }

    private function optimizedLineOutput(int $y, int $xFrom, int $xTo)
    {
        $string = "";
        $currentAttrs = null;
        for ($i = $xFrom; $i < $xTo; $i++) {
            $charCode = $this->canvas[$y][$i][0];
            $attrs = $this->canvas[$y][$i][1];
            $string .= $this->changeAttrs($currentAttrs, $attrs);
            $currentAttrs = $attrs;
            $char = mb_chr($charCode, $this->outputEncoding);
            if (!is_string($char)) {
                $char = " ";
            }
            $string .= $char;
        }
        $string .= $this->changeAttrs($currentAttrs, null);

        $string .= $this->offset(0, 0);

        return $string;
    }

    private function changeAttrs(?array $oldAttrs, ?array $newAttrs): string
    {
        if ($newAttrs === null) {
            return ($oldAttrs === null) ? '' : $this->escape("0m");
        }

        $escape = "";

        foreach (self::ATTR_DESCRIPTOR as $index => $descriptor) {
            $method = $descriptor[0];
            $newAttr = $newAttrs[$index] ?? null;
            $oldAttr = isset($oldAttrs) ? ($oldAttrs[$index] ?? null) : null;
            if ($oldAttr !== $newAttr) {
                $escape .= $this->$method($newAttr);
            }
        }

        return $escape;
    }

    private function transformColor(?int $color, int $range1, int $range2, int $default): int
    {
        if ($color === null || $color < 0 || $color > 15) {
            return $default;
        }
        if ($color < 8) {
            return $color + $range1;
        }
        return $color - 8 + $range2;
    }

    private function color(?int $color): string
    {
        return $this->escape($this->transformColor($color, 30, 90, 39) . "m");
    }

    private function bgColor(?int $color): string
    {
        return $this->escape($this->transformColor($color, 40, 100, 49) . "m");
    }

    private function blink(?int $blinkMode): string
    {
        if ($blinkMode === null) {
            $blinkMode = 0;
        }
        switch ($blinkMode) {
            case 0:
                $code = 25;
                break;
            case 1:
                $code = 5;
                break;
            case 2:
                $code = 6;
                break;
            default:
                $code = 25;
                break;
        }
        return $this->escape($code . "m");
    }

    private function offset(int $x, int $y): string
    {
        return $this->escape(sprintf("%s;%sf", $y, $x));
    }

    private function escape(string $seq): string
    {
        return "\x1b[".$seq;
    }

    private function calcParams(): array
    {
        $params = [];

        if ($this->width <= $this->lastWrittenWidth) {
            $params['xo'] = intdiv($this->lastWrittenWidth - $this->width, 2);
            $params['xf'] = 0;
            $params['xt'] = $this->width;
        } else {
            $params['xo'] = 0;
            $params['xf'] = intdiv($this->width - $this->lastWrittenWidth, 2);
            $params['xt'] = $params['xf'] + $this->lastWrittenWidth;
        }

        if ($this->height <= $this->lastWrittenHeight) {
            $params['yo'] = intdiv($this->lastWrittenHeight - $this->height, 2);
            $params['yf'] = 0;
            $params['yt'] = $this->height;
        } else {
            $params['yo'] = 0;
            $params['yf'] = intdiv($this->height - $this->lastWrittenHeight, 2);
            $params['yt'] = $params['yf'] + $this->lastWrittenHeight;
        }

        return $params;
    }

    private function updateTerminalSize($outputFd): bool
    {
        list ($rows, $cols) = $this->getTerminalSize($outputFd);
        $redraw = false;
        if ($rows !== $this->lastWrittenHeight || $cols !== $this->lastWrittenWidth) {
            $redraw = true;
        }
        $this->lastWrittenHeight = $rows;
        $this->lastWrittenWidth = $cols;

        return $redraw;
    }

    private function getTerminalSize($outputFd): array
    {
        $rows = 25;
        $cols = 80;
        $data = @exec('stty size');
        if (is_string($data)) {
            $data = trim($data);
            if (preg_match('/^[0-9]+ [0-9]+$/', $data)) {
                list($rows, $cols) = explode(' ',  $data);
                $rows = (int) $rows;
                $cols = (int) $cols;
            }
            
        }
        return [$rows, $cols];
    }


}
