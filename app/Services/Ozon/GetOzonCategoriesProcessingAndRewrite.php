<?php

namespace App\Services\Ozon;

use App\Contracts\Ozon\SyncOzonCategories;
use App\Models\OzonCategory;
use App\Models\OzonCategoryAttribute;
use App\Models\OzonCategoryAttributeValue;
use App\Services\Ozon\Methods\OzonCategoriesAndAttributeMethods;
use Illuminate\Support\Facades\Log;

class GetOzonCategoriesProcessingAndRewrite implements SyncOzonCategories
{
    public function __construct(protected OzonCategoriesAndAttributeMethods $methods) {}

    public function findNecessaryParentOzonCategory()
    {
        $parentOzonCategories = $this->methods->getOzonCategoryTree();
        // truncate table 'ozon_category_attributes' and rewrite data.
        if (count($parentOzonCategories->result) > 0) OzonCategoryAttribute::query()->truncate();
        $this->searchInParentCategories($parentOzonCategories);
        $this->checkAttributeValues();
    }

    /**
     * Получить авто категории из родительских.
     * @param $parentCategories
     */
    private function searchInParentCategories($parentCategories)
    {
        foreach ($parentCategories->result as $category) {
            if (stristr($category->title, 'Авто')) {
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
            $data[] = [
                'source_id'             => $attribute->id,
                'ozon_category_id'      => $ozonCategory->id,
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
            ];
        }
        OzonCategoryAttribute::query()->insert($data);
    }

    private function checkAttributeValues()
    {
        $data = OzonCategoryAttribute::query()->with('ozonCategory')
            ->where('dictionary_id', '>', 0)
            ->select('id', 'ozon_category_id', 'source_id')
            ->get()
            ->unique('source_id');
        if ($data) {
            foreach ($data as $item) {
                Log::info('item: ozon source category id: ' . $item->ozonCategory->source_id . ', attribute source id' . $item->source_id);
                $values = $this->methods->getListCategoryAttributeValues($item->ozonCategory->source_id, $item->source_id);
                Log::info('Ozon response: ' . json_encode($values->result, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE) . PHP_EOL . __METHOD__);
                $this->saveAttributeValues($item->source_id, $values);
            }
        }
    }

    private function saveAttributeValues($attributeID, $values)
    {
        if (count($values->result) > 0) {
            $data = [];
            foreach ($values->result as $value) {
                $data[] = [
                    'source_attribute_id'    => $attributeID,
                    'source_id'	             => $value->id,
                    'value'	                 => $value->value,
                    'info'                   => $value->info,
                    'picture'                => $value->picture,
                ];
            }
            foreach (collect($data)->chunk(1000) as $chunk) {
                OzonCategoryAttributeValue::query()->insert($chunk->toArray());
            }
        }
    }
}
