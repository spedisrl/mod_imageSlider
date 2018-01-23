<?php
# @Author: SPEDI srl
# @Date:   19-01-2018
# @Email:  sviluppo@spedi.it
# @Last modified by:   SPEDI srl
# @Last modified time: 22-01-2018
# @License: GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
# @Copyright: Copyright (C) SPEDI srl

// no direct access
defined('_JEXEC') or die ('Restricted access');

class modSPImageSliderHelper
{
    static function getImagesFromFolder(&$params) {

    	if(!is_numeric($max = $params->get('max_images'))) $max = 20;
        $folder = $params->get('image_folder');
        if(!$dir = @opendir($folder)) return null;
        while (false !== ($file = readdir($dir)))
        {
            if (preg_match('/.+\.(jpg|jpeg|gif|png)$/i', $file)) {
            	// check with getimagesize() which attempts to return the image mime-type
            	$path = JPath::clean(JPATH_ROOT.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$file);
            	if(getimagesize($path)!==FALSE) $files[filemtime($path).$file] = $file;
			}
        }
        closedir($dir);

        $sort = $params->get('sort_by');

        switch($sort) {
        	case 0:
        		shuffle($files);
        		break;
        	case 3:
        	case 4:
        		ksort($files);
        		break;
        	default:
        		natcasesort($files);
        		break;
        }

        if($sort == 2 || $sort == 4) {
        	$files = array_reverse($files);
        }

		$images = array_slice($files, 0, $max);

		$target = modSPImageSliderHelper::getSlideTarget($params->get('link'));

		foreach($images as $image) {
			$slides[] = (object) array('title'=>'', 'description'=>'', 'image'=>$folder.'/'.$image, 'link'=>$params->get('link'), 'alt'=>$image, 'target'=>$target);
		}

		return $slides;
    }

	static function getImagesFromDJImageSlider(&$params) {

		if(!is_numeric($max = $params->get('max_images'))) $max = 20;
        $catid = $params->get('category',0);

		// build query to get slides
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__djimageslider AS a');

		if (is_numeric($catid)) {
			$query->where('a.catid = ' . (int) $catid);
		}

		// Filter by start and end dates.
		$nullDate	= $db->Quote($db->getNullDate());
		$nowDate	= $db->Quote(JFactory::getDate()->format($db->getDateFormat()));

		$query->where('a.published = 1');
		$query->where('(a.publish_up = '.$nullDate.' OR a.publish_up <= '.$nowDate.')');
		$query->where('(a.publish_down = '.$nullDate.' OR a.publish_down >= '.$nowDate.')');

		switch($params->get('sort_by',1)) {
			case 1:
				$query->order('a.ordering ASC');
				break;
			case 2:
				$query->order('a.ordering DESC');
				break;
			case 3:
				$query->order('a.publish_up ASC');
				break;
			case 4:
				$query->order('a.publish_up DESC');
				break;
			default:
				$query->order('RAND()');
				break;
		}

		$db->setQuery($query, 0 , $max);
		$slides = $db->loadObjectList();

		foreach($slides as $slide){
			$slide->params = new JRegistry($slide->params);
			$slide->link = modSPImageSliderHelper::getSlideLink($slide);
			$slide->description = modSPImageSliderHelper::getSlideDescription($slide, $params->get('limit_desc'));
			$slide->alt = $slide->params->get('alt_attr', $slide->title);
			$slide->img_title = $slide->params->get('title_attr');
			$slide->target = $slide->params->get('link_target','');
			$slide->rel = $slide->params->get('link_rel','');
			if(empty($slide->target)) $slide->target = modSPImageSliderHelper::getSlideTarget($slide->link);
		}

		return $slides;
    }

	static function getSlideLink(&$slide) {
		$link = '';
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();

		switch($slide->params->get('link_type', '')) {
			case 'menu':
				if ($menuid = $slide->params->get('link_menu',0)) {

					$menu = $app->getMenu();
					$menuitem = $menu->getItem($menuid);
					if($menuitem) switch($menuitem->type) {
						case 'component':
							$link = JRoute::_($menuitem->link.'&Itemid='.$menuid);
							break;
						case 'url':
						case 'alias':
							$link = JRoute::_($menuitem->link);
							break;
					}
				}
				break;
			case 'url':
				if($itemurl = $slide->params->get('link_url',0)) {
					$link = JRoute::_($itemurl);
				}
				break;
			case 'article':
				if ($artid = $slide->params->get('id',$slide->params->get('link_article',0))) {
					jimport('joomla.application.component.model');
					require_once(JPATH_BASE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_content'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'route.php');
					JModelLegacy::addIncludePath(JPATH_BASE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_content'.DIRECTORY_SEPARATOR.'models');
					$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request'=>true));
					$model->setState('params', $app->getParams());
					$model->setState('filter.article_id', $artid);
					$model->setState('filter.article_id.include', true); // Include
					$items = $model->getItems();
					if($items && $item = $items[0]) {
						$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
						$link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid));
						$slide->introtext = $item->introtext;
					}
				}
				break;
		}

		return $link;
	}

	static function getSlideDescription($slide, $limit) {
		$sparams = new JRegistry($slide->params);
		if($sparams->get('link_type','')=='article' && empty($slide->description)){ // if article and no description then get introtext as description
			if(isset($slide->introtext)) $slide->description = $slide->introtext;
		}

		$desc = strip_tags($slide->description);

		if($limit && $limit - strlen($desc) < 0) {
			// don't cut in the middle of the word unless it's longer than 20 chars
			if($pos = strpos($desc, ' ', $limit)) $limit = ($pos - $limit > 20) ? $limit : $pos;
			// cut text and add dots
			if(function_exists('mb_substr')) {
				$desc = mb_substr($desc, 0, $limit);
			} else {
				$desc = substr($desc, 0, $limit);
			}
			if(preg_match('/[a-zA-Z0-9]$/', $desc)) $desc.='&hellip;';
			$desc = '<p>'.nl2br($desc).'</p>';
		} else { // no limit or limit greater than description
			$desc = $slide->description;
		}

		return $desc;
	}


	static function getSlideTarget($link) {

		if(preg_match("/^http/",$link) && !preg_match("/^".str_replace(array('/','.','-'), array('\/','\.','\-'),JURI::base())."/",$link)) {
			$target = '_blank';
		} else {
			$target = '_self';
		}

		return $target;
	}

}
