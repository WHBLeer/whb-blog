<?php

return [
   //输出替换
   'view_replace_str'  =>  [
	    '__ADMINRES__'=>SITE_URL.'/static/admin/assets',
	    '__IMG__'=>SITE_URL,
	 	'__EDITORMD__'=>SITE_URL.'/static/editorMd',
	],
	//分页配置
    'paginate'			=> [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
];
