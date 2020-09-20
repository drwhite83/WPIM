const actions = {

async deletePhoto(id)
{
	try {
		const url = 'app/php/fetch.php?action=deletePhoto&id=' + id;
		l(url);
		const response = await fetch(url);
		if (!response.ok) throw new Error('ERROR FETCH RESPONSE: HTTP CODE: ' + response.status + ', URL: ' + response.url);
		const JSON = await response.json();
		if (JSON.error !== null) {l(JSON.error); throw new Error('ERROR FETCH ACTION');}
		l(JSON.data);
		const DOM_file_action_btn = document.getElementById('photo_file_action_btn_'+id);
		DOM_file_action_btn.value = 'Download';
		DOM_file_action_btn.classList.remove('delete_photo_btn');
		DOM_file_action_btn.classList.add('download_photo_btn');
		DOM_file_action_btn.dataset.action = 'downloadPhoto';
		
//		return JSON.data;
	}
	catch (e)	{		 alert(e);	}
},


async downloadPhoto(id)
{
	try {
		const url = 'app/php/fetch.php?action=downloadPhoto&id=' + id;
		l(url);
		const response = await fetch(url);
		if (!response.ok) throw new Error('ERROR FETCH RESPONSE: HTTP CODE: ' + response.status + ', URL: ' + response.url);
		const JSON = await response.json();
		if (JSON.error !== null) {l(JSON.error); throw new Error('ERROR FETCH ACTION');}
		l(JSON.data);
		const DOM_file_action_btn = document.getElementById('photo_file_action_btn_'+id);
		DOM_file_action_btn.value = 'Delete';
		DOM_file_action_btn.classList.remove('download_photo_btn');
		DOM_file_action_btn.classList.add('delete_photo_btn');
		DOM_file_action_btn.dataset.action = 'deletePhoto';
		
//		return JSON.data;
	}
	catch (e)	{		 alert(e);	}
},


async getMiscInfo(id)
{
	try {
		const url = 'app/php/fetch.php?action=getMiscInfo&id=' + id;
		const response = await fetch(url);
		if (!response.ok) throw new Error('ERROR FETCH RESPONSE: HTTP CODE: ' + response.status + ', URL: ' + response.url);
		const JSON = await response.json();
		if (JSON.error !== null) {l(JSON.error); throw new Error('ERROR FETCH ACTION');}
//		l(JSON.data);
		return JSON.data;
	}
	catch (e)	{		 alert(e);	}
},


async showMiscInfo(id)
{
	l('showMiscInfo('+id+')');
	let misc_info = await this.getMiscInfo(id);
	misc_info = '<p>'+misc_info.part+'</p><table class="photo_misc_info_tbl"><tr><th>rating</th><th>marks</th><th>views</th><th>comments</th></tr>'+
		'<tr><td>'+misc_info.rating+'</td><td>'+misc_info.marks+'</td><td>'+misc_info.views+'</td><td>'+misc_info.comments+'</td></tr></table>';
	document.getElementById('photo_misc_info_'+id).innerHTML = misc_info;
	document.getElementById('show_misc_info_btn_'+id).value = 'Update info';
	
},


showPhoto(id)
{
	clearPhotoShowBlock();
	const DOM_photo_preview = document.getElementById('photo_preview_'+id);
	const DOM_photo_show_block = document.createElement('div');
	DOM_photo_show_block.setAttribute('class', 'photo_show_block');
	DOM_photo_show_block.setAttribute('onclick', 'this.remove()');
	DOM_photo_show_block.innerHTML = '<img src='+DOM_photo_preview.src+'>';
	document.body.appendChild(DOM_photo_show_block);
},

}//actions


function wPhotoline()
{
	document.addEventListener('keydown', Ev => {if (Ev.keyCode == 27) clearPhotoShowBlock();});
	document.addEventListener('click',  Ev => {
		if (actions[Ev.target.dataset.action] !== undefined) actions[Ev.target.dataset.action](event.target.dataset.photoId);
		else 
		{	l('event.target.dataset.action: '+event.target.dataset.action+', actions['+event.target.dataset.action+']: '+actions[event.target.dataset.action]);		}
	});
}


function clearPhotoShowBlock()
{
	let blocks = document.getElementsByClassName('photo_show_block');
	let count = blocks.length;
	for (let i = 0; i < count; i++) { if (blocks[i]) blocks[i].remove(); }
}