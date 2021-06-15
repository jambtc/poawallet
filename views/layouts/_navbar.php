<?php

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;

use yii\helpers\Url;
use yii\helpers\Html;
// Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';

// mi serve per far caricare bootstrap4
NavBar::begin();
NavBar::end();

?>


<div class="nav-menu" style="display: none;">
	<nav class="menu">

		<!-- Menu navigation start -->
		<div class="nav-container">
			<ul class="main-menu">
				<li class="">
					<a href="<?= Url::to(['/wallet/index']) ?>">
						<!-- <img src="css/img/content/icons/2.png" alt=""> -->
						<i class="fas fa-wallet fa-lg text-primary"></i>
						<strong class="special"><?= Yii::t('app','My Wallet') ?></strong>
					</a>
				</li>
				<li class="">
					<a href="<?= Url::to(['/send/index']) ?>">
						<!-- <img src="css/img/content/icon1.png" alt=""> -->
						<i class="far fa-paper-plane fa-lg text-primary"></i>
						<strong class="special"><?= Yii::t('app','Send') ?></strong>
					</a>
				</li>
				<li class="">
					<a href="<?= Url::to(['/receive/index']) ?>">
						<!-- <img src="css/img/content/icon2.png" alt=""> -->
						<i class="fas fa-download fa-lg text-primary"></i>
						<strong class="special"><?= Yii::t('app','Receive') ?></strong>
					</a>
				</li>
				<li class="">
					<a href="<?= Url::to(['/tokens/index']) ?>">
						<i class="fa fa-list fa-lg text-primary"></i>
						<strong class="special"><?= Yii::t('app','Transactions') ?></strong>
					</a>
				</li>
				<li class="">
					<a href="<?= Url::to(['/settings/index']) ?>">
						<i class="fa fa-network-wired fa-lg text-primary"></i>
						<strong class="special"><?= Yii::t('app','Network') ?></strong>
					</a>
				</li>
				<li class="">
					<a href="<?= Url::to(['users/view','id'=>app\components\WebApp::encrypt(Yii::$app->user->identity->id)]); ?>">
						<i class="fas fa-user-circle fa-lg text-primary"></i>
						<strong class="special"><?= Yii::t('app','Profile') ?></strong>
					</a>
				</li>
				<li><?php
			             echo Html::beginForm(['/site/logout'], 'post')
			                . Html::submitButton(
			                  '<i class="fa fa-sign-out-alt fa-lg mr-4"></i>'.Yii::t('app','Logout').' (' . Yii::$app->user->identity->first_name . ')',
			                  ['class' => 'btn btn-link logout']
			                  )
			                  . Html::endForm();
					?>
			     </li>
			</ul>
		</div>
	<!-- Menu navigation end -->
	</nav>
</div>
