<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\Rover;
use InvalidArgumentException;

class RoverTest extends TestCase
{
    /**
     * A basic unit test example.
     */
  public function test_initial_position_and_direction()
    {
        $r = new Rover(0, 0, 'N', 10, 10);
        $this->assertEquals(0, $r->getX());
        $this->assertEquals(0, $r->getY());
        $this->assertEquals('N', $r->getDirection());
    }

    public function test_move_forward_north()
    {
        $r = new Rover(1, 1, 'N', 5, 5);
        $r->processCommands('F');
        $this->assertEquals(1, $r->getX());
        $this->assertEquals(2, $r->getY());
    }

    public function test_move_backward_south()
    {
        $r = new Rover(2, 2, 'S', 5, 5);
        $r->processCommands('B');
        $this->assertEquals(2, $r->getX());
        $this->assertEquals(1, $r->getY());
    }

    public function test_turns()
    {
        $r = new Rover(0, 0, 'N', 3, 3);
        $r->processCommands('R'); // N->E
        $this->assertEquals('E', $r->getDirection());
        $r->processCommands('R'); // E->S
        $this->assertEquals('S', $r->getDirection());
        $r->processCommands('L'); // S->E
        $this->assertEquals('E', $r->getDirection());
    }

    public function test_ignore_moves_out_of_bounds()
    {
        $r = new Rover(0, 0, 'S', 3, 3); 
        $r->processCommands('F'); 
        $this->assertEquals(0, $r->getX());
        $this->assertEquals(0, $r->getY());
    }

    public function test_grid_1x1_only_turns_allowed()
    {
        $r = new Rover(0, 0, 'N', 1, 1);
        $r->processCommands('FFRFFLBB');
        $this->assertEquals(0, $r->getX());
        $this->assertEquals(0, $r->getY());
        $this->assertContains($r->getDirection(), ['N','E','S','W']);
    }

    public function test_large_command_sequence()
    {
        $r = new Rover(0, 0, 'E', 100, 100);
        $commands = str_repeat('F', 150); 
        $r->processCommands($commands);
        $this->assertEquals(99, $r->getX()); 
    }

    public function test_invalid_direction_throws()
    {
        $this->expectException(InvalidArgumentException::class);
        new Rover(0, 0, 'A', 5, 5);
    }

    public function test_invalid_commands_string_throw_on_process()
    {
        $this->expectException(InvalidArgumentException::class);
        $r = new Rover(0, 0, 'N', 5, 5);
        $r->processCommands('FX'); 
    }
}
