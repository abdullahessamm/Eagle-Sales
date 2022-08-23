<?php

namespace App\Services\ItemsExcelParser;

use App\Rules\ArabicLetters;
use Shuchkin\SimpleXLSX;
use Illuminate\Support\Facades\Validator;

class ItemsExcelParser
{
    private SimpleXLSX $xlsx;
    private array $sheets;
    private array $items;
    private array $uoms;
    private array $vars;

    public function __construct(string $file)
    {
        $this->xlsx = SimpleXLSX::parse($file);
        $this->check();
        $this->parseItems();
        $this->validateItems();
        $this->parseUoms();
        // $this->parseVars();
    }

    private function check()
    {
        if (! $this->xlsx) {
            throw new \Exception('Invalid Excel file');
        }
        
        $this->sheets = array_map('strtolower', $this->xlsx->sheetNames());
        if (! in_array('items', $this->sheets)) {
            throw new \Exception('Excel file must contain sheet named \'items\'');
        }
    }

    private function parseItems()
    {
        $itemSheetIndex = array_search('items', $this->sheets);
        $items = $this->xlsx->rows($itemSheetIndex);
        array_shift($items); // unset all headers

        $this->items = [];
        foreach ($items as $item) {
            $this->items[] = [
                'index'                 => $item[0] ?? null,
                'name'                  => $item[1] ?? null,
                'ar_name'               => $item[2] ?? null,
                'brand'                 => $item[3] ?? null,
                'ar_brand'              => $item[4] ?? null,
                'barcode'               => $item[5] ?? null,
                'description'           => $item[6] ?? null,
                'ar_description'        => $item[7] ?? null,
                'total_available_count' => $item[8] ?? null,
                'price'                 => $item[9] ?? null,
                'default_uom_name'      => $item[10] ?? null,
                'default_uom_ar_name'   => $item[11] ?? null,
                'keywords'              => $item[12] ?? null,
            ];
        }
    }

    private function validateItems()
    {
        if (count($this->items) === 0) {
            throw new \Exception('Excel file must contain at least one item');
        }

        $errors = [];
        $usedIndexes = [];
        foreach ($this->items as $index => $item) {
            $validator = Validator::make($item, [
                'index'                 => 'required|integer|min:1',
                'name'                  => 'required|regex:/^[A-Za-z]+[A-Za-z\d\W]+$/|min:3|max:50',
                'ar_name'               => 'required|regex:/^[\x{0621}-\x{064A}]+[\x{0621}-\x{064A}\d\W]+$/u',
                'brand'                 => 'required|regex:/^[A-Za-z]+[A-Za-z\d\W]+$/|min:3|max:50',
                'ar_brand'              => 'required|regex:/^[\x{0621}-\x{064A}]+[\x{0621}-\x{064A}\d\W]+$/u',
                'barcode'               => 'string|max:64',
                'description'           => 'required|regex:/^[A-Za-z\d\W\d]+$/|min:10|max:16777215',
                'ar_description'        => 'required|regex:/^[\x{0621}-\x{064A}\d\W]+$/u|min:10|max:16777215',
                'total_available_count' => 'required|integer|min:1|max:65535',
                'price'                 => 'required|numeric|between:0,999999.99',
                'default_uom_name'      => 'required|regex:/^[A-Za-z]+[A-Za-z\d\W]+$/|min:3|max:30',
                'default_uom_ar_name'   => 'required|regex:/^[\x{0621}-\x{064A}]+[\x{0621}-\x{064A}\d\W]+$/u|min:3|max:30',
                'keywords'              => 'required|regex:/^([\x{0621}-\x{064A}A-Za-z]+,?)+$/u|min:3|max:255',
            ], [
                'index.required'                    => 'Item index is required',
                'index.integer'                     => 'Item index must be a number',
                'index.min'                         => 'Item index must be greater than 0',
                'name.required'                     => 'Item name is required',
                'name.regex'                        => 'Item name format is invalid',
                'name.min'                          => 'Item name must be at least 3 characters',
                'name.max'                          => 'Item name must be at most 50 characters',
                'ar_name.required'                  => 'Item arabic name is required',
                'ar_name.regex'                     => 'Item arabic name format is invalid',
                'ar_name.min'                       => 'Item arabic name must be at least 3 characters',
                'ar_name.max'                       => 'Item arabic name must be at most 50 characters',
                'brand.required'                    => 'Item brand is required',
                'brand.regex'                       => 'Item brand format is invalid',
                'ar_brand.regex'                    => 'Item arabic brand format is invalid',
                'barcode.string'                    => 'Item barcode format is invalid',
                'barcode.max'                       => 'Item barcode length must be less than 64',
                'description.required'              => 'Item description is required',
                'description.regex'                 => 'Item description format is invalid',
                'description.min'                   => 'Item description length must be greater than 10',
                'description.max'                   => 'Item description length must be less than 16777215',
                'ar_description.required'           => 'Item arabic description is required',
                'ar_description.regex'              => 'Item arabic description format is invalid',
                'ar_description.min'                => 'Item arabic description length must be greater than 10',
                'ar_description.max'                => 'Item arabic description length must be less than 16777215',
                'total_available_count.required'    => 'Item total available count is required',
                'total_available_count.integer'     => 'Item total available count must be a number',
                'total_available_count.min'         => 'Item total available count must be greater than 0',
                'total_available_count.max'         => 'Item total available count must be less than 65536',
                'price.required'                    => 'Item price is required',
                'price.numeric'                     => 'Item price must be a number',
                'price.between'                     => 'Item price must be between 0 and 9999999.99',
                'default_uom_name.required'         => 'Item default UOM name is required',
                'default_uom_name.regex'            => 'Item default UOM name format is invalid',
                'default_uom_ar_name.required'      => 'Item default UOM arabic name is required',
                'default_uom_ar_name.regex'         => 'Item default UOM arabic name format is invalid',
                'keywords.required'                 => 'Item keywords is required',
                'keywords.string'                   => 'Item keywords format is invalid',
                'keywords.min'                      => 'Item keywords length must be greater than 3',
                'keywords.max'                      => 'Item keywords length must be less than 255'
            ]);

            $itemErrors = [
                "line" => $index + 2,
                "messages" => []
            ];
            if ($validator->fails())
                $itemErrors['messages'] = $validator->errors()->all();

            if (in_array($item['index'], $usedIndexes))
                array_unshift($itemErrors['messages'], "Item index is duplicated");
            else
                $usedIndexes[] = $item['index'];

            if (count($itemErrors['messages']) > 0)
                $errors[] = $itemErrors;
        }

        if (count($errors) > 0)
            throw new \App\Exceptions\ValidationError($errors);
    }

    private function parseUoms()
    {
        $uomsSheetIndex = array_search('uoms', $this->sheets);
        
        if (! $uomsSheetIndex) {
            $this->uoms = [];
            return;
        }

        $uoms = $this->xlsx->rows($uomsSheetIndex);
        array_shift($uoms);

        $this->uoms = [];
        
        foreach ($uoms as $uom) {
            $this->uoms[] = [
                'item_index' => $uom[0] ?? null,
                'name' => $uom[1] ?? null,
                'ar_name' => $uom[2] ?? null,
                'description' => $uom[3] ?? null,
                'ar_description' => $uom[4] ?? null,
                'weight' => $uom[5] ?? null,
                'length' => $uom[6] ?? null,
                'width' => $uom[7] ?? null,
                'height' => $uom[8] ?? null,
                'conversion_rule' => [
                    'factor' => $uom[9] ?? null,
                    'is_multiply' => $uom[10] ?? null,
                ],
            ];
        }
    }

    private function validateUoms()
    {
        $itemsIndexs = collect($this->items)->pluck('index')->toArray();

        $errors = [];
        foreach ($this->uoms as $index => $uom) {
            $uomErrors = [
                "line" => $index + 2,
                "messages" => []
            ];

            $validator = Validator::make($uom, [
                'item_index' => 'required|integer|in:' . implode(',', $itemsIndexs),
                'name' => 'required|regex:/^[a-zA-Z]{3,30}$/',
                'ar_name' => ['required', new ArabicLetters, 'min:3', 'max:30'],
                'description' => 'regex:/^\w\W{3,255}$/',
                'ar_description' => 'regex:/^[\x{0621}-\x{064A}\W]{3,255}$/u',
                'weight' => 'numeric|between:0,999999.99',
                'length' => 'numeric|between:0,999999.99',
                'width' => 'numeric|between:0,999999.99',
                'height' => 'numeric|between:0,999999.99',
                'conversion_rule.factor' => 'required|numeric|between:0,999999.99',
                'conversion_rule.is_multiply' => 'required|in:yes,no',
            ]);

            if ($validator->fails())
                $uomErrors['messages'] = $validator->errors()->all();

            if (! in_array($uom['item_index'], $itemsIndexs))
                $uomErrors['messages'][] = "Item index is not found";
            if (count($uomErrors['messages']) > 0)
                $errors[] = $uomErrors;
        }
    }

    /**
     * Get Items
     *
     * @return array
     * @author Abdullah Essam <abdoessam.2010@gmail.com>
     */
    public function getItems(): array
    {
        $items = collect($this->items);
        
        return $items->map(function ($item) {
            unset($item['index']);
            return $item;
        })->toArray();
    }


}