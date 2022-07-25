<?php

namespace Marktic\Newsletter\Base\Models\Traits;

use ByTIC\Records\Behaviors\HasForms\HasFormsRecordsTrait;
use Nip\I18n\Translatable\HasTranslations;
use Nip\Records\Filters\Records\HasFiltersRecordsTrait;

/**
 * Trait CommonRecordsTrait
 * @package Marktic\Newsletter\Models\AbstractModels
 */
trait CommonRecordsTrait
{
    use HasTranslations;
    use HasFormsRecordsTrait;
    use HasFiltersRecordsTrait;

    protected function initRelations()
    {
        parent::initRelations();
        $this->initRelationsNewsletter();
    }

    /**
     * @return string
     */
    public function getTranslateRoot()
    {
        return $this->getController();
    }

    public function getRootNamespace()
    {
        return 'Marktic\Newsletter\Models\\';
    }

    protected function generateController(): string
    {
        if (defined('static::CONTROLLER')) {
            return static::CONTROLLER;
        }

        return $this->getTable();
    }
}
