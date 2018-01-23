<?php
# @Author: SPEDI srl
# @Date:   19-01-2018
# @Email:  sviluppo@spedi.it
# @Last modified by:   SPEDI srl
# @Last modified time: 19-01-2018
# @License: GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
# @Copyright: Copyright (C) SPEDI srl

// no direct access
defined('_JEXEC') or die('Restricted access');
// defined('DS') or define('DS', DIRECTORY_SEPARATOR);

JLoader::register('modSPImageSliderHelper', __DIR__ . '/helper.php');

$app 			= JFactory::getApplication();
$tmpl 		= $app->getTemplate();
$document = JFactory::getDocument();

// taking the slides from the source
if($params->get('slider_source')==1) {
	jimport('joomla.application.component.helper');
	if(!JComponentHelper::isEnabled('com_djimageslider', true)){
		$app->enqueueMessage(JText::_('MOD_SPIMAGESLIDER_NO_COMPONENT'),'notice');
		return;
	}
	$slides = modSPImageSliderHelper::getImagesFromDJImageSlider($params);
	if($slides==null) {
		$app->enqueueMessage(JText::_('MOD_SPIMAGESLIDER_NO_CATEGORY_OR_ITEMS'),'notice');
		return;
	}
} else {
	$slides = modSPImageSliderHelper::getImagesFromFolder($params);
	if($slides==null) {
		$app->enqueueMessage(JText::_('MOD_SPIMAGESLIDER_NO_CATALOG_OR_FILES'),'notice');
		return;
	}
}

/* params */
// $slider_source   = $params->get('slider_source');
// $place					 = $params->get('place');
// $date					   = $params->get('date');
// $show_title 		 = $params->get('show_title');
// $link_title 		 = $params->get('link_title');
// $show_desc 			 = $params->get('show_desc');
// $limit_desc 		 = $params->get('limit_desc');
// $image_width 		 = $params->get('image_width');
$image_height 	 = $params->get('image_height');
$show_arrows     = $params->get('show_arrows');
$show_custom_nav = $params->get('show_custom_nav');

JHtml::_('jquery.framework');

switch ($params->get('layout')) {
	case '_:grid':
		$document->addStyleSheet(JURI::base(true).'/modules/'.$module->module.'/css/'.substr($params->get('layout', 'default'), 2, strlen($params->get('layout', 'default'))).'/default.min.css?v=1.0.0');
		break;
	case '_:grid-vertical':
		$document->addStyleSheet(JURI::base(true).'/modules/'.$module->module.'/css/'.substr($params->get('layout', 'default'), 2, strlen($params->get('layout', 'default'))).'/default.min.css?v=1.0.0');
		$extensionPath = '/templates/'.$tmpl.'/dist/swiper/';
    if(file_exists(JPATH_SITE.$extensionPath)){
    	$document->addStyleSheet(JUri::base(true).'/templates/'.$tmpl.'/dist/swiper/swiper.min.css');
      $document->addScript(JUri::base(true).'/templates/'.$tmpl.'/dist/swiper/swiper.min.js', 'text/javascript', true, false);
    } else{
      $document->addStyleSheet(JUri::base(true).'/modules/'.$module->module.'/dist/swiper/swiper.min.css');
    	$document->addScript(JUri::base(true).'/modules/'.$module->module.'/dist/swiper/swiper.min.js', 'text/javascript', true, false);
    }
		break;
	case '_:bootstrap-carousel':
		$document->addStyleSheet(JURI::base(true).'/modules/'.$module->module.'/css/'.substr($params->get('layout', 'default'), 2, strlen($params->get('layout', 'default'))).'/default.min.css?v=1.0.0');
		break;
	case '_:swiper-fade-effect':
		$document->addStyleSheet(JURI::base(true).'/modules/'.$module->module.'/css/'.substr($params->get('layout', 'default'), 2, strlen($params->get('layout', 'default'))).'/default.min.css?v=1.0.0');
		$extensionPath = '/templates/'.$tmpl.'/dist/swiper/';
    if(file_exists(JPATH_SITE.$extensionPath)){
    	$document->addStyleSheet(JUri::base(true).'/templates/'.$tmpl.'/dist/swiper/swiper.min.css');
      $document->addScript(JUri::base(true).'/templates/'.$tmpl.'/dist/swiper/swiper.min.js', 'text/javascript', true, false);
    } else{
      $document->addStyleSheet(JUri::base(true).'/modules/'.$module->module.'/dist/swiper/swiper.min.css');
    	$document->addScript(JUri::base(true).'/modules/'.$module->module.'/dist/swiper/swiper.min.js', 'text/javascript', true, false);
    }
		break;
}

require JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default'));
