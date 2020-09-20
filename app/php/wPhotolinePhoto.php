<?php
//подключение w
include_once $_SERVER['DOCUMENT_ROOT'].'/w_inc.php';
include_once 'wl.php';
include_once 'wlURL.php';
include_once 'wlFile.php';
include_once 'wCommonParamsObj.php';

class wPhotolinePhoto extends wCommonParamsObj
{

protected $_params = ['id'=>null, 'title'=>null, 'date'=>null, 'author_id'=>null, 'author_name'=>'', 'description'=>null, 'views'=>null, 
											'marks'=>null, 'rating'=>null, 'in_best'=>0, 'w_rating'=>0];


public function __construct($in_params = []){	$this->setParams($in_params, $this->_params);}

}?>