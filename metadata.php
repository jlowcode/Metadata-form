<?php
/**
 * Form's Metadata
 * 
 * @package     Joomla.Plugin
 * @subpackage  Fabrik.Form.metadata
 * @copyright   Copyright (C) 2025 Jlowcode Org - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * 	Plugin that displays relevant information form a form when its URL is shared
 * 
 * @package     	Joomla.Plugin
 * @subpackage  	Fabrik.form.metadata
 */
class PlgFabrik_FormMetadata extends PlgFabrik_Form {

    public function __construct(&$subject, $config = array()) 
    {
        parent::__construct($subject, $config);
    }

    /**
     * Check user can view the read only element OR view in form view
     *
     * @param   	string 		$view 		View form
     *
     * @return  	bool
     */
    public function canView($view = 'form') 
    {
        return true;
    }

    /**
     * Increments the access (views) counter each time the record is loaded.
     * 
     * @return 		void
    */
    public function onLoad(&$args)
    {
        $model = $this->getModel();
        $elements = $model->getListModel()->getElements();
        $formData = $model->getData();

        foreach ($elements as $element) {
            $name = $element->getFullName(true, false);

            if ($element->getElement()->plugin === 'textarea' && !isset($description) && stripos($name, 'indexing_text') === false) {
                $description = strip_tags($formData[$name]) ?? null;
            }

            if ($element->getElement()->plugin === 'fileupload' && !isset($image)) {
                $params = $element->getParams();
                if($params->get('ajax_upload') == '0'){
                    $image = $formData[$name] ?? null;
                } elseif($params->get('fu_show_image_in_table') == '3'){
                    $image = (array)$element->getPrincipal($model->getListModel()->getTable()->db_table_name, $model->getRowId(), Factory::getConfig()->get('db')) ?? null;
                    if(isset($image) ){
                        $image = $image[$element->getElement()->name];
                        $image = str_replace(JPATH_SITE, '', $image->dir);
                    } else{
                        $image = $formData[$name][0];
                    }
                } else {
                    $image = $formData[$name][0];
                }

                $image = strip_tags($image);
            }

            if ($element->getElement()->plugin === 'field' && !isset($title)) {
                $title = strip_tags($formData[$name]) ?? null;
            }

            if (isset($description) && isset($image) && isset($title)) {
                break;
            }
        }

        $this->setOgTags($title, $description, $image);
        $this->setTwitterTags($title, $description, $image);
        $this->setTags($title, $description, $image);
    }
    
    /**
     * Sets the Open Graph meta tags for page title, description, and type.
     * 
     * @param   string  $title        The title to set in og:title
     * @param   string  $description  The description to set in og:description
     * @param   string  $image        The image URL to set in og:image
     * 
     * @return  void
     */
    public function setOgTags($title, $description, $image)
    {
        $this->app->getDocument()->setMetaData('og:title', strip_tags($title), 'property');
        $this->app->getDocument()->setMetaData('og:description', strip_tags($description), 'property');
        $this->app->getDocument()->setMetaData('og:type', 'website', 'property');

        if(!empty($image)) {
            $image = Uri::root() . ltrim($image, '/');
            $this->app->getDocument()->setMetaData('og:image', $image, 'property');
        }
    }

    /**
     * Sets the Twitter meta tags for card type, title, and description.
     * 
     * @param   string  $title        The title to set in twitter:title
     * @param   string  $description  The description to set in twitter:description
     * @param   string  $image        The image URL to set in twitter:image
     * 
     * @return  void
     */
    public function setTwitterTags($title, $description, $image)
    {
        $this->app->getDocument()->setMetaData('twitter:title', strip_tags($title), 'property');
        $this->app->getDocument()->setMetaData('twitter:description', strip_tags($description), 'property');
        $this->app->getDocument()->setMetaData('twitter:card', 'summary_large_image', 'property');

        if(!empty($image)) {
            $image = Uri::root() . ltrim($image, '/');
            $this->app->getDocument()->setMetaData('twitter:image', $image, 'property');
        }
    }

    /**
     * Sets the standard meta tags for page title, description, and image.
     * 
     * @param   string  $title        The title to set in meta title
     * @param   string  $description  The description to set in meta description
     * @param   string  $image        The image URL to set in meta image
     * 
     * @return  void
     */
    public function setTags($title, $description, $image)
    {
        $this->app->getDocument()->setMetaData('title', strip_tags($title), 'property');
        $this->app->getDocument()->setMetaData('description', strip_tags($description), 'property');

        if(!empty($image)) {
            $image = Uri::root() . ltrim($image, '/');
            $this->app->getDocument()->setMetaData('image', $image, 'property');
        }   
    }
}