<header class="no-background">
	<?php if (Yii::$app->controller->id != 'wallet'): ?>
	<!-- extra class no-background -->
	<a class="go-back-link mr-2" href="javascript:history.back();">
		<i class="fa fa-arrow-left"></i>
	</a>
	<?php endif ?>
	<div class="search-button" data-search="open">
		<span style="font-size: 1.5em;">
			<i class="fas fa-bell"></i>
			<span class="notify-quantity" style="display:none;" id="quantity_circle">
				<div id="quantity_notify"></div>
			</span>
		</span>
	</div>
	<div class="search-button">
	  <button class="pulse-button pulse-button-offline"></button>
	</div>
	<div class="page-title">
		<small class="header-message"></small>
	</div>

	<!-- <div class="page-title">
		<div class="row">


		</div>
    </div> -->



	<div class="navi-menu-button">
		<em></em>
		<em></em>
		<em></em>
	</div>

</header>
