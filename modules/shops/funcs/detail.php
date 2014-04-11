<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3-6-2010 0:14
 */

if( ! defined( 'NV_IS_MOD_SHOPS' ) ) die( 'Stop!!!' );

if( empty( $id ) )
{
	Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true ) );
	exit();
}

// Thiet lap quyen xem chi tiet
$contents = '';
$publtime = 0;
$func_who_view = $global_array_cat[$catid]['who_view'];
$allowed = false;
if( $func_who_view == 0 )
{
	$allowed = true;
}
if( $func_who_view == 1 and defined( 'NV_IS_USER' ) )
{
	$allowed = true;
}
elseif( $func_who_view == 2 and defined( 'NV_IS_MODADMIN' ) )
{
	$allowed = true;
}
elseif( $func_who_view == 3 and ( ( defined( 'NV_IS_USER' ) and nv_is_in_groups( $user_info['in_groups'], $global_array_cat[$catid]['groups_view'] ) ) or defined( 'NV_IS_MODADMIN' ) ) )
{
	$allowed = true;
}

$sql = $db->query( 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows WHERE id = ' . $id . ' AND status=1' );
$data_content = $sql->fetch();
$data_shop = array();

if( empty( $data_content ) )
{
	$nv_redirect = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
	redict_link( $lang_module['detail_do_not_view'], $lang_module['redirect_to_back_shops'], $nv_redirect );
}

$page_title = $data_content[NV_LANG_DATA . '_title'];
$key_words = $data_content[NV_LANG_DATA . '_keywords'];
$description = $data_content[NV_LANG_DATA . '_hometext'];

if( $allowed )
{
	$sql = 'UPDATE ' . $db_config['prefix'] . '_' . $module_data . '_rows SET hitstotal=hitstotal+1 WHERE id=' . $id;
	$db->query( $sql );

	$catid = $data_content['listcatid'];
	$base_url_rewrite = nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_cat[$catid]['alias'] . '/' . $data_content[NV_LANG_DATA . '_alias'] . '-' . $data_content['id'] . $global_config['rewrite_exturl'], true );
	if( $_SERVER['REQUEST_URI'] != $base_url_rewrite )
	{
		Header( 'Location: ' . $base_url_rewrite );
		die();
	}

	$sql = $db->query( 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_units WHERE id = ' . $data_content['product_unit'] );
	$data_unit = $sql->fetch();
	$data_unit['title'] = $data_unit[NV_LANG_DATA . '_title'];

	// Xac dinh anh lon
	$homeimgfile = $data_content['homeimgfile'];
	if( $data_content['homeimgthumb'] == 1 )//image thumb
	{
		$data_content['homeimgthumb'] = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_name . '/' . $homeimgfile;
		$data_content['homeimgfile'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/' . $homeimgfile;
	}
	elseif( $data_content['homeimgthumb'] == 2 )//image file
	{
		$data_content['homeimgthumb'] = $data_content['homeimgfile'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/' . $homeimgfile;
	}
	elseif( $data_content['homeimgthumb'] == 3 )//image url
	{
		$data_content['homeimgthumb'] = $data_content['homeimgfile'] = $homeimgfile;
	}
	else//no image
	{
		$data_content['homeimgthumb'] = NV_BASE_SITEURL . 'themes/' . $module_info['template'] . '/images/' . $module_file . '/no-image.jpg';
	}

	$sql = $db->query( 'SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_sources WHERE sourceid = ' . $data_content['source_id'] );
	$data_temp = $sql->fetch();
	$data_content['source'] = $data_temp[NV_LANG_DATA . '_title'];

	// Fetch Limit
	$db->sqlreset()->select( ' id, ' . NV_LANG_DATA . '_title, ' . NV_LANG_DATA . '_alias, homeimgfile, homeimgthumb, addtime, product_code, product_price, product_discounts, money_unit, showprice, ' . NV_LANG_DATA . '_hometext ' )->from( $db_config['prefix'] . '_' . $module_data . '_rows' )->where( 'id!=' . $id . ' AND listcatid = ' . $data_content['listcatid'] . ' AND status=1' )->order( 'ID DESC' )->limit( $pro_config['per_row'] * 2 );
	$result = $db->query( $db->sql() );

	$data_others = array();
	while( list( $_id, $title, $alias, $homeimgfile, $homeimgthumb, $addtime, $product_code, $product_price, $product_discounts, $money_unit, $showprice, $hometext ) = $result->fetch( 3 ) )
	{
		if( $homeimgthumb == 1 )//image thumb
		{
			$thumb = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_name . '/' . $homeimgfile;
		}
		elseif( $homeimgthumb == 2 )//image file
		{
			$thumb = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/' . $homeimgfile;
		}
		elseif( $homeimgthumb == 3 )//image url
		{
			$thumb = $homeimgfile;
		}
		else//no image
		{
			$thumb = NV_BASE_SITEURL . 'themes/' . $module_info['template'] . '/images/' . $module_name . '/no-image.jpg';
		}

		$data_others[] = array(
			'id' => $_id,
			'title' => $title,
			'alias' => $alias,
			'homeimgthumb' => $thumb,
			'hometext' => $hometext,
			'addtime' => $addtime,
			'product_code' => $product_code,
			'product_price' => $product_price,
			'product_discounts' => $product_discounts,
			'money_unit' => $money_unit,
			'showprice' => $showprice,
			'link_pro' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_cat[$data_content['listcatid']]['alias'] . '/' . $alias . '-' . $_id . $global_config['rewrite_exturl'],
			'link_order' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=setcart&amp;id=' . $_id );
	}

	$array_other_view = array();
	if( ! empty( $_SESSION[$module_data . '_proview'] ) )
	{
		$arrid = array();
		foreach( $_SESSION[$module_data . '_proview'] as $id_i => $data_i )
		{
			if( $id_i != $id )
			{
				$arrid[] = $id_i;
			}
		}
		$arrtempid = implode( ',', $arrid );
		if( ! empty( $arrtempid ) )
		{
			// Fetch Limit
			$db->sqlreset()->select( 'id, ' . NV_LANG_DATA . '_title, ' . NV_LANG_DATA . '_alias, homeimgfile, homeimgthumb, addtime, product_code, product_price, product_discounts, money_unit, showprice, ' . NV_LANG_DATA . '_hometext' )->from( $db_config['prefix'] . '_' . $module_data . '_rows' )->where( 'id IN ( ' . $arrtempid . ') AND status=1' )->order( 'id DESC' )->limit( $pro_config['per_row'] * 2 );
			$result = $db->query( $db->sql() );

			while( list( $_id, $title, $alias, $homeimgfile, $homeimgthumb, $addtime, $product_code, $product_price, $product_discounts, $money_unit, $showprice, $hometext ) = $result->fetch( 3 ) )
			{
				if( $homeimgthumb == 1 )//image thumb
				{
					$thumb = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_name . '/' . $homeimgfile;
				}
				elseif( $homeimgthumb == 2 )//image file
				{
					$thumb = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_name . '/' . $homeimgfile;
				}
				elseif( $homeimgthumb == 3 )//image url
				{
					$thumb = $homeimgfile;
				}
				else//no image
				{
					$thumb = NV_BASE_SITEURL . 'themes/' . $module_info['template'] . '/images/' . $module_name . '/no-image.jpg';
				}

				$array_other_view[] = array(
					'id' => $_id,
					'title' => $title,
					'alias' => $alias,
					'homeimgthumb' => $thumb,
					'hometext' => $hometext,
					'addtime' => $addtime,
					'product_code' => $product_code,
					'product_price' => $product_price,
					'product_discounts' => $product_discounts,
					'money_unit' => $money_unit,
					'showprice' => $showprice,
					'link_pro' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $global_array_cat[$data_content['listcatid']]['alias'] . '/' . $alias . '-' . $_id . $global_config['rewrite_exturl'],
					'link_order' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=setcart&amp;id=' . $_id );
			}

		}
	}

    if( ! empty( $data_content['ratingdetail'] ) )
	{
		$data_content['ratingdetail'] = unserialize( $data_content['ratingdetail'] );
	}
	else
	{
		$data_content['ratingdetail'] = array(
			1 => 0,
			2 => 0,
			3 => 0,
			4 => 0,
			5 => 0 );
	}

	$total_value = array_sum( $data_content['ratingdetail'] );
    $total_value = ( $total_value == 0 )? 1 : $total_value;
	$data_content['percent_rate'] = array();

	$data_content['percent_rate'][1] = round( $data_content['ratingdetail'][1] * 100 / $total_value );
	$data_content['percent_rate'][2] = round( $data_content['ratingdetail'][2] * 100 / $total_value );
	$data_content['percent_rate'][3] = round( $data_content['ratingdetail'][3] * 100 / $total_value );
	$data_content['percent_rate'][4] = round( $data_content['ratingdetail'][4] * 100 / $total_value );
	$data_content['percent_rate'][5] = round( $data_content['ratingdetail'][5] * 100 / $total_value );

	$total_rate = $data_content['ratingdetail'][1] + ( $data_content['ratingdetail'][2] * 2 ) + ( $data_content['ratingdetail'][3] * 3 ) + ( $data_content['ratingdetail'][4] * 4 ) + ( $data_content['ratingdetail'][5] * 5 );
	$data_content['ratefercent_avg'] = round( $total_rate / $total_value, 1 );

	SetSessionProView( $data_content['id'], $data_content[NV_LANG_DATA . '_title'], $data_content[NV_LANG_DATA . '_alias'], $data_content['addtime'], NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $global_array_cat[$catid]['alias'] . '/' . $data_content[NV_LANG_DATA . '_alias'] . '-' . $data_content['id'], $data_content['homeimgthumb'] );

	// comment
	define( 'NV_COMM_ID', $data_content['id'] );
	define( 'NV_COMM_ALLOWED', $data_content['allowed_comm'] );
	require_once NV_ROOTDIR . '/modules/comment/comment.php';

	$contents = detail_product( $data_content, $data_unit, $data_others, $data_shop, $array_other_view );
}
else
{
	$nv_redirect = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;
	redict_link( $lang_module['detail_no_permission'], $lang_module['redirect_to_back_shops'], $nv_redirect );
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';