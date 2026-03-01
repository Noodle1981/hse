<?php

namespace App\Contracts;

use App\Models\EppReplacementAlert;

interface EppExchangeContract
{
    /**
     * Obtiene productos del catálogo de EPP Exchange, opcionalmente filtrados por grupo.
     * @param string|null $tipo El tipo de elemento, ej: "Cabeza", "Auditiva"
     * @return array
     */
    public function getProductsByTipo(?string $tipo): array;

    /**
     * Obtiene los proveedores disponibles para un producto específico o todos los proveedores.
     * @param string|null $productId
     * @return array
     */
    public function getProviders(?string $productId = null): array;

    /**
     * Envía una alerta de reposición al mercado (EPP Exchange) y devuelve 
     * proveedores sugeridos y los IDs de los productos de catálogo compatibles.
     * @param EppReplacementAlert $alert
     * @return array
     */
    public function submitAlert(EppReplacementAlert $alert): array;
}
