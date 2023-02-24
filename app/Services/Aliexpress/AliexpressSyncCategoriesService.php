<?php

namespace App\Services\Aliexpress;

use App\Contracts\Aliexpress\SyncAliexpressCategories;
use App\Models\AliexpressCategory;
use App\Models\AliexpressCategoryAttribute;
use App\Models\AliexpressCategoryAttributeValue;
use App\Services\Aliexpress\Methods\AliexpressCategoryMethods;

class AliexpressSyncCategoriesService implements SyncAliexpressCategories
{
    public function __construct(protected AliexpressCategoryMethods $method) {}
    public function getParentsCategoriesAndSynchronization()
    {
        $parentCategories = $this->method->getParentCategories();
        foreach ($parentCategories->categories as $category) {
            if (stristr($category->name, 'Авто')) {
                if (count($category->children_ids) > 0) {
                    $this->checkChildrenCategories($this->method->getChildrenCategories($category->children_ids));
                }
            }
        }
    }

    private function checkChildrenCategories($childrenCategories)
    {
        foreach ($childrenCategories->categories as $category) {
            if (count($category->children_ids) > 0) {
                foreach (collect($category->children_ids)->chunk(20) as $chunk) {
                    $response = $this->method->getChildrenCategories($chunk->toArray());
                    if (count($response->categories) > 0) {
                        $this->checkChildrenCategories($response);
                    }
                }
            }
            else {
                $this->saveCategory($category);
            }
        }
    }

    private function saveCategory($category)
    {
        $localCategory = AliexpressCategory::updateOrCreate([
            'source_id' => (int)$category->id,
        ], [
            'name'          => $category->name,
            'is_visible'    => (bool)$category->is_visible,
        ]);
        if (count($category->sku_properties) > 0) {
            $this->checkAttribute($category->sku_properties, $localCategory, true);
        }
        else if (count($category->properties) > 0) {
            $this->checkAttribute($category->properties, $localCategory, false);
        }
    }

    private function checkAttribute($attributes, $category, bool $isSku)
    {
        foreach ($attributes as $attribute) {
            $this->saveAttribute($attribute, $category, $isSku);
        }
    }

    private function saveAttribute($attribute, $category, $isSku)
    {
        $localAttribute = AliexpressCategoryAttribute::updateOrCreate([
            'source_id' => (int)$attribute->id,
        ], [
            'aliexpress_category_id'    => $category->id,
            'is_sku'                    => $isSku,
            'name'                      => $attribute->name,
            'is_required'               => $attribute->is_required,
            'is_key'                    => $attribute->is_key,
            'is_brand'                  => $attribute->is_brand,
            'is_enum_prop'              => $attribute->is_enum_prop,
            'is_multi_select'           => $attribute->is_multi_select,
            'is_input_prop'             => $attribute->is_input_prop,
            'has_unit'                  => $attribute->has_unit,
            'has_customized_pic'        => $attribute->has_customized_pic,
            'has_customized_name'       => $attribute->has_customized_name,
            'units'                     => $attribute->units,
        ]);

        $this->checkAttributeValue($category, $localAttribute, $isSku);
    }

    private function checkAttributeValue($category, $attribute, $isSku)
    {
        $response = $this->method->getCategoryAttributeValue($category->source_id, $attribute->source_id, $isSku);
        if (count($response->values) > 0) {
            AliexpressCategoryAttributeValue::updateOrCreate([
                'attribute_id' => $attribute->id,
            ], [
                'values'    => $response->values,
                'error'     => $response->error,
            ]);
        }
    }
}
