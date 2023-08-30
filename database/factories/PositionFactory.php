<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Position>
 */

class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $positionIndex = 0;
    public function definition(): array
    {

        $positions = ['Teacher I', 'Teacher II','Administrator','House Keeping','HR'];
        $currentPosition= $positions[$this->positionIndex];
        $this->positionIndex = ($this->positionIndex + 1) % count($positions);
        return [
            'name'=>$currentPosition
        ];
    }
}
