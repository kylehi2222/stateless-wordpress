<?php
/**
 * The main class for controllers
 *
 * @package HMWP/Main
 * @file The Front Controller file
 *
 */

defined('ABSPATH') || die('Cheatin\' uh?');

class HMWPP_Classes_FrontController
{

    /**
     * The class Model from /models
     *
     * @var object of the model class 
     */
    public $model;

    /**
     * The class view from /views
     *
     * @var HMWPP_Classes_DisplayController of the view class
     */
    public $view;

    /**
     * The class name
     *
     * @var string name of theclass 
     */
    protected $name;

    /**
     * HMWPP_Classes_FrontController constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {

        /* get the name of the current class */
        $this->name = get_class($this);

        /* load the model and hooks here for WordPress actions to take efect */
        /* create the model and view instances */
        $model_classname = str_replace('Controllers', 'Models', $this->name);
        if(HMWPP_Classes_ObjController::getClassByPath($model_classname)) {
            $this->model = HMWPP_Classes_ObjController::getClass($model_classname);
        }

        //IMPORTANT TO LOAD HOOKS HERE
        /* check if there is a hook defined in the controller clients class */
        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_HookController')->setHooks($this);

        /* Load the Main classes Actions Handler */
        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_Action');
        HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController');
        HMWPP_Classes_ObjController::getClass('HMWPP_Models_Abstract_Provider');

    }

    /**
     * load sequence of classes
     * Function called usualy when the controller is loaded in WP
     *
     * @return HMWPP_Classes_FrontController
     * @throws Exception
     */
    public function init()
    {
        return $this;
    }

    /**
     * Get the block view
     *
     * @param  string $view
     * @param  stdClass $obj
     * @return string HTML
     * @throws Exception
     */
    public function getView($view = null, $obj = null)
    {
        if(!isset($obj)) {
            $obj = $this;
        }

        //Get the view class name if not defined
        if (!isset($view)) {
            if ($class = HMWPP_Classes_ObjController::getClassByPath($this->name)) {
                $view = $class['name'];
            }
        }

        //Call the display class to load the view
        if (isset($view)) {
            $this->view = HMWPP_Classes_ObjController::getClass('HMWPP_Classes_DisplayController');
            return $this->view->getView($view, $obj);
        }

        return '';
    }

    /**
     * Called as menu callback to show the block
     *
     * @param  string $view
     * @throws Exception
     */
    public function show($view = null)
    {
        echo $this->getView($view);
    }

    /**
     * first function call for any class on form submit
     */
    protected function action()
    {
        // called within each class with the action
    }


    /**
     * initialize settings
     * Called from index
     *
     * @return void
     */
    public function hookInit()
    {
        //Show the menu for admins only
        HMWPP_Classes_ObjController::getClass('HMWPP_Controllers_Menu');
    }


    /**
     * Called on frontend. For disconnected users
     */
    public function hookFrontinit()
    { 
    }

    /**
     * Hook the admin head
     * This function will load the media in the header for each class
     *
     * @return void
     */
    public function hookHead()
    { 
    }

}
