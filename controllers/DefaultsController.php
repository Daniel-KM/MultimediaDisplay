<?php
/**
 * MultimediaDisplay
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * The MultimediaDisplay defaults controller class.
 */
class MultimediaDisplay_DefaultsController extends Omeka_Controller_AbstractActionController
{    

    public function indexAction() 
    {
        $viewers = apply_filters('multimedia_display_viewers', array());
        foreach ($viewers as $slug => &$viewer) {
            $viewer = $viewer['title'];
       }

        $this->view->viewers = $viewers;
    }
 
/**
 * Install the default configuration for a viewer.
 *
 * @return void
 */
  public function installAction()
  {
    if(isset($_REQUEST['viewer']))
       $viewerName = $_REQUEST['viewer'];

    //initialize flash messenger for success or fail messages
    $flashMessenger = $this->_helper->FlashMessenger;

    $viewers = apply_filters('multimedia_display_viewers', array());
    if (!isset($viewers[$viewerName]['class'])) {
        $flashMessenger->addMessage($e->getMessage(),'error');
    }
    else {
        $viewerClass = $viewers[$viewerName]['class'];
        try{
            // Use Zend autoload.
            $viewer = new $viewerClass();
            $successMessage = $viewer->installDefaults();
        } catch (Exception $e) {
            $flashMessenger->addMessage($e->getMessage(),'error');
        }
    }

    if(!empty($successMessage))
      $flashMessenger->addMessage($successMessage,'success');

    $this->_helper->redirector->gotoUrl('multimedia-display');
  }
}