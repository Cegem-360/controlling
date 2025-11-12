<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\GoogleAnalitycs\OrderByType;
use App\Models\AnalyticsSettings;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnalyticsSettings>
 */
final class AnalyticsSettingsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dimensions = [['name' => 'city'], ['name' => 'country'], ['name' => 'deviceCategory']];
        $metrics = [['name' => 'activeUsers'], ['name' => 'newUsers'], ['name' => 'sessions']];
        $selectedDimensions = fake()->randomElements($dimensions, count: 2);
        $selectedMetrics = fake()->randomElements($metrics, count: 2);
        $selectedOrderByType = fake()->randomElement(OrderByType::class);
        if ($selectedOrderByType === OrderByType::DIMENSION) {
            $selectedOrderBy = fake()->randomElement($selectedDimensions)['name'];
        } else {
            $selectedOrderBy = fake()->randomElement($selectedMetrics)['name'];
        }

        return [
            'dimensions' => $selectedDimensions,
            'metrics' => $selectedMetrics,
            'order_by_type' => $selectedOrderByType,
            'order_by' => $selectedOrderBy,
            'order_by_direction' => fake()->randomElement(['asc', 'desc']),
        ];
    }
}
