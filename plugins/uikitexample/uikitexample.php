<?php
defined('_JEXEC') or die;
use Joomla\CMS\Plugin\CMSPlugin;
use YOOtheme\Application;

class plgSystemUikitexample extends CMSPlugin
{	
    function onAfterInitialise()
    {
        if (!class_exists(Application::class, false)) {
            return;
        }

        $app = Application::getInstance();
        $app->load(__DIR__ . '/bootstrap.php');
    }
}
