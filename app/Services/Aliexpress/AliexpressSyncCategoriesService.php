<?php

namespace App\Services\Aliexpress;

use App\Contracts\Aliexpress\SyncAliexpressCategories;
use App\Models\AliCategory;
use App\Models\AliCategoryAttribute;
use App\Models\AliCategoryAttributeValue;
use App\Services\Aliexpress\Methods\AliexpressCategoryMethods;

class AliexpressSyncCategoriesService implements SyncAliexpressCategories
{
    public function __construct(protected AliexpressCategoryMethods $method) {}

    public function getParentsCategoriesAndSynchronization() : void
    {
        $parentCategories = $this->method->getParentCategories();
        if (isset($parentCategories->categories)) {
            foreach ($parentCategories->categories as $category) {
                if (stristr($category->name, 'Авто')) {
                    $this->getCategoryTree($category);
                }
            }
        }
    }

    private function getCategoryTree($category, $parentID = null) : void
    {
        $localCategory = $this->saveCategory($category, $parentID);

        if (count($category->children_ids) > 0) {
            $this->checkChildrenCategories($category->children_ids, $localCategory->id);
        }

        if (count($category->sku_properties) > 0) {
            $this->checkAttribute($category->sku_properties, $localCategory, true);
        }

        if (count($category->properties) > 0) {
            $this->checkAttribute($category->properties, $localCategory, false);
        }

    }

    private function checkChildrenCategories($childrenCategories, $parentID = null)
    {
        $data = [];
        foreach (collect($childrenCategories)->chunk(20) as $chunk) {
            $response = $this->method->getChildrenCategories($chunk->toArray());
            if (count($response->categories) > 0) {
                foreach ($response->categories as $category) {
                    $data[] = $category;
                }
            }
        }
        foreach ($data as $category) {
            $this->getCategoryTree($category, $parentID);
        }
    }


    private function saveCategory($category, $parentID = null)
    {

        if (isset($category->is_visible)) {
            $is_visible = $category->is_visible;
        }
        else {
            $is_visible = false;
        }

        return AliCategory::updateOrCreate([
            'source_id' => (int)$category->id,
        ], [
            'parent_id'     => $parentID,
            'name'          => $category->name,
            'is_visible'    => $is_visible,
        ]);
    }

    private function checkAttribute($attributes, $localCategory, bool $isSku)
    {
        foreach ($attributes as $attribute) {
            $this->saveAttribute($attribute, $localCategory, $isSku);
        }

//        $this->checkAttributeValues($localCategory->source_id, $data, $isSku);
    }

    private function saveAttribute($attribute, $localCategory, $isSku) : void
    {
        $localAttribute = AliCategoryAttribute::updateOrCreate([
            'source_id'         => (int)$attribute->id,
        ], [
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

        $localAttribute->aliCategories()->sync($localCategory->id);

//        return $localAttribute;

//        $this->checkAttributeValue($localCategory, $localAttribute, $isSku);
    }

    private function checkAttributeValues($aliexpressCategoryID, $localAttributes, $isSku) : void
    {
        foreach ($localAttributes as $attribute) {
            $response = $this->method->getCategoryAttributeValue($aliexpressCategoryID, $attribute->source_id, $isSku);
            if (count($response->values) > 0) {
                if  (is_null(AliCategoryAttributeValue::query()->where('ali_attribute_id', '=', $attribute->id)->first())) {
                    $attributeValue = AliCategoryAttributeValue::query()->updateOrCreate([
                        'ali_attribute_id' => $attribute->id,
                    ], [
                        'values'    => $response->values,
                        'error'     => $response->error,
                    ]);
                    $attribute->aliAttributeValues()->sync($attributeValue);
                }
            }
        }
    }
}
