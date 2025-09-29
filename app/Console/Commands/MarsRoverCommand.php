<?php

namespace App\Console\Commands;

use App\Services\Rover;
use Illuminate\Console\Command;
use InvalidArgumentException;

class MarsRoverCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'mars:rover 
                            {x : Starting X coordinate (integer)}
                            {y : Starting Y coordinate (integer)}
                            {direction : Starting direction (N|S|E|W)}
                            {commands : Command sequence string (e.g., "FFRFF")}
                            {--width=10 : Grid width (default: 10)}
                            {--height=10 : Grid height (default: 10)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate a Mars rover navigating a grid plateau.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $xRaw = $this->argument('x');
        $yRaw = $this->argument('y');
        $direction = strtoupper($this->argument('direction'));
        $commands = $this->argument('commands');

        $widthRaw = $this->option('width');
        $heightRaw = $this->option('height');

        if (!ctype_digit((string)$xRaw) && !is_numeric($xRaw)) {
            $this->error('X must be an integer.');
            return 1;
        }
        if (!ctype_digit((string)$yRaw) && !is_numeric($yRaw)) {
            $this->error('Y must be an integer.');
            return 1;
        }

        $x = (int)$xRaw;
        $y = (int)$yRaw;

        if (!in_array($direction, ['N','S','E','W'], true)) {
            $this->error('Direction must be one of: N, S, E, W.');
            return 1;
        }

        if ($commands !== '' && !preg_match('/^[FBLR]*$/i', $commands)) {
            $this->error('Commands string may contain only F, B, L, R characters.');
            return 1;
        }

        if (!ctype_digit((string)$widthRaw) && !is_numeric($widthRaw)) {
            $this->error('Width must be a positive integer.');
            return 1;
        }
        if (!ctype_digit((string)$heightRaw) && !is_numeric($heightRaw)) {
            $this->error('Height must be a positive integer.');
            return 1;
        }

        $width = (int)$widthRaw;
        $height = (int)$heightRaw;

        if ($width < 1 || $height < 1) {
            $this->error('Grid width and height must be at least 1.');
            return 1;
        }

        if ($width > 100 || $height > 100) {
            $this->error('Grid width and height maximum is 100.');
            return 1;
        }

        if ($x < 0 || $x >= $width) {
            $this->error("Initial X must be within grid bounds (0 to " . ($width - 1) . ").");
            return 1;
        }
        if ($y < 0 || $y >= $height) {
            $this->error("Initial Y must be within grid bounds (0 to " . ($height - 1) . ").");
            return 1;
        }

        try {
            $rover = new Rover($x, $y, $direction, $width, $height);
            $rover->processCommands($commands);

            $this->info('Final position: ' . $rover->finalPositionString());
            return 0;
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());
            return 1;
        } catch (\Throwable $e) {
            $this->error('Unexpected error: ' . $e->getMessage());
            return 1;
        }
    }
}
