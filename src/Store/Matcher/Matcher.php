<?php

declare(strict_types=1);

namespace Blixit\EventSourcing\Store\Matcher;

use function array_merge;
use function implode;
use function in_array;
use function sprintf;

class Matcher implements MatcherInterface
{
    /** @var FilterObject[] $fields */
    protected $fields = [];

    /** @var string[] $allowedFields */
    protected $allowedFields = [];

    /**
     * @param FilterObject[] $fields
     * @param string[]       $allowedFields
     */
    public function __construct(array $fields, array $allowedFields = [])
    {
        $this->allowedFields = array_merge($allowedFields, $this->allowedFields);
        foreach ($fields as $field) {
            $this->addSearchField($field);
        }
    }

    /**
     * @return FilterObject[]
     */
    public function getFields() : array
    {
        return $this->fields;
    }

    public function addAllowedField(string $field) : void
    {
        if (in_array($field, $this->allowedFields)) {
            return;
        }
        $this->allowedFields[] = $field;
    }

    /**
     * @throws MatcherException
     */
    public function addSearchField(FilterObject $filterObject) : void
    {
        if (! in_array($filterObject->getField(), $this->allowedFields)) {
            throw new MatcherException(
                sprintf(
                    'Field "%s" is not allowed. Allowed: %s',
                    $filterObject->getField(),
                    implode(', ', $this->allowedFields)
                )
            );
        }
        $this->fields[$filterObject->getField()] = $filterObject;
    }
}
