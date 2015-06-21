<?php
/**
 * MultimediaDisplay
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package MultimediaDisplay
 */

/**
 * The MultimediaDisplay index controller class.
 */
class MultimediaDisplay_IndexController extends Omeka_Controller_AbstractActionController
{    
 
/**
 * The default action to display the setup form and process it.
 *
 * This action runs before loading the main import form. It 
 * processes the form output if there is any, and populates
 * some variables used by the form.
 *
 * @return void
 */
  public function indexAction()
  {
        $viewers = apply_filters('multimedia_display_viewers', array());
        foreach ($viewers as $slug => &$viewer) {
            $viewer = $viewer['title'];
        }
        $this->view->viewers = $viewers;

      if($this->_defaultsInstalled())
          $this->_helper->redirector->gotoUrl('multimedia-display/profile');
      else 
          $this->_helper->redirector->gotoUrl('multimedia-display/defaults');
      
  }

  private function _defaultsInstalled() {
      $db = get_db();
      $sql = "select * from `".$db->prefix."MmdProfileAux`";
      $response = $db->query($sql);
      return $response->fetch() ? true : false;
  }
}