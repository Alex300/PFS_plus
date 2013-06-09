<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=parser.last
[END_COT_EXT]
==================== */
/**
 * PFS Plus plugin for Cotonti Siena CMF
 *
 * @package PFS
 * @author Alex
 * @copyright (c) 2013 Alex http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('pfs_plus', 'plug');
require_once cot_incfile('pfs', 'module');

if (!function_exists('pfs_gallery_bbcode'))
{
    // Replaces pfs_gallery bbcode with the thumbnail galery
    function pfs_gallery_bbcode($m){
        global $db, $db_attach, $att_item_cache;

        parse_str(htmlspecialchars_decode($m[1]), $params);

        if (!isset($params['f']) || !is_numeric($params['f']) || $params['f'] <= 0)
        {
            return $m[0].'err';
        }
        $params['f'] = (int) $params['f'];
//        $src = att_thumb($params['id'], $params['width'], $params['height'], $params['frame']);
        $html = pfs_gallery($params['f']);
        if (!$html) return $m[0].'err2';

        return $html;
    }

}


$text = preg_replace_callback('`\[pfs_gallery\?(.+?)\]`i', 'pfs_gallery_bbcode', $text);
//$text = preg_replace_callback('`\[att_image\?(.+?)\]`i', 'att_image_bbcode', $text);
