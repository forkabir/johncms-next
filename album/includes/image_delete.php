<?php

defined('_IN_JOHNCMS') or die('Error: restricted access');

require('../system/head.php');

// Удалить картинку
if ($img && $user['id'] == $user_id || $rights >= 6) {
    /** @var PDO $db */
    $db = App::getContainer()->get(PDO::class);

    $req = $db->query("SELECT * FROM `cms_album_files` WHERE `id` = '$img' AND `user_id` = '" . $user['id'] . "' LIMIT 1");
    if ($req->rowCount()) {
        $res = $req->fetch();
        $album = $res['album_id'];
        echo '<div class="phdr"><a href="?act=show&amp;al=' . $album . '&amp;user=' . $user['id'] . '"><b>' . _t('Photo Album') . '</b></a> | ' . _t('Delete image') . '</div>';
        //TODO: Администрация не должна удалять фотки старших по должности
        if (isset($_POST['submit'])) {
            // Удаляем файлы картинок
            @unlink('../files/users/album/' . $user['id'] . '/' . $res['img_name']);
            @unlink('../files/users/album/' . $user['id'] . '/' . $res['tmb_name']);

            // Удаляем записи из таблиц
            $db->exec("DELETE FROM `cms_album_files` WHERE `id` = '$img'");
            $db->exec("DELETE FROM `cms_album_votes` WHERE `file_id` = '$img'");
            $db->exec("DELETE FROM `cms_album_comments` WHERE `sub_id` = '$img'");

            header('Location: ?act=show&al=' . $album . '&user=' . $user['id']);
        } else {
            echo '<div class="rmenu"><form action="?act=image_delete&amp;img=' . $img . '&amp;user=' . $user['id'] . '" method="post">' .
                '<p>' . _t('Are you sure you want to delete this image?') . '</p>' .
                '<p><input type="submit" name="submit" value="' . _t('Delete') . '"/></p>' .
                '</form></div>' .
                '<div class="phdr"><a href="?act=show&amp;al=' . $album . 'user=' . $user['id'] . '">' . _t('Cancel') . '</a></div>';
        }
    } else {
        echo functions::display_error(_t('Wrong data'));
    }
}
