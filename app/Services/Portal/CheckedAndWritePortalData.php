<?php

namespace App\Services\Portal;

use App\Models\PortalCategory;
use App\Models\PortalNomenclature;
use App\Models\PortalStock;
use App\Models\PortalWarehouse;
use Illuminate\Support\Str;

class CheckedAndWritePortalData
{
    public function checkNomenclatureData($data) : void
    {
        $data = json_decode(file_get_contents($data));
        $this->prepareNomenclaturesData($data->nomenclatures);
    }

    public function checkStocksData($data) : void
    {
        $data = json_decode(file_get_contents($data));
        $this->prepareStocksData($data->products);
    }

    public function checkWarehousesData($data) : void
    {
        $data = json_decode(file_get_contents($data));
        $prepare = [];
        foreach ($data->warehouses as $warehouse)
        {
            $prepare[] = [
                'warehouse_id'          => (int)$warehouse->warehouse_id,
                'name'                  => $warehouse->wh_name,
                'type'                  => $warehouse->wh_type,
                'is_active'             => (int)$warehouse->is_active,
                'supplier_working_name' => $warehouse->supplier_working_name,
            ];
        }
        foreach ($prepare as $item) {
            PortalWarehouse::updateOrCreate([
                'warehouse_id' => $item['warehouse_id'],
            ], [
                'name'                  => $item['name'],
                'type'                  => $item['type'],
                'is_active'             => $item['is_active'],
                'supplier_working_name' => $item['supplier_working_name'],
            ]);
        }
    }

    /**
     * @param $data
     */
    private function prepareNomenclaturesData($data) : void
    {
        $prepare = [];
        $collection = collect($data);
        foreach ($collection->chunk(1000) as $chunk) {
            foreach ($chunk as $item) {
                $prepare[] = [
                    'brand_name'            => $item->brand_name,
                    'article'               => $item->article,
                    'nom_name'              => trim($item->nom_name, " \n\r\t\v\x00"),
                    'measure_id'            => (int)$item->measure_id,
                    'certificate'           => $item->certificate,
                    'size_length'           => (float)$item->size_length,
                    'size_width'            => (float)$item->size_width,
                    'size_height'           => (float)$item->size_height,
                    'net_weight'            => (float)$item->net_weight,
                    'gross_weight'          => (float)$item->gross_weight,
                    'volume'                => (float)$item->volume,
                    'image'                 => str_replace('data/', 'https://absel.ru/static/img/data/', $item->image),
                    'description'           => trim($item->description, " \n\r\t\v\x00"),
                    'mult_sale'             => (int)$item->mult_sale,
                    'mult_complect'         => (int)$item->mult_complect,
                    'mult_pack'             => (int)$item->mult_pack,
                    'nomenclature_timing'   => $item->nomenclature_timing,
                ];
            }
        }
        $this->rewriteNomenclature($prepare);
    }

    private function rewriteNomenclature($prepareData) : void
    {
        if (count($prepareData) > 0) {
            foreach (collect($prepareData)->chunk(1000) as $chunk) {
                PortalNomenclature::query()->insert($chunk->toArray());
            }
        }
    }

    private function prepareStocksData($stocksData) : void
    {
        $prepare = [];
        $collection = collect($stocksData);

        foreach ($collection->chunk(1000) as $chunk) {
            foreach ($chunk as $item) {
                $prepare[] = [
                    'warehouse_id'  => (int)$item->warehouse_id,
                    'brand_name'    => $item->brand_name,
                    'article'       => $item->article,
                    'nom_name'      => $item->nom_name,
                    'quantity'      => (int)$item->quantity,
                    'price'         => (int)$item->price,
                    'old_price'     => (int)$item->old_price,
                ];
            }
        }
        $this->rewriteStocksData($prepare);
    }

    private function rewriteStocksData($prepareData) : void
    {
        if (count($prepareData) > 0) {
            foreach (collect($prepareData)->chunk(1000) as $chunk) {
                PortalStock::query()->insert($chunk->toArray());
            }
        }
    }


    public function checkCategoriesData($portalCategories): void
    {
        $data = json_decode(file_get_contents($portalCategories[0]));
        $portalCategories = [];
        if (count($data->categories) > 0) {
            PortalCategory::query()->truncate();
            foreach ($data->categories as $category) {
                if (!is_null($category->ozon_type_id) and !is_null($category->ozon_type_name)) {
                    $portalCategories[] = [
                        'nomenclature_type_id'      => (int)$category->nomenclature_type_id,
                        'parent_id'                 => (int)$category->parent_id,
                        'type_name'                 => $category->type_name,
                        'ozon_type_id'              => (int)$category->ozon_type_id,
                        'ozon_type_name'            => $category->ozon_type_name,
                    ];
                }
            }
            foreach (collect($portalCategories)->chunk(1000) as $chunk) {
                PortalCategory::query()->insert($chunk->toArray());
            }
        }
    }
}
