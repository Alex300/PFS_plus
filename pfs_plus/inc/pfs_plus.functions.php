<?php
/**
 * PFS Plus plugin for Cotonti Siena CMF
 *
 * @package PFS
 * @author Alex
 * @copyright (c) 2013 Alex http://portal30.ru
 */

/**
 * @param $fid
 * @param string $tpl
 * @param string $order
 * @return bool
 * @todo генерация произвольных миниатюр
 */
function pfs_gallery($fid, $tpl = 'pfs_plus.gallery', $order = 'pfs_order DESC, pfs_file ASC'){
    global $db, $db_pfs, $cfg;

    $gd_supported = array('jpg', 'jpeg', 'png', 'gif');
    if ($cfg['pfs']['th_amode'] == 'Disabled') return false;

    $fid = (int)$fid;
    if (!$fid || $fid < 0) return false;

    $sql_pfs = $db->query("SELECT * FROM $db_pfs WHERE pfs_folderid=$fid ORDER BY $order");

    $t = new XTemplate(cot_tplfile($tpl, 'plug'));

    $iji=0;

    foreach ($sql_pfs->fetchAll() as $row) {
//        $pfs_id = $row['pfs_id'];
//        $pfs_file = $row['pfs_file'];
//        $pfs_date = $row['pfs_date'];
        $pfs_dir_user = cot_pfs_path($row['pfs_userid']);
        $thumbs_dir_user = cot_pfs_thumbpath($row['pfs_userid']);

        $pfs_extension = $row['pfs_extension'];

        $pfs_desc = htmlspecialchars($row['pfs_desc']);
        $pfs_fullfile = $pfs_dir_user.$row['pfs_file'];
        $pfs_filesize = $row['pfs_size'];
//        $pfs_icon = $icon[$pfs_extension];

        $dotpos = mb_strrpos($row['pfs_file'], ".")+1;
        $pfs_realext = mb_strtolower(mb_substr($row['pfs_file'], $dotpos, 5));
        unset($add_thumbnail, $add_image);
//        $add_file = ($standalone) ? cot_rc('pfs_link_addfile') : '';

        if ($pfs_extension!=$pfs_realext)
        {
            $db->update($db_pfs, array('pfs_extension' => $pfs_realext), "pfs_id={$row['pfs_id']}");
            $pfs_extension = $pfs_realext;
        }

        if (in_array( mb_strtolower($pfs_extension), $gd_supported))
        {
            if (!file_exists($thumbs_dir_user.$row['pfs_file']) && file_exists($pfs_dir_user.$row['pfs_file']))
            {
                $th_colortext = array(hexdec(mb_substr($cfg['pfs']['th_colortext'],0,2)), hexdec(mb_substr($cfg['pfs']['th_colortext'],2,2)), hexdec(mb_substr($cfg['pfs']['th_colortext'],4,2)));
                $th_colorbg = array(hexdec(mb_substr($cfg['pfs']['th_colorbg'],0,2)), hexdec(mb_substr($cfg['pfs']['th_colorbg'],2,2)), hexdec(mb_substr($cfg['pfs']['th_colorbg'],4,2)));
                cot_imageresize($pfs_dir_user . $row['pfs_file'], $thumbs_dir_user . $row['pfs_file'],
                    $cfg['pfs']['th_x'], $cfg['pfs']['th_y'], 'fit', $th_colorbg,
                    $cfg['pfs']['th_jpeg_quality'], true);
            }

            $pfs_icon = cot_rc('pfs_link_thumbnail', array(
                'thumbpath' => $thumbs_dir_user,
                'pfs_fullfile' => $pfs_fullfile,
                'pfs_desc' => $pfs_desc,
                'pfs_file' => $row['pfs_file'],
            ));

        }else{
            continue;
        }

        $t-> assign(array(
            'PFS_ROW_NUM' => $iji + 1,
            'PFS_ROW_ID' => $row['pfs_id'],
            'PFS_ROW_FILE' => $row['pfs_file'],
            'PFS_ROW_DATE' => cot_date('datetime_medium', $row['pfs_date']),
            'PFS_ROW_DATE_STAMP' => $row['pfs_date'],
            'PFS_ROW_EXT' => $pfs_extension,
            'PFS_ROW_DESC' => $pfs_desc,
//            'PFS_ROW_TYPE' => $filedesc[$pfs_extension],
            'PFS_ROW_FILE_URL' => $pfs_fullfile,
            'PFS_ROW_SIZE' => cot_build_filesize($pfs_filesize, 1),
            'PFS_ROW_SIZE_BYTES' => $pfs_filesize,
            'PFS_ROW_ICON' => $pfs_icon,
            'PFS_ROW_COUNT' => $row['pfs_count'],
        ));

        $t->parse('MAIN.PFS_ROW');
        $iji++;
    }

    $t->parse();

    return $t->text();
}
