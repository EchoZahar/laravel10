<?php

namespace App\Services\Ozon\Methods;

use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OzonCategoriesAndAttributeMethods
{
    /**
     * Дерево категории (все родительские категории).
     *
     * @link https://docs.ozon.ru/api/seller/#operation/CategoryAPI_GetCategoryTree
     * @api https://api-seller.ozon.ru/v2/category/tree
     * @return mixed
     */
    public function getOzonCategoryTree() : mixed
    {
        $response = Http::retry(3, 100, function ($e) {
            Log::warning('Ошибка в запросе API: ' . $e->getMessage() . ', method: ' . __METHOD__);
            return $e->getMessage() instanceof ConnectException;
        })->withHeaders([
            'Client-Id' => config('ozon.client_id'),
            'Api-Key'   => config('ozon.token'),
        ])->post('https://api-seller.ozon.ru/v2/category/tree', ["language" => "DEFAULT"]);

        return json_decode($response);
    }

    /**
     * Список характеристик категории
     *
     * @link https://docs.ozon.ru/api/seller/#operation/CategoryAPI_GetCategoryAttributesV3
     * @api https://api-seller.ozon.ru/v3/category/attribute
     * @param $sourceCategoryID - Идентификатор категории.
     * @param $attributeType    - Фильтр по характеристикам (Enum: "ALL" "REQUIRED" "OPTIONAL").
     * @param $language         - Язык в ответе: (Enum: "DEFAULT" "RU" "EN" "TR")
     * @return mixed
     */
    public function getListCategoryAttribute($sourceCategoryID, string $attributeType, string $language = null) : mixed
    {
        $response = Http::retry(3, 100, function ($e) {
            Log::warning('Ошибка в запросе API: ' . $e->getMessage() . ', method: ' . __METHOD__);
            return $e->getMessage() instanceof ConnectException;
        })->withHeaders([
            'Client-Id' => config('ozon.client_id'),
            'Api-Key'   => config('ozon.token'),
        ])->post('https://api-seller.ozon.ru/v3/category/attribute', [
            "attribute_type"    => $attributeType,
            "category_id"       => [$sourceCategoryID],
            "language"          => $language ?? "DEFAULT",
        ]);

        return json_decode($response);
    }

    /**
     * Справочник значений характеристики
     *
     * @link https://docs.ozon.ru/api/seller/#operation/CategoryAPI_DictionaryValueBatch
     * @api https://api-seller.ozon.ru/v2/category/attribute/values
     * @param $sourceCategoryID - Идентификатор категории.
     * @param $attributeID      - Идентификатор характеристики.
     * @param null $lang        - Язык в ответе Enum: "DEFAULT" "RU" "EN", по умолчанию русский.
     * @param null $lastValueID - Идентификатор справочника, с которого нужно начать ответ.
     * @param null $limit       - Количество значений в ответе: min = 1, max = 5000.
     * @return mixed
     */
    public function getListCategoryAttributeValues(int $sourceCategoryID, int $attributeID, $limit = null, $lang = null, $lastValueID = null) : mixed
    {
        $response = Http::retry(3, 100, function ($e) {
            Log::warning('Ошибка в запросе API: ' . $e->getMessage() . ', method: ' . __METHOD__);
            return $e->getMessage() instanceof ConnectException;
        })->withHeaders([
            'Client-Id' => config('ozon.client_id'),
            'Api-Key'   => config('ozon.token'),
        ])->post('https://api-seller.ozon.ru/v2/category/attribute/values', [
            'attribute_id'  => $attributeID,
            'category_id'   => $sourceCategoryID,
            'last_value_id' => $lastValueID ?? 0,
            'limit'         => $limit ?? 5000,
            'language'      => $lang ?? "DEFAULT"
        ]);

        return json_decode($response);
    }
}
