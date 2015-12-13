<?php
/**
 * Multimedia Display
 *
 * This Omeka 2.0+ plugin 
 * 
 *
 * @copyright Copyright 2014 UCSC Library Digital Initiatives
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 *
 * @package MultimediaDisplay
 */

define('MMD_PLUGIN_DIR', dirname(__FILE__));
define('MMD_HELPERS_DIR', MMD_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'helpers');
define('MMD_FORMS_DIR', MMD_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'forms');
require_once MMD_HELPERS_DIR . DIRECTORY_SEPARATOR . 'ThemeHelpers.php';

/**
 * Multimedia Display plugin class
 */
class MultimediaDisplayPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Options for the plugin.
     */
    protected $_options = array(
    );

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install',
        'uninstall',
        'define_acl',
        'admin_items_show',
        'public_items_show',
        'admin_head',
        'public_head'
    );
  
    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'admin_navigation_main',
        'multimedia_display_viewers',
    );

    /**
     * Define the plugin's access control list.
     *
     *@param array $args Parameters supplied by the hook
     *@return void
     */
    public function hookDefineAcl($args)
    {
        $args['acl']->addResource('MultimediaDisplay_Index');
    }

    /**
     * Load the plugin javascript & css when admin section loads
     *
     *@return void
     */
    public function hookAdminHead()
    { 
        queue_js_file('MmdAdminScripts');
        queue_css_file('MultimediaDisplay');

        //$this->_applyAssignments();
    }

    /**
     * Load the plugin javascript & css when admin section loads
     *
     *@return void
     */
    public function hookPublicHead()
    {
        queue_css_file('MultimediaDisplay');
        $this->_applyAssignments();
    }

    private function _applyAssignments() {
        try{
            $item = get_current_record('Item');
        }catch(Exception $e) {
            if(empty($item))
                return;
        }
        $profiles = $this->_db->getTable('MmdProfile')->getAssignedProfiles($item);
        if(count($profiles)==0)
            return;
        foreach($profiles as $profile) 
            $profile->executeViewerHead();
    }

    /**
     * Add the Multimedia Display link to the admin main navigation.
     * 
     * @param array $nav Navigation array.
     * @return array $filteredNav Filtered navigation array.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Media Display'),
            'uri' => url('multimedia-display'),
            'resource' => 'MultimediaDisplay_Index',
            'privilege' => 'index'
        );
        return $nav;
    }

    /**
     * When the plugin installs, create the database tables 
     * 
     * @return void
     */
    public function hookInstall()
    {

        $db = $this->_db;
        try{
            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->MmdAssign` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `profile_id` int(10) unsigned NOT NULL,
                `item_type_id` int(10) unsigned,
                `collection_id` int(10) unsigned,
                `default` bool default true,
                `filetypes` text,
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            $this->_db->query($sql);

            $sql = "
            CREATE TABLE IF NOT EXISTS `$db->MmdProfile` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` text NOT NULL,
                `viewer` text NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            $this->_db->query($sql);

            $sql = "
            CREATE TABLE IF NOT EXISTS `".$db->prefix."MmdProfileAux` (
                `profile_id` int(10) unsigned NOT NULL,
                `option` text NOT NULL,
                `value` text NOT NULL,
                `static` tinyint NOT NULL,
                `multiple` tinyint DEFAULT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
            $this->_db->query($sql);
        }catch(Exception $e) {
            throw $e; 
        }

        $this->_installOptions();
/*
        $viewers = $this->filterMultimediaDisplayViewers(array());
        foreach ($viewers as $slug => $viewer) {
            $viewerClass = $viewer['class'];
            $viewer = new $viewerClass();
            $viewer->installDefaults();
       } 
*/

        //todo - create a temp directory in files/

    }

    /**
     * When the plugin uninstalls, delete the database tables 
     *which store the logs
     * 
     * @return void
     */
    public function hookUninstall()
    {
        $this->_uninstallOptions();
      try{
	$db = get_db();
	$sql = "DROP TABLE IF EXISTS `$db->MmdAssign`; ";
	$db->query($sql);
	$sql = "DROP TABLE IF EXISTS `$db->MmdProfile`; ";
	$db->query($sql);
	$sql = "DROP TABLE IF EXISTS `".$db->prefix."MmdProfileAux`; ";
	$db->query($sql);
      }catch(Exception $e) {
	throw $e;	
      }

      //TODO delete any temp directories

    }

    /**
     * Add viewer markup to public item display pages
     *
     * @param array $args An array of parameters passed by the hook
     * @return void
     */
    public function hookPublicItemsShow($args)
    {
        $item = $args['item'];
        if (empty($item)) {
            return;
        }

        $profile = $this->_db->getTable('MmdProfile')->getPrimaryAssignedProfile($item);
        if (empty($profile)) {
            return;
        }

        try{
            echo $profile->getBodyHtml();
        } catch (Exception $e) {
            echo '<h3>' . __('Error loading viewer') . '</h3>'
                . '<p>' . $e->getMessage() . '</p>';
        }
    }


    /**
     * Add viewer markup to admin item display pages
     * (or not)
     *
     * @param array $args An array of parameters passed by the hook
     * @return void
     */
    public function hookAdminItemsShow($args)
    {

    }

    /**
     * Add the viewers that are available.
     *
     * @param array $viewers List of supported viewers.
     * @return array Filtered list of supported viewers.
    */
    public function filterMultimediaDisplayViewers($viewers)
    {
        // Available default viewers managed by the plugin, ordered by title.
        $viewers['PDF'] = array(
            'title' => 'Embedded PDF Viewer',
            'class' => 'MultimediaDisplay_Viewer_PDF',
        );
        $viewers['Kaltura'] = array(
            'title' => 'Kaltura',
            'class' => 'MultimediaDisplay_Viewer_Kaltura',
        );
        $viewers['BookReader'] = array(
            'title' => 'Internet Archive Book Reader',
            'class' => 'MultimediaDisplay_Viewer_BookReader',
        );
        $viewers['MediaElement'] = array(
            'title' => 'MediaElement.js',
            'class' => 'MultimediaDisplay_Viewer_MediaElement',
        );
        /*
        $viewers['Mirador'] = array(
            'title' => 'Mirador',
            'class' => 'MultimediaDisplay_Viewer_Mirador',
        );
        */
        $viewers['Ohms'] = array(
            'title' => 'OHMS Viewer',
            'class' => 'MultimediaDisplay_Viewer_Ohms',
        );
        $viewers['OpenSeaDragon'] = array(
            'title' => 'OpenSeaDragon Jpeg2000 Viewer',
            'class' => 'MultimediaDisplay_Viewer_OpenSeaDragon',
        );
        $viewers['PanZoom'] = array(
            'title' => 'PanZoom Image Zooming',
            'class' => 'MultimediaDisplay_Viewer_PanZoom',
        );
        /*
        $viewers['Youtube'] = array(
            'title' => 'Youtube',
            'class' => 'MultimediaDisplay_Viewer_Youtube',
        );
        */
        return $viewers;
    }
}
