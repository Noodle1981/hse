<?php

namespace App\Services;

use App\Contracts\EppExchangeContract;
use App\Models\EppReplacementAlert;
use Illuminate\Support\Facades\Log;

class MockEppExchangeService implements EppExchangeContract
{
    protected array $products;
    protected array $providers;

    public function __construct()
    {
        $this->loadMockData();
    }

    /**
     * MOCK WEBHOOK: Simula la confirmación de una orden comprada en EPP Exchange.
     * Retorna el payload final que mandaría la plataforma B, incluyendo el ID
     * de la orden de compra y trazabilidad base (lote ficticio).
     */
    public function simulateOrderConfirmation(\App\Models\EppReplacementAlert $alert): array
    {
        // Generar IDs aleatorios para dar realismo a la simulación
        $fakeOrderId = 'EX-' . now()->year . '-' . str_pad(rand(1000, 9999), 5, '0', STR_PAD_LEFT);
        $proveedor = collect($this->getProviders())->random();

        return [
            'exchange_order_id'            => $fakeOrderId,
            'lote'                         => 'LOT-SIM-' . strtoupper(substr(md5($fakeOrderId), 0, 8)),
            'fecha_fabricacion'            => now()->subMonths(rand(1,6))->toDateString(),
            'fecha_vencimiento_fabricante' => now()->addYears(rand(2,5))->toDateString(),
            'proveedor'                    => $proveedor,
            'cantidad'                     => $alert->cantidad_requerida,
            'timestamp'                    => now()->toIso8601String(),
        ];
    }

    protected function loadMockData()
    {
        $productsFile = storage_path('app/mock_exchange/products.json');
        $providersFile = storage_path('app/mock_exchange/providers.json');

        $this->products = file_exists($productsFile) ? json_decode(file_get_contents($productsFile), true)['products'] ?? [] : [];
        $this->providers = file_exists($providersFile) ? json_decode(file_get_contents($providersFile), true)['providers'] ?? [] : [];
    }

    public function getProductsByTipo(?string $tipo): array
    {
        if (!$tipo) {
            return $this->products;
        }

        // El tipo en EPP Exchange puede no ser exactamente igual al de nuestra BD
        // Hacemos una búsqueda simple por "contains"
        return array_values(array_filter($this->products, function ($product) use ($tipo) {
            return stripos($product['tipo'], $tipo) !== false 
                || stripos($product['nombre'], $tipo) !== false;
        }));
    }

    public function getProviders(?string $productId = null): array
    {
        if (!$productId) {
            return $this->providers;
        }

        $product = collect($this->products)->firstWhere('exchange_product_id', $productId);
        
        if (!$product || !isset($product['proveedores_disponibles'])) {
            return [];
        }

        $providerIds = $product['proveedores_disponibles'];
        return array_values(array_filter($this->providers, fn($p) => in_array($p['id'], $providerIds)));
    }

    public function submitAlert(EppReplacementAlert $alert): array
    {
        Log::info("MockEppExchangeService: Enviando alerta de tipo {$alert->trigger_type} para item {$alert->epp_item_id}");

        // 1. Encontrar productos compatibles en el catálogo de Exchange
        $eppType = $alert->eppItem->tipo ?? '';
        $compatibleProducts = collect($this->getProductsByTipo($eppType))
            ->take(3) // Sugerir max 3 productos
            ->values()
            ->all();

        // 2. Extraer IDs de proveedores únicos de esos productos
        $providerIds = collect($compatibleProducts)
            ->pluck('proveedores_disponibles')
            ->flatten()
            ->unique();

        // 3. Traer la info de esos proveedores
        $suggestedProviders = collect($this->providers)
            ->whereIn('id', $providerIds)
            ->values()
            ->all();

        // 4. Simular la respuesta de cotización sugerida
        return [
            'status' => 'success',
            'exchange_quote_id' => 'MOCK-QUOTE-' . date('Ymd-His') . '-' . rand(1000, 9999),
            'timestamp' => now()->toIso8601String(),
            'message' => 'Alerta recibida. Aquí hay proveedores que venden elementos de tipo ' . $eppType,
            'suggested_products' => $compatibleProducts,
            'suggested_providers' => $suggestedProviders,
        ];
    }
}
