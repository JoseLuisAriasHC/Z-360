<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Talla;
use App\Models\VariantSize;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        
        if ($products->isEmpty()) {
            $this->command->warn('No se encontraron productos. Ejecuta el ProductSeeder primero.');
            return;
        }

        $colorIds = Color::pluck('id')->toArray();
        $tallaIds = Talla::pluck('id')->toArray();

        if (empty($colorIds) || empty($tallaIds)) {
            $this->command->warn('No se encontraron colores o tallas. Ejecuta los seeders correspondientes.');
            return;
        }

        foreach ($products as $product) {
            // Crear entre 1 y 5 variantes por producto
            $numVariants = rand(1, 5);
            
            // Asegurar que no se repitan colores en un mismo producto
            $coloresUsados = [];
            
            for ($i = 0; $i < $numVariants; $i++) {
                // Seleccionar un color que no se haya usado
                $availableColors = array_diff($colorIds, $coloresUsados);
                
                if (empty($availableColors)) {
                    break; // No hay más colores disponibles
                }
                
                $colorId = fake()->randomElement($availableColors);
                $coloresUsados[] = $colorId;

                // Precio base entre 30 y 150 euros
                $precio = fake()->randomFloat(2, 30, 150);

                // Decidir si tiene descuento (30% de probabilidad)
                $tieneDescuento = fake()->boolean(30);
                $descuento = null;
                $descuentoDesde = null;
                $descuentoHasta = null;

                if ($tieneDescuento) {
                    $descuento = fake()->randomElement([5, 10, 15, 20, 25, 30, 35, 40, 50]);
                    
                    // Fecha de inicio del descuento (puede ser en el pasado o futuro)
                    $descuentoDesde = Carbon::now()->subDays(rand(-30, 30));
                    
                    // Fecha de fin del descuento (entre 7 y 60 días después del inicio)
                    $descuentoHasta = (clone $descuentoDesde)->addDays(rand(7, 60));
                }

                // Crear la variante
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'color_id' => $colorId,
                    'precio' => $precio,
                    'descuento' => $descuento,
                    'descuento_desde' => $descuentoDesde,
                    'descuento_hasta' => $descuentoHasta,
                    'imagen_principal' => null, // Puedes agregar lógica para imágenes si lo necesitas
                    'imagen_principal_jpeg' => null,
                ]);

                // Crear entre 5 y 18 tallas para esta variante
                $numTallas = rand(5, 18);
                
                // Seleccionar tallas aleatorias sin repetir
                $tallasSeleccionadas = fake()->randomElements($tallaIds, $numTallas);
                
                foreach ($tallasSeleccionadas as $tallaId) {
                    VariantSize::create([
                        'product_variant_id' => $variant->id,
                        'talla_id' => $tallaId,
                        'stock' => rand(0, 50), // Stock aleatorio entre 0 y 50
                        // El SKU se genera automáticamente en el modelo
                    ]);
                }

                $this->command->info("Variante creada para producto {$product->id} con {$numTallas} tallas");
            }
        }

        $totalVariants = ProductVariant::count();
        $totalSizes = VariantSize::count();
        
        $this->command->info("✓ Creadas {$totalVariants} variantes de productos");
        $this->command->info("✓ Creadas {$totalSizes} tallas de variantes");
    }
}