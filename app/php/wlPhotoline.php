<?php
//подключение w
include_once $_SERVER['DOCUMENT_ROOT'].'/w_inc.php';
include_once 'wl.php'; include_once 'wlParamHandler.php'; include_once 'wCommonParamsObj.php';
include_once 'wlURL.php'; include_once 'wEnvParam.php';
include_once 'wlFile.php'; include_once 'wlDir.php';
include_once 'DB/wlSQLite.php';
include_once 'UI/wlUI.php';
include_once 'Exception/wExceptions.php';
include_once 'wPhotolinePhoto.php';

class wlPhotoline
{

private static $new_photos_page_url = 'http://www.photoline.ru/1/newphoto.php';
private static $new_photos_per_page = 40;
private static $photo_img_path = 'http://www.photoline.ru/critic/picpart/';
private static $photo_page_path = 'http://www.photoline.ru/photo/';
private static $author_page_path = 'http://www.photoline.ru/author/';

private static $photos_save_path = 'B:\www\lifeinfo\proj\grab\photo\photoline.ru\img';


public static function getAuthorPageURL($author_id){	return self::$author_page_path.$author_id;}
public static function getPhotoPageURL($photo_id){	return self::$photo_page_path.$photo_id;}
public static function getPhotoFileURL($photo_id)
{
	if (strlen($photo_id) < 10) $add_dir = '0'.substr($photo_id, 0, 3);
	else $add_dir = substr($photo_id, 0, 4);
	return self::$photo_img_path.$add_dir.'/'.$photo_id.'.jpg';
}


public static function getPageUrl($page_num)
{	
	$page_num = (int)$page_num;
	if ($page_num < 1) $page_num = 1;
	return self::$new_photos_page_url.'?now='.(self::$new_photos_per_page * $page_num - self::$new_photos_per_page);
}


public static function prepareShowPhotosObjs($Photos_list)
{
	$prepared_show_photos_list = [];
	foreach ($Photos_list as $k => $PhotoObj)
	{
		$id = $PhotoObj->id;
		$title = $PhotoObj->title;
		$date = $PhotoObj->date;
		$author_id = $PhotoObj->author_id;
		$author_name = $PhotoObj->author_name;
		$author_page_url = self::getAuthorPageURL($author_id);
		$photo_page_url = self::getPhotoPageURL($id);
		$img_url = self::getPhotoFileURL($id);
		$HTML_img = '<img id="photo_preview_'.$id.'" data-action="showPhoto" data-photo-id="'.$id.'" src="'.$img_url.'" /></a>';
		$HTML_id = '<p>ID: <a target="_blank" href="'.$photo_page_url.'">'.$id.'</a>, '.$date.'</p>';
		$HTML_other = '<p><a target="_blank" href="'.$author_page_url.'">'.$author_name.'</a></p>';
		$HTML_title = '<h1>'.$title.'</h1>';
		$HTML_main_info = $HTML_id.$HTML_other.$HTML_title;
		$HTML_show_misc_info = '<input type="button" class="action_btn" data-action="showMiscInfo" id="show_misc_info_btn_'.$id.'" data-photo-id="'.$id.'" value="Show info">';
		$HTML_file_delete = '<input type="button" class="action_btn delete_photo_btn" id="photo_file_action_btn_'.$id.'" data-action="deletePhoto" data-photo-id="'.$id.'" value="Delete">';
		$HTML_file_download = '<input type="button" class="action_btn download_photo_btn" id="photo_file_action_btn_'.$id.'" data-action="downloadPhoto" data-photo-id="'.$id.'" value="Download">';
		$HTML_file_actions = self::isDownloaded($id) ? $HTML_file_delete : $HTML_file_download;
		$HTML_photo_actions = '<div class="photo_actions">'.$HTML_show_misc_info.$HTML_file_actions.'</div>';
		$HTML_photo_misc_info = '<div class="photo_misc_info" id="photo_misc_info_'.$id.'"></div>';
		$prepared_show_photos_list[$k] = '<div class="photo_block">'.$HTML_img.$HTML_main_info.$HTML_photo_actions.$HTML_photo_misc_info.'</div>';
	}
	return $prepared_show_photos_list;
}


public static function createPhotosObjsFromURL($url)
{
	$Photos_list = [];
	$photos_html_list = self::getPhotosHtml($url);
	foreach ($photos_html_list as $i => $photo_html) {	$Photos_list[] = self::createPhotoObj($photo_html);	}
//	wl::d($Photos_list);
	return $Photos_list;
}


public static function getPhotosHtml($url)
{
	$photos_html = [];
	$data = iconv('KOI8-R', 'UTF-8//ignore', file_get_contents($url));
	$data = html_entity_decode(preg_replace('/&#(\d+)([^;])/is', '&#$1;$2', $data), ENT_QUOTES);
	preg_match_all('{<div id="?photo_cell"?.+?</div>}is', $data, $matches);
	foreach ($matches[0] as $photo_html)		
	{
		if (!preg_match('/\d+\.jpg/is', $photo_html)) continue;
		$photos_html[] = $photo_html;
	}
	return $photos_html;
}


public static function createPhotoObj($photo_html)
{
	$Photo = new wPhotolinePhoto();
	preg_match('{(\d*)\?rzd=}is', $photo_html, $mtch);
	$Photo->id = isset($mtch[1]) ? $mtch[1] : null;
	
	preg_match('{alt="(.*?)</a>}is', $photo_html, $mtch);
	$Photo->title = isset($mtch[1]) ? trim(preg_replace('{"\s>$}is', '', $mtch[1])) : null;
	$Photo->title = preg_replace('{</?[A-Za-z0-9]+>}is', '', $Photo->title);
	
	preg_match('{class=stext>(\d{1,2})\.(\d{2})\.(\d{4})}is', $photo_html, $mtch);
	$Y = isset($mtch[3]) ? $mtch[3] : '0000';
	$m = isset($mtch[2]) ? $mtch[2] : '00';
	$d = isset($mtch[1]) ? wl::fillZero(2, $mtch[1]) : '00';
	$Photo->date = $Y.'-'.$m.'-'.$d;
	
	preg_match('{author/(\d*)}is', $photo_html, $mtch);
	$Photo->author_id = isset($mtch[1]) ? $mtch[1] : null;
	
	preg_match('{author/(\d*).+?>(.+?)<}is', $photo_html, $mtch);
	$Photo->author_name = isset($mtch[2]) ? $mtch[2] : null;
	
	preg_match('{iz\.gif}is', $photo_html, $mtch);
	$Photo->in_best = isset($mtch[0]) ? 1 : 0;
	return $Photo;
}


public static function extractMiscInfo($photo_id)
{
	$misc_info = ['part'=>null, 'rating'=>null, 'marks'=>null, 'views'=>null, 'comments'=>null];
	$html = iconv('KOI8-R', 'windows-1251//ignore', 
								file_get_contents('http://www.photoline.ru/cgi-bin/cr/info1.pl?ind='.$photo_id));
	
	$part_pattern = iconv('UTF-8', 'windows-1251', 'Раздел');
	if (preg_match('{'.$part_pattern.'</td><td>(.+?)</td>}is', $html, $matches)) $misc_info['part'] = iconv('windows-1251', 'UTF-8', $matches[1]);
	
	$rating_pattern = iconv('UTF-8', 'windows-1251', 'Рейтинг');
	if (preg_match('{'.$rating_pattern.'</td><td>(\d+\.?\d*)}is', $html, $matches)) $misc_info['rating'] = $matches[1];
	
	$marks_count_pattern = iconv('UTF-8', 'windows-1251', 'Оценок');
	if (preg_match('{'.$marks_count_pattern.'</td><td>(\d+)}is', $html, $matches)) $misc_info['marks'] = $matches[1];
	
	$views_pattern = iconv('UTF-8', 'windows-1251', 'Просмотров');
	if (preg_match('{'.$views_pattern.'</td><td>(\d+)}is', $html, $matches)) $misc_info['views'] = $matches[1];
	
	$comments_pattern = iconv('UTF-8', 'windows-1251', 'Комментариев');
	if (preg_match('{'.$comments_pattern.'</td><td>(\d+)}is', $html, $matches)) $misc_info['comments'] = $matches[1];
	return $misc_info;
}


public static function extractDescrition($photo_id)
{
	$html = iconv('KOI8-R', 'windows-1251//ignore', file_get_contents('http://www.photoline.ru/photo/'.$photo_id));
	if (!preg_match('{<div itemprop="description">(.+?)</div>}is', $html, $matches)) return null;
	return iconv('windows-1251', 'UTF-8', $matches[1]);
}


public static function isDownloaded($photo_id)
{	return is_file(self::$photos_save_path.'/'.$photo_id.'.jpg');}


public static function downloadPhoto($photo_id)
{
	$remote_file = self::getPhotoFileURL($photo_id);
	$local_file = self::$photos_save_path.'/'.$photo_id.'.jpg';
	$result = false;
	if (!file_exists($local_file)) $result = copy($remote_file, $local_file);
	return $result;
}


public static function deletePhoto($photo_id)
{
	if (!preg_match('/^\d+$/is', $photo_id)) 
	{	throw new wException(['info'=>'photo_id contains illegal chars', 'add_info'=>'photo_id: '.$photo_id]);	}
	$local_file = self::$photos_save_path.'/'.$photo_id.'.jpg';
	$result = false;
	if (file_exists($local_file)) $result = unlink($local_file);
	return $result;
}








//public static function getPartTitleById(&$parts, $part_id = '')
//{
//	if (trim($part_id) == '') return 'Все разделы';
//	return wl::arrKey($parts, $part_id, '');
//}


//public static function addPhotos($in_params=[])
//{
//	$_params = ['page_url'=>null, 'DB_photos_table'=>'photos'];
//	wlParamHandler::setParams($in_params, $_params);
////	echo '<p>'.wlPhotolineDB::getDBFile().': '.wlPhotolineDB::getPhotosTable().'</p>';
//	$Photos_list = self::createPhotosFromPage(['page_url'=>$_params['page_url']]);
//	if (!$Photos_list)	{		echo '<h3>No photos extracted on this page</h3>';		return false;	}
//	$DB = new PDO('sqlite:'.wlPhotolineDB::getDBFilePath());
//	$table_name = wlPhotolineDB::getDBPhotosTableName();
//	foreach ($Photos_list as $Photo)	
//	{
//		$SQL = 'INSERT INTO '.$table_name.' VALUES ('.wlSQLite::getObjParamsSQL($Photo->getParams(), true).')';
//		$DB->exec($SQL);
//		wl::D($DB->errorInfo());
//	}
//	$DB = null;
//}


//public static function getPart($photo_html)
//{
//	preg_match('{category/(.*?)"}is', $photo_html, $mtch);
//	$Photo->part_id = isset($mtch[1]) ? $mtch[1] : null;
//}

}?>