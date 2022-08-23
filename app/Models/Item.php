<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class Item extends Model
{
    use HasFactory;

    const AVAILABLE_COLUMNS = [
        'name',
        'ar_name',
        'category_id',
        'brand',
        'ar_brand',
        'barcode',
        'keywords',
        'description',
        'ar_description',
        'has_promotions',
        'is_var',
        'vars',
        'is_available',
        'video_uri',
        'total_available_count',
        'is_approved',
        'supplier_id',
        'is_active',
        'price',
        'currency',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'name',
        'ar_name',
        'category_id',
        'brand',
        'ar_brand',
        'barcode',
        'keywards',
        'description',
        'ar_description',
        'has_promotions',
        'is_var',
        'vars',
        'is_available',
        'video_uri',
        'total_available_count',
        'is_approved',
        'supplier_id',
        'is_active',
        'price'
    ];

    protected $casts = [
        'vars' => 'array',
        'is_available' => 'boolean',
        'is_approved' => 'boolean',
        'is_active' => 'boolean',
        'has_promotions' => 'boolean',
        'is_var' => 'boolean',
        'price' => 'float',
        'total_available_count' => 'integer',
        'supplier_id' => 'integer',
        'category_id' => 'integer'
    ];

    public function setKeywordsAttribute($value)
    {
        $valToArray = explode(',', $value);
        $valToArray = array_map('trim', $valToArray);
        $valToArray = array_map('strtolower', $valToArray);
        $valToArray = array_unique($valToArray);
        $this->attributes['keywords'] = implode(',', $valToArray);
    }

    public function activate(bool $is_activate)
    {
        $this->is_active = $is_activate;
        try {
            $this->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return $this;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id')->first();
    }

    public function getCategory()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id', 'id')->first();
    }

    public function withCategory()
    {
        $this->category = $this->getCategory();
        return $this;
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class, 'item_id', 'id');
    }

    public function getCoverImage()
    {
        return $this->images()->where("is_cover", true)->first();
    }

    public function getCoverImageAttribute()
    {
        return $this->getCoverImage();
    }

    public function addCoverImage(string $uri)
    {
        return $this->addImage($uri, null, null, null, true);
    }

    public function updateOrAddCoverImage(string $uri)
    {
        if ($savedCover = $this->getCoverImage()) {
            $savedCover->uri = $uri;
            $savedCover->save();
            return $savedCover;
        } else {
            return $this->addCoverImage($uri);
        }
    }

    public function getImages()
    {
        return $this->images()->get()->all();
    }
    
    public function getImagesAttribute()
    {
        return $this->getImages();
    }

    /**
     * add image to item
     *
     * @param string $uri uri to saved image file
     * @param integer $posX x position of image
     * @param integer $posY y position of image
     * @param integer $scale scale of image
     * @return \App\Models\ItemImage
     */
    public function addImage(string $uri, $posX = null, $posY = null, $scale = null, $isCover = false)
    {
        return $this->images()->create([
            'uri' => $uri,
            'pos' => [
                'x'     => $posX ?? 0,
                'y'     => $posY ?? 0,
                'scale' => $scale ?? 1
            ],
            'is_cover' => $isCover
        ]);
    }

    /**
     * Validate the attributes of the varient item.
     *
     * @param array $attribute The attribute to validate.
     * @return void
     * @throws Exception if rules not confirmed
     * @author Abdullah Essam <abdoessam.2010@gmail.com>
     */
    public function validateVariantAttribute(array $attribute)
    {
        if (! isset($attribute['name']))
            throw new Exception("Missing attribute 'name' property");
        
        if (! isset($attribute['ar_name']))
            throw new Exception("Missing attribute 'ar_name' property");

        if (! is_string($attribute['name']))
            throw new Exception("name property must be string");

        if (! is_string($attribute['ar_name']))
            throw new Exception("ar_name property must be string");

        $nameLength = strlen($attribute['name']);
        if ($nameLength < 2 || $nameLength > 20)
            throw new Exception("name property must be between '2-20' charactars");

        $arNameLength = strlen($attribute['ar_name']);
        if ($arNameLength < 2 || $arNameLength > 20)
            throw new Exception("ar_name property must be between '2-20' charactars");
    }
    
    /**
     * Validate the attribute's values of the varient item. 
     *
     * @param array $values The values to validate.
     * @return void
     * @throws Exception if rules not confirmed
     * @author Abdullah Essam <abdoessam.2010@gmail.com>
     */
    public function validateVariantValues(array $values)
    {
        if (count($values) < 1)
            throw new Exception("Attribute values must be at least one");

        $usedValNames = [];
        
        foreach ($values as $index => $value) {
            if (! is_array($value))
                throw new Exception("Value must be an array at index [$index]");

            if (! isset($value['name']))
                throw new Exception("Missing value 'name' property at index [$index]");
            if (! isset($value['ar_name']))
                throw new Exception("Missing value 'ar_name' property at index [$index]");

            if (! isset($value['is_available']))
                throw new Exception("Missing value 'is_available' property at index [$index]");

            if (! is_string($value['name']))
                throw new Exception("value 'name' property must be string at index [$index]");

            if (! is_string($value['ar_name']))
                throw new Exception("value 'ar_name' property must be string at index [$index]");

            if (! is_bool($value['is_available']))
                throw new Exception("value 'is_available' property must be boolean at index [$index]");

            $nameLength = strlen($value['name']);
            if ($nameLength < 2 || $nameLength > 20)
                throw new Exception("value 'name' property must be between '2-20' charactars at index [$index]");
            
            $arNameLength = strlen($value['ar_name']);
            if ($arNameLength < 2 || $arNameLength > 20)
                throw new Exception("value 'ar_name' property must be between '2-20' charactars at index [$index]");

            if (in_array(strtolower($value['name']), $usedValNames))
                throw new Exception("duplicated value name at value[$index]");

            $usedValNames[] = strtolower($value['name']);
            
            if (isset($value['children'])) {
                $children = $value['children'];

                if (! is_array($children))
                    throw new Exception("value 'children' property must be array at index [$index]");

                $usedAttrNames = [];
                foreach ($children as $childIndex => $child) {
                    if (! is_array($child))
                        throw new Exception("child[$childIndex] must be an array at value[$index]");

                    if (! isset($child['attribute']))
                        throw new Exception("Missing child 'attribute' property at child [$childIndex] in value [$index]");

                    if (! is_array($child['attribute']))
                        throw new Exception("child 'attribute' property must be array at child [$childIndex] in value [$index]");

                    if (! isset($child['values']))
                        throw new Exception("Missing child 'values' property at child [$childIndex] in value [$index]");

                    if (! is_array($child['values']))
                        throw new Exception("child 'values' property must be array at child [$childIndex] in value [$index]");

                    foreach ($child['values'] as $childValueIndex => $childValue)
                        if (isset($childValue['children']))
                            throw new Exception("child value cannot include children at value [$childValueIndex] at child [$childIndex] in value [$index]");

                    try {
                        $this->validateVariantAttribute($child['attribute']);
                    } catch (Exception $e) {
                        throw new Exception($e->getMessage() . " at child [$childIndex] in value [$index]");
                    }

                    if (in_array(strtolower($child['attribute']['name']), $usedAttrNames))
                        throw new Exception('duplicated attribute "' . $child['attribute']['name'] . '" at child' . "[$childIndex] at value[$index]");
                    
                    $usedAttrNames[] = strtolower($child['attribute']['name']);
                    
                    try {
                        $this->validateVariantValues($child['values']);
                    } catch (Exception $e) {
                        throw new Exception($e->getMessage() . " at child [$childIndex] in value [$index]");
                    }
                }
            }
        }
    }

    /**
     * Sanitize variant attribute to collect needed data only.
     *
     * @param array $attribute The attribute to sanitize.
     * @return array $attribute The sanitized attribute.
     * @author Abdullah Essam <abdoessam.2010@gmail.com>
     */
    public function sanitizeVariantAttribute(array $attribute): array
    {
        $sanitized = collect($attribute)->only(['name', 'ar_name'])->toArray();
        $sanitized['name'] = ucfirst(strtolower(trim($sanitized['name'])));
        return $sanitized;
    }

    /**
     * Sanitize variant values to collect needed data only.
     *
     * @param array $values The values to sanitize
     * @param boolean $isParent Whether the values are parent values or not.
     * @return array The sanitized values.
     * @author Abdullah Essam <abdoessam.2010@gmail.com>
     */
    public function sanitizeVariantValues(array $values, bool $isParent = true): array
    {
        $neededValues = ['name', 'ar_name', 'is_available'];

        if ($isParent)
            $neededValues[] = 'children';

        return collect($values)->map(function ($value) use ($neededValues) {
            $value = collect($value)->only($neededValues)->all();
            $value['name'] = ucfirst(strtolower(trim($value['name'])));
            if (isset($value['children'])) {
                $children = $value['children'];
                $value['children'] = collect($children)->map(function ($child) {
                    $child = collect($child)->only(['attribute', 'values'])->all();
                    $child['attribute'] = $this->sanitizeVariantAttribute($child['attribute']);
                    $child['values'] = $this->sanitizeVariantValues($child['values'], false);
                    return $child;
                })->toArray();
            } // end of if

            return $value;
        })->toArray();
    }

    /**
     * add Variant to item, if attribute is defined in vars it will be overridden
     *
     * @param array $attribute associative array consisting of attribute name and attribute name in arabic
     * @param array $values numeric array consisting of values of the attribute, every value is an associative array consisting of value name and value name in arabic, is_available and children if any.
     * @return bool true if success.
     * @throws Exception if rules not confirmed
     * @author Abdullah Essam <abdoessam.2010@gmail.com>
     */
    public function updateOrCreateVariant(array $attribute, array $values)
    {
        // validate params
        $this->validateVariantAttribute($attribute);
        $this->validateVariantValues($values);

        $this->is_var = true;
        $attribute['name'] = ucfirst(strtolower($attribute['name']));
        $vars = $this->vars ?? [];
        foreach ($vars as $index => $var) {
            $attributeName = $var['attribute']['name'];
            
            if ($attributeName === $attribute['name']) {
                $vars[$index]['attribute'] = $this->sanitizeVariantAttribute($attribute);
                $vars[$index]['values'] = $this->sanitizeVariantValues($values);
                $this->vars = $vars;
                return true;
            }
        }

        $vars[] = [
            'attribute' => $this->sanitizeVariantAttribute($attribute),
            'values' => $this->sanitizeVariantValues($values)
        ];

        $this->vars = $vars;
        return true;
    }

    /**
     * Delete variant from item, if attribute is defined in vars it will be deleted
     *
     * @param string $attributeName name of the attribute to delete
     * @return boolean true if success, false if not found.
     * @author Abdullah Essam <abdoessam.2010@gmail.com>
     */
    public function deleteVariant(string $attributeName): bool
    {
        $attributeName = strtolower($attributeName);
        $vars = $this->vars ?? [];

        foreach ($vars as $index => $var) {
            $varAttributeName = strtolower($var['attribute']['name']);
            
            if ($varAttributeName === $attributeName) {
                unset($vars[$index]);
                $this->vars = $vars;
                if (empty($vars)) {
                    $this->is_var = false;
                    $this->vars = null;
                }
                return true;
            }
        }

        return false;
    }

    /**
     * Set item to be not variant and delete all variants if any
     *
     * @return void
     * @author Abdullah Essam <abdoessam.2010@gmail.com>
     */
    public function setItemToNotVariant()
    {
        $this->is_var = false;
        $this->vars = null;
    }

    public function withImages()
    {
        $this->images = $this->getImages();
        return $this;
    }

    public function addComment(ItemsComment $comment)
    {
        $comment->item_id = $this->id;
        try {
            $comment->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function getComments()
    {
        return $this->hasMany(ItemsComment::class, 'item_id', 'id')->get()->all();
    }

    public function withComments()
    {
        $this->comments = $this->getComments();
        return $this;
    }

    public function addRate(int $rate)
    {
        $rate = new ItemsRate;
        $rate->rate = $rate;
        $rate->item_id = $this->id;
        $rate->customer_id = auth()->user()->userData->userInfo->id;

        try {
            $rate->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function getRates()
    {
        return $this->hasMany(ItemsRate::class, 'item_id', 'id')->get()->all();
    }

    public function calculateRate()
    {
        $allRates = $this->getRates();
        $countOfRates = count($allRates);
        if ($countOfRates === 0)
            return 0;
        $ratesSum = 0;
        foreach ($allRates as $rate)
            $ratesSum += (int) $rate->rate;

        return $ratesSum/$countOfRates;
    }

    public function withRate()
    {
        $this->rate = $this->calculateRate();
        return $this;
    }

    public function getPromotions()
    {
        return $this->hasMany(Promotion::class, 'item_id', 'id')->get()->all();
    }

    // add promotion to item
    public function addPromotion(Promotion $promotion)
    {
        $this->has_promotions = true;
        try {
            $this->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        $promotion->item_id = $this->id;
        try {
            $promotion->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function withPromotions()
    {
        $this->promotions = $this->getPromotions();
        return $this;
    }

    /**
     * Add units of measurement to item
     *
     * @param array $uoms array of Uom::class objects
     * @return true on success
     * @throws \App\Exceptions\DBException if query fails
     */
    public function addUOMs(array $uoms)
    {

        try {
            foreach ($uoms as $uom) {
                $uomObj = new Uom;
                $uomObj->item_id = $this->id;
                foreach ($uom as $key => $value) {
                    if (! in_array($key, ['is_default', 'conversion_rule']))
                        $uomObj->$key = $value;
                }
                if ($uom['is_default'])
                    $uomObj->setDefault();
                $uomObj->save();

                // adding conversion rule
                if (isset($uom['conversion_rule'])) {
                    $uom = (object) $uom['conversion_rule'];
                    $uomObj->setConversionRule($uom->factor, $uom->is_multiply);
                }
            }
            return true;
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function getUOMs()
    {
        $UOMs = $this->hasMany(Uom::class, 'item_id', 'id')->get()->all();
        foreach ($UOMs as $UOM)
            $UOM->withPrice();
        return $UOMs;
    }

    // update uom by id
    public function updateUOM
    (
        int $id,
        string $name,
        string $arName,
        string $description=null,
        string $arDescription=null,
        bool $isDefault=false
    )
    {
        $uom = $this->hasMany(Uom::class, 'item_id', 'id')->where('id',$id)->first();
        if (!$uom)
            throw new \App\Exceptions\NotFoundException(get_class($this), $id);

        try {
            $uom->uom_name = $name;
            $uom->ar_uom_name = $arName;
            $uom->description = $description;
            $uom->ar_description = $arDescription;
            $uom->is_default = $isDefault;
            $uom->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    // delete uom by id
    public function deleteUOM(int $id)
    {
        $uom = $this->hasMany(Uom::class, 'item_id', 'id')->where('id',$id)->first();
        if (!$uom)
            throw new \App\Exceptions\NotFoundException(get_class($this), $id);

        try {
            $uom->delete();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function withUOMs()
    {
        $this->UOMs = $this->getUOMs();
        return $this;
    }

    public function getFullObject()
    {
        $fullItemObject = clone $this;
        return $fullItemObject
        ->withCategory()
        ->withUOMs()
        ->withPromotions()
        ->withRate()
        ->withComments()
        ->withImages();
    }

    public function withFullData()
    {
        return $this
        ->withCategory()
        ->withUOMs()
        ->withPromotions()
        ->withRate()
        ->withComments()
        ->withImages();
    }

    /**
     * 
     *
     * @param Uom $uom
     * @param integer $quantity
     * @return array
     */
    public function sell(Uom $uom, int $quantity, Promotion $promotion=null)
    {
        if ($quantity > $this->total_available_count)
            return false;

        $unitPrice = $uom->calculateUnitPrice($this);
        
        $totalPrice = $unitPrice * $quantity;

        if ($promotion) {
            if ($promotion->start_date->isPast() && $promotion->expire_date->isFuture()):
                $discount = $promotion->is_discount ?
                            floor($quantity/$promotion->conditional_quantity) * $promotion->amount_of_discount
                            : 0;

                $freeElements = $promotion->is_discount ? 0 : floor($quantity/$promotion->conditional_quantity);
            endif;
        }

        $totalPriceAfterDiscount = $totalPrice - $discount;
        return [
            'total' => $totalPrice,
            'free_elements' => $freeElements,
            'discount' => $discount,
            'total_after_discount' => $totalPriceAfterDiscount
        ];
    }

    // approve item
    public function approve()
    {
        try {
            $this->is_approved = true;
            $this->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    // reject item
    public function reject()
    {
        try {
            $this->is_approved = false;
            $this->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }
    
}
