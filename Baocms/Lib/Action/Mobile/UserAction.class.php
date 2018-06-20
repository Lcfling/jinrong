<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/6/20
 * Time: 下午7:01
 */

if (!file_exists(BASE_PATH . '/attachs/install.lock')) {
    header("Location: install/index.php");
    die;
}
