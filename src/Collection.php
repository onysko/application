<?php
/**
 * Created by PhpStorm.
 * User: egorov
 * Date: 21.04.2015
 * Time: 9:59
 */
namespace samsoncms;

use samsoncms\field\Generic;
use samsonframework\core\RenderInterface;
use samsonframework\pager\PagerInterface;
use samsonframework\orm\QueryInterface;

/**
 * Generic SamsonCMS application entities collection.
 * Class provide all basic UI interactions with database entities.
 *
 * @package samsoncms
 * @author Vitaly Iegorov <egorov@samsonos.com>
 */
class Collection extends \samsonframework\collection\Paged
{
    /** @var string Entity fields row view */
    protected $rowView = 'collection/body/row';

    /** @var CollectionField[] Coolection of entity fields to manipulate */
    protected $fields;

    /**
     * Generic collection constructor
     * @param RenderInterface $renderer View render object
     * @param QueryInterface $query Query object
     */
    public function __construct(RenderInterface $renderer, QueryInterface $query, PagerInterface $pager)
    {
        // Call parent initialization
        parent::__construct($renderer, $query, $pager);

        // If we have not configured fields before
        if (!sizeof($this->fields)) {
            // TODO: This must be incapsulated into QueryInterface ancestor
            // Get current entity name
            $entity = $query->className();
            // Store its attributes
            foreach ($entity::$_attributes as $field) {
                $this->fields[] = new Generic($field);
            }
        }
    }

    /**
     * Overload default, render SamsonCMS collection index
     * @param string $items Rendered items
     * @return string Rendered collection block
     */
    public function renderIndex($items)
    {
        $headerHTML = '';
        foreach ($this->fields as $field) {
            $headerHTML .= $field->renderHeader($this->renderer);
        }

        return $this->renderer
            ->view($this->indexView)
            ->set('header', $headerHTML)
            ->set('items', $items)
            ->output();
    }

    /**
     * Overload default, render fields as SamsonCMS input fields
     * @param mixed $item Item to render
     * @return string Rendered collection item block
     */
    public function renderItem($item)
    {
        // Iterate all entity fields
        $fieldsHTML = '';
        foreach ($this->fields as $field) {
            // Render input field view
            $fieldsHTML .= $field->render($this->renderer, $this->query, $item);
        }

        // Render fields row view
        return $this->renderer
            ->view($this->rowView)
            ->set('cols', $fieldsHTML)
            ->output();
    }
}
