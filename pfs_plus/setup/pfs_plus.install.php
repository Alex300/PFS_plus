<?php
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('pfs', 'module');

// Add groups fields if missing
$dbres = $db->query("SHOW COLUMNS FROM `$db_pfs` WHERE `Field` = 'pfs_order'");
if ($dbres->rowCount() == 0){
    $db->query("ALTER TABLE `$db_pfs` ADD COLUMN `pfs_order` INT DEFAULT 0");
}
$dbres->closeCursor();
