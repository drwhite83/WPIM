<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/w_inc.php';
include_once 'wlParamHandler.php';
include_once 'wl.php';
include_once 'wlFile.php';  include_once 'wEnvParam.php';
include_once 'Exception/wExceptions.php';
include_once 'wlPhotoline.php';


function getMiscInfo($id){	return wlPhotoline::extractMiscInfo($id);}
function downloadPhoto($id){	return wlPhotoline::downloadPhoto($id);}
function deletePhoto($id){	return wlPhotoline::deletePhoto($id);}

$GET_action = new wEnvParam(['name'=>'action']);
$action = $GET_action->value;
$GET_id = new wEnvParam(['name'=>'id']);
$actions = ['getMiscInfo', 'downloadPhoto', 'deletePhoto'];
$data = json_encode('DATA', JSON_UNESCAPED_UNICODE);


try
{
	if (wl::trimlen($GET_action->value) === false)
	{		throw new wException(['info'=>'ACTION_EMPTY', 'add_info'=>'No input param "action" in '.$_SERVER['REQUEST_URI']]);	}
	
	if (!in_array($GET_action->value, $actions))
	{		throw new wException(['info'=>'ACTION_NOT_REGISTERED', 'add_info'=>'Action "'.$GET_action->value.'" not registered in '.$_SERVER['REQUEST_URI']]);	}
	
	if (wl::trimlen($GET_id->value) === false)
	{		throw new wException(['info'=>'ACTION_NOT_REGISTERED', 'add_info'=>'No input param "id" in '.$_SERVER['REQUEST_URI']]);	}
	
	$data = json_encode($action($GET_id->value), JSON_UNESCAPED_UNICODE);
	echo '{"state":"success", "error":null, "data":'.$data.'}';
}
catch (Exception $E) 
{ 	
	echo '{"state":"error", "error":{"type":"'.$E->info.'", "trace":'.$E->getJSON().'}, "data":null}'; 
}


?>