<?php
/**
 *
 *
 * @category MultimediaDisplay
 * @uses Zend_Form_Element_Text
 */
class MultimediaDisplay_Form_Element_DisplayParam extends Zend_Form_Element_Text
{
    public $helper = 'displayParam';
    public function init()
    {
        // $this->addValidator('EmailAddress')
        //     ->addFilter('StringToLower');
        return parent::init();
    }
}

?>