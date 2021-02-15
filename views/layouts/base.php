<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;


AppAsset::register($this);
?>


<!-- <div class="wrap h-100 d-flex flex-column"> -->
    <?php echo $this->render('_header'); ?>
    <?php echo $content; ?>
<!-- </div> -->
