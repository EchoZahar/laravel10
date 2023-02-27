<?php

namespace App\Services\Ozon;

use App\Contracts\Ozon\SyncOzonCategories;
use App\Models\OzonCategory;
use App\Models\OzonCategoryAttribute;
use App\Models\OzonCategoryAttributeValue;
use App\Services\Ozon\Methods\OzonCategoriesAndAttributeMethods;

class GetOzonCategoriesProcessingAndRewrite implements SyncOzonCategories
{
    public function __construct(protected OzonCategoriesAndAttributeMethods $methods) {}

    public function findNecessaryParentOzonCategory()
    {
        $parentOzonCategories = $this->methods->getOzonCategoryTree();
        $this->searchInParentCategories($parentOzonCategories);
    }

    /**
     * Получить авто категории из родительских.
     * @param $parentCategories
     */
    private function searchInParentCategories($parentCategories)
    {
        OzonCategoryAttributeValue::query()->truncate();
        foreach ($parentCategories->result as $category) {
            if (stristr($category->title, 'Автот')) {
                if (count($category->children) > 0) {
                    $this->checkChildrenCategories($category->children);
                }
                else {
                    $this->saveCategory($category);
                }
            }
        }
    }

    private function checkChildrenCategories($childrenCategories)
    {
        foreach ($childrenCategories  as $category) {
            if (count($category->children) > 0) {
                $this->checkChildrenCategories($category->children);
            }
            else {
                $this->saveCategory($category);
            }
        }
    }

    private function saveCategory($category)
    {
        $ozonCategory = OzonCategory::query()->updateOrCreate([
            'source_id' => $category->category_id
        ], [
            'name' => $category->title,
        ]);
        $this->checkCategoryAttributes($ozonCategory);
    }

    private function checkCategoryAttributes($ozonCategory)
    {
        $attributeList = $this->methods->getListCategoryAttribute($ozonCategory->source_id, "ALL", "DEFAULT");

        if (count($attributeList->result[0]->attributes) > 0) {
            $this->saveCategoryAttributes($ozonCategory, $attributeList->result[0]->attributes);
        }
    }

    private function saveCategoryAttributes(OzonCategory $ozonCategory, $attributes)
    {
        $data = [];
        foreach ($attributes as $attribute) {
            $data[] = OzonCategoryAttribute::query()->updateOrCreate([
                'ozon_category_id'      => $ozonCategory->id,
                'source_id'             => $attribute->id,
            ], [
                'name'                  => $attribute->name,
                'description'           => $attribute->description,
                'type'                  => $attribute->type,
                'is_collection'         => $attribute->is_collection,
                'is_required'           => $attribute->is_required,
                'group_id'              => $attribute->group_id,
                'group_name'            => $attribute->group_name,
                'dictionary_id'         => $attribute->dictionary_id,
                'is_aspect'             => $attribute->is_aspect,
                'category_dependent'    => $attribute->category_dependent,
            ]);
        }
        if (count($data) > 0) {
            $this->checkAttributeValues($data);
        }
    }

    private function checkAttributeValues($attributes)
    {
        foreach ($attributes as $attribute) {
            if ($attribute->dictionary_id > 0) {
                if (is_null(OzonCategoryAttributeValue::where('ozon_attribute_source_id', '=', $attribute->source_id)->first())) {
                    $values = $this->methods->getListCategoryAttributeValues($attribute->ozonCategory->source_id, $attribute->source_id);
                    $this->saveAttributeValues($attribute->source_id, $values);
                }
            }
        }
    }

    private function saveAttributeValues($attributeSourceID, $values)
    {
        if (count($values->result) > 0) {
            $data = [];
            foreach ($values->result as $value) {
                $data[] = [
                    'ozon_attribute_source_id'  => $attributeSourceID,
                    'source_id'	                => $value->id,
                    'value'	                    => $value->value,
                    'info'                      => $value->info,
                    'picture'                   => $value->picture,
                ];
            }
            foreach (collect($data)->chunk(1000) as $chunk) {
                OzonCategoryAttributeValue::query()->insert($chunk->toArray());
            }
        }
    }
}
