<?php

namespace App\Services\Ozon;

use App\Contracts\Ozon\SyncOzonCategories;
use App\Models\OzonCategory;
use App\Models\OzonCategoryAttribute;
use App\Models\OzonCategoryAttributeValue;
use App\Services\Ozon\Methods\OzonCategoriesAndAttributeMethods;
use Illuminate\Database\Eloquent\Model;

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
                $this->parseCategoryData($category);
            }
        }
    }

    /**
     * Проверка вложенных категории.
     *
     * @param $category
     * @return void
     */
    private function parseCategoryData($category) : void
    {
        $localCategory = $this->saveCategory($category);
        if (count($category->children) > 0) {
            $this->checkChildrenCategories($category->children, $localCategory->id);
        }
    }

    private function checkChildrenCategories($childrenCategories, $parentCategoryId) : void
    {
        foreach ($childrenCategories as $category) {
            $savedCategory = $this->saveCategory($category, $parentCategoryId);
            if (count($category->children) > 0) {
                $this->checkChildrenCategories($category->children, $savedCategory->id);
            }
            else {
                $this->checkCategoryAttributes($savedCategory);
            }
        }
    }

    /**
     * Сохранить категорию и вернуть сохраненные данные.
     *
     * @param $category
     * @param $parentCategory
     * @return Model
     */
    private function saveCategory($category, $parentCategory = null) : Model
    {
        return OzonCategory::query()->updateOrCreate([
            'source_id' => $category->category_id
        ], [
            'parent_id' => (!$parentCategory) ? null : $parentCategory,
            'name'      => $category->title,
        ]);
    }


    /**
     * Получить атрибуты категории.
     * @param $savedCategory
     * @return void
     */
    private function checkCategoryAttributes($savedCategory) : void
    {

        $attributeList = $this->methods->getListCategoryAttribute($savedCategory->source_id, "ALL", "DEFAULT");

        if (count($attributeList->result[0]->attributes) > 0) {
            $this->saveCategoryAttributes($savedCategory, $attributeList->result[0]->attributes);
        }
    }

    /**
     * Сохранение атрибутов категории.
     *
     * @param OzonCategory $savedCategory
     * @param $attributes
     * @return void
     */
    private function saveCategoryAttributes(OzonCategory $savedCategory, $attributes) : void
    {
        $data = [];
        foreach ($attributes as $attribute) {
            $data[] = OzonCategoryAttribute::query()->updateOrCreate([
                'ozon_category_id'      => $savedCategory->id,
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

    /**
     * Проверить подготовленное значение атрибута.
     *
     * @param $attributes
     * @return void
     */
    private function checkAttributeValues($attributes) : void
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

    /**
     * Сохранение значений атрибутов.
     *
     * @param $attributeSourceID
     * @param $values
     * @return void
     */
    private function saveAttributeValues($attributeSourceID, $values) : void
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
