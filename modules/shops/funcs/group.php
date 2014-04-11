<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3-6-2010 0:14
 */

if( ! defined( 'NV_IS_MOD_SHOPS' ) ) die( 'Stop!!!' );

if( empty( $groupid ) )
{
	Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
	exit();
}

$page_title = $lang_module['group_title'];
if( preg_match( "/^page\-([0-9]+)$/", ( isset( $array_op[2] ) ? $array_op[2] : "" ), $m ) )
{
	$page = ( int )$m[1];
}

$page_title = $global_array_group[$groupid]['title'];
$key_words = $global_array_group[$groupid]['keywords'];
$description = $global_array_group[$groupid]['description'];
$data_content = array();

$link = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=";
$base_url = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=group/" . $global_array_group[$groupid]['alias'];

// Fetch Limit
$db->sqlreset()->select( 'COUNT(*)' )->from( $db_config['prefix'] . "_" . $module_data . "_rows" )->where( "(group_id='" . $groupid . "' OR group_id REGEXP '^" . $groupid . "\\\,' OR group_id REGEXP '\\\," . $groupid . "\\\,' OR group_id REGEXP '\\\," . $groupid . "\$') AND status =1" );
$all_page = $db->query( $db->sql() )->fetchColumn();

$db->select( "id, listcatid, publtime, " . NV_LANG_DATA . "_title, " . NV_LANG_DATA . "_alias, " . NV_LANG_DATA . "_hometext, " . NV_LANG_DATA . "_address, homeimgalt, homeimgfile, homeimgthumb, product_code, product_price, product_discounts, money_unit, showprice" )->order( 'id DESC' )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

$result = $db->query( $db->sql() );

$data_content = GetDataInGroup( $result, $groupid );
$data_content['count'] = $all_page;

if( sizeof( $data_content['data'] ) < 1 and $page > 1 )
{
	Header( "Location: " . nv_url_rewrite( NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name, true ) );
	exit();
}

$pages = nv_alias_page( $page_title, $base_url, $all_page, $per_page, $page );

if( $page > 1 )
{
	$page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
	$description .= ' ' . $page;
}

$contents = call_user_func( $global_array_group[$groupid]['viewgroup'], $data_content, $pages );

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';