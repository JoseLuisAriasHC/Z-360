<?php

namespace Database\Factories;

use App\Enums\AlturaSuelaProduct;
use App\Enums\CierreProducto;
use App\Enums\Genero;
use App\Enums\TipoProducto;
use App\Models\Product;
use App\Models\Marca;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Asegurarse de que exista al menos una marca para las claves foráneas
        $marcaId = Marca::inRandomOrder()->value('id');

        return [
            'nombre' => fake()->words(2, true) . ' Zapatilla ' . fake()->unique()->randomNumber(3),
            'marca_id' => $marcaId ?: Marca::factory(),
            'tipo' => fake()->randomElement(TipoProducto::cases()),
            'genero' => fake()->randomElement(Genero::cases()),
            'descripcion' => fake()->paragraph(),
            'cierre' => fake()->randomElement(CierreProducto::cases()),
            'altura_suela' => fake()->randomElement(AlturaSuelaProduct::cases()),
            'plantilla' => fake()->word() . ' Foam',
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            $product->detail()->create([
                'parte_superior' => $this->faker->optional(0.8, null)->sentence(4),
                'parte_inferior' => $this->faker->optional(0.8, null)->sentence(4),
                'suela'          => $this->faker->optional(0.8, null)->sentence(4),
                'cuidados'       => $this->faker->optional(0.8, null)->paragraph(2),
            ]);
        });
    }
}
