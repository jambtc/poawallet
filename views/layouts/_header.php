<header class="no-background">
	<?php if (Yii::$app->controller->id != 'wallet'): ?>
	<!-- extra class no-background -->
	<a class="go-back-link" href="javascript:history.back();">
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

	<div id='poa-pulse'>
	  <button class="pulse-button" ></button>
	</div>

	<div class="navi-menu-button">
		<em></em>
		<em></em>
		<em></em>
	</div>

</header>
