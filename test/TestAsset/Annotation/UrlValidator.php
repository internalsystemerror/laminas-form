<?php

namespace LaminasTest\Form\TestAsset\Annotation;

use Laminas\Validator\ValidatorInterface;

class UrlValidator implements ValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function isValid($value)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getMessages()
    {
        return [];
    }
}
