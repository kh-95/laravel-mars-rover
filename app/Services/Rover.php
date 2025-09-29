<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * Class Rover
 *
 * Handles rover state and command processing.
 *
 * @package App\Services
 */
class Rover
{
    /** @var int */
    private int $x;
    /** @var int */
    private int $y;
    /** @var string One of N,S,E,W */
    private string $direction;

    private int $width;
    private int $height;

    private const DIRECTIONS = ['N', 'E', 'S', 'W'];

    /**
     * Rover constructor.
     *
     * @param int $x
     * @param int $y
     * @param string $direction
     * @param int $width
     * @param int $height
     */
    public function __construct(int $x, int $y, string $direction, int $width = 10, int $height = 10)
    {
        $direction = strtoupper($direction);

        if (!in_array($direction, self::DIRECTIONS, true)) {
            throw new InvalidArgumentException('Invalid direction. Must be one of: N, S, E, W.');
        }

        if ($width < 1 || $height < 1) {
            throw new InvalidArgumentException('Grid size must be positive integers.');
        }

        $this->width = $width;
        $this->height = $height;

        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            throw new InvalidArgumentException("Initial position must be within grid bounds (0..width-1, 0..height-1).");
        }

        $this->x = $x;
        $this->y = $y;
        $this->direction = $direction;
    }

    /**
     * Process a sequence of commands. Valid commands: F,B,L,R
     *
     * @param string $commands
     * @return void
     */
    public function processCommands(string $commands): void
    {
        $commands = strtoupper($commands);

        if ($commands === '') {
            return;
        }

        if (!preg_match('/^[FBLR]*$/', $commands)) {
            throw new InvalidArgumentException('Commands string contains invalid characters. Only F,B,L,R allowed.');
        }

        $len = mb_strlen($commands);
        for ($i = 0; $i < $len; $i++) {
            $c = $commands[$i];
            switch ($c) {
                case 'F':
                    $this->move(1);
                    break;
                case 'B':
                    $this->move(-1);
                    break;
                case 'L':
                    $this->turnLeft();
                    break;
                case 'R':
                    $this->turnRight();
                    break;
            }
        }
    }

    /**
     * Move forward (step=1) or backward (step=-1) relative to current direction.
     *
     * If the move would go out of bounds, ignore it.
     *
     * @param int $step
     * @return void
     */
    private function move(int $step = 1): void
    {
        $nx = $this->x;
        $ny = $this->y;

        switch ($this->direction) {
            case 'N':
                $ny += $step;
                break;
            case 'S':
                $ny -= $step;
                break;
            case 'E':
                $nx += $step;
                break;
            case 'W':
                $nx -= $step;
                break;
        }

        // check bounds: x in [0, width-1], y in [0, height-1]
        if ($nx >= 0 && $nx < $this->width && $ny >= 0 && $ny < $this->height) {
            $this->x = $nx;
            $this->y = $ny;
        }
        // else ignore invalid move
    }

    /**
     * Turn left 90 degrees.
     */
    private function turnLeft(): void
    {
        $idx = array_search($this->direction, self::DIRECTIONS, true);
        // left is -1 modulo 4
        $newIdx = ($idx + 3) % 4;
        $this->direction = self::DIRECTIONS[$newIdx];
    }

    /**
     * Turn right 90 degrees.
     */
    private function turnRight(): void
    {
        $idx = array_search($this->direction, self::DIRECTIONS, true);
        $newIdx = ($idx + 1) % 4;
        $this->direction = self::DIRECTIONS[$newIdx];
    }

    /**
     * Get current X coordinate.
     *
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * Get current Y coordinate.
     *
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * Get current direction.
     *
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * Return formatted final position "X,Y,DIRECTION"
     *
     * @return string
     */
    public function finalPositionString(): string
    {
        return sprintf('%d,%d,%s', $this->x, $this->y, $this->direction);
    }
}
