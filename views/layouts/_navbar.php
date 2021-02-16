<?php

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;

use yii\helpers\Url;
use yii\helpers\Html;
Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';

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
					<a href="<?= Url::to(['/wallet/index']) ?>"><img src="css/img/content/icons/2.png" alt=""><strong class="special"><?= Yii::t('lang','My Wallet') ?></strong> </a>
				</li>
				<li class="">
					<a href="<?= Url::to(['/send/index']) ?>"><img src="css/img/content/icon1.png" alt=""><strong class="special"><?= Yii::t('lang','Send') ?></strong> </a>
				</li>
				<li class="">
					<a href="<?= Url::to(['/receive/index']) ?>"><img src="css/img/content/icon2.png" alt=""><strong class="special"><?= Yii::t('lang','Receive') ?></strong> </a>
				</li>
				<li class="">
					<a href="<?= Url::to(['/tokens/index']) ?>"><img src="css/img/content/icons/3.png" alt=""><strong class="special"><?= Yii::t('lang','Transactions') ?></strong> </a>
				</li>
				<li class="">
					<a href="<?= Url::to(['users/view','id'=>\webapp::encrypt(Yii::$app->user->identity->id)]); ?>"><img src="css/img/content/icons/5.png" alt=""><strong class="special"><?= Yii::t('lang','Profile') ?></strong> </a>
				</li>
				<li><i class="fa fa-logout"></i><?php
			             echo Html::beginForm(['/site/logout'], 'post')
			                . Html::submitButton(
			                  'Logout (' . Yii::$app->user->identity->first_name . ')',
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
