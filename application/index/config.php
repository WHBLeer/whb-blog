<?php

return [
   //输出替换
   'view_replace_str'  =>  [
	    '__INDEXRES__'=>SITE_URL.'/static/index',
	 	'__EDITORMD__'=>SITE_URL.'/static/editorMd',
	],
	
	//分页配置
    'paginate'			=> [
        'type'      => 'Bpagination',
        'var_page'  => 'page',
        'list_rows' => 12,
    ],
];
