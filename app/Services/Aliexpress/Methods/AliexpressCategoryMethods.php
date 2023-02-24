<?php

namespace App\Services\Aliexpress\Methods;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class AliexpressCategoryMethods
{
    public function getParentCategories()
    {
        $response = Http::retry(3, 100, function ($exception) {
            return $exception instanceof ConnectionException;
        })->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-auth-token' => config('aliexpress.token'),
        ])->post('https://openapi.aliexpress.ru/api/v1/categories/top',null);

        return json_decode($response);
    }

    public function getChildrenCategories(array $ids)
    {
        $response = Http::retry(3, 100, function ($exception) {
            return $exception instanceof ConnectionException;
        })->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-auth-token' => config('aliexpress.token'),
        ])->post('https://openapi.aliexpress.ru/api/v1/categories/get', [
            'ids' => array_values($ids)
        ]);

        return json_decode($response->body());
    }

    public function getCategoryAttributeValue(int $categoryID, int $propertyID, bool $isSku)
    {
        $response = Http::retry(3, 100, function ($exception) {
            return $exception instanceof ConnectionException;
        })->timeout(300)->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-auth-token' => config('aliexpress.token'),
        ])->post('https://openapi.aliexpress.ru/api/v1/categories/values-dictionary', [
            'category_id'       => $categoryID,
            'property_id'       => $propertyID,
            'is_sku_property'   => $isSku,
        ]);

        return json_decode($response);
    }
}
