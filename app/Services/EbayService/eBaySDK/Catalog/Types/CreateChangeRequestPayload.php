<?php
/**
 * DO NOT EDIT THIS FILE!
 *
 * This file was automatically generated from external sources.
 *
 * Any manual change here will be lost the next time the SDK
 * is updated. You've been warned!
 */

namespace DTS\eBaySDK\Catalog\Types;

/**
 *
 * @property \DTS\eBaySDK\Catalog\Enums\ChangeRequestType $changeRequestType
 * @property string $reasonForChangeRequest
 * @property string $referenceId
 * @property \DTS\eBaySDK\Catalog\Enums\ReferenceType $referenceType
 * @property \DTS\eBaySDK\Catalog\Types\SuggestedProduct $suggestedProduct
 */
class CreateChangeRequestPayload extends \DTS\eBaySDK\Types\BaseType
{
    /**
     * @var array Properties belonging to objects of this class.
     */
    private static $propertyTypes = [
        'changeRequestType' => [
            'type' => 'string',
            'repeatable' => false,
            'attribute' => false,
            'elementName' => 'changeRequestType'
        ],
        'reasonForChangeRequest' => [
            'type' => 'string',
            'repeatable' => false,
            'attribute' => false,
            'elementName' => 'reasonForChangeRequest'
        ],
        'referenceId' => [
            'type' => 'string',
            'repeatable' => false,
            'attribute' => false,
            'elementName' => 'referenceId'
        ],
        'referenceType' => [
            'type' => 'string',
            'repeatable' => false,
            'attribute' => false,
            'elementName' => 'referenceType'
        ],
        'suggestedProduct' => [
            'type' => 'DTS\eBaySDK\Catalog\Types\SuggestedProduct',
            'repeatable' => false,
            'attribute' => false,
            'elementName' => 'suggestedProduct'
        ]
    ];

    /**
     * @param array $values Optional properties and values to assign to the object.
     */
    public function __construct(array $values = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        $this->setValues(__CLASS__, $childValues);
    }
}
