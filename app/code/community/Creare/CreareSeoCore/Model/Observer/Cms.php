<?php

class Creare_CreareSeoCore_Model_Observer_Cms extends Mage_Core_Model_Abstract
{
    protected $helper;

    public function _construct()
    {
        $this->helper = Mage::helper('creareseocore/meta');
        parent::_construct();
    }

    /* If enabled, sets the page <title> for all CMS pages based on template defined in the config */

    public function forceTitle($observer)
    {
        if(!$this->shouldRunOnCurrentAction()) {
            return $this;
        }

        $newTitle = $this->helper->getDefaultTitle('cms');

        if (!empty($newTitle)) {
            $this->overridePageTitle($newTitle);
        }

        return $this;
    }

    protected function overridePageTitle($titleString) {
        $layout = Mage::app()->getLayout();
        if(!$layout) {
            $this->log("Failed to load layout; unable to force page title to '{$titleString}'.");
            return;
        }

        $head = $layout->getBlock('head');
        if (!$head) {
            $this->log("Failed to get head block; unable to force page title to '{$titleString}'.");
            return;
        }
        
        $head->setData('title', $titleString);
    }

    public function shouldRunOnCurrentAction() {
        $actionName = Mage::app()->getFrontController()->getAction()->getFullActionName();

        if ( !in_array($actionName, $this->getActionNamesToActUpon()) ) {
            return false;
        }

        $enabledInConfig = $this->helper->config("cms_title_enabled");
        if ( !$enabledInConfig ) {
            return false;
        }

        return true;
    }

    public function getActionNamesToActUpon() {
        return ["cms_index_index", "cms_page_view"];
    }

    public function log($msg, $level=null) {
        Mage::log($msg, $level, 'creare_seo.log');
    }
}
