<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

use yii\helpers\Url;
use yii\helpers\Html;
use app\components\Settings;
use app\components\WebApp;


$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->activation_code]);
?>
<table style="font-family:Roboto; border-spacing:0px;padding: 20px; background-color: #F8F9FA; border-collapse:separate;" summary="o_mail_notification" width="100%" cellpadding="0" border="0" bgcolor="#F8F9FA">
	<!-- HEADER -->
	<tr>
		<td style="min-width: 590px;" align="center">
			<table style="border-spacing:0px;width:590px;background:inherit;color:inherit" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td style="padding:10px 10px 10px 0px;font-size: 14px" width="200" valign="center">
						<img src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/css/images/logo.png" style="padding: 20px; margin: 0px; height: auto; max-width:200px;" alt="<?= Yii::$app->name; ?>" data-original-title="" title="">
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
	<!-- CONTENT -->
	<tr>
		<td style="min-width: 590px;" align="center">
			<table style="border-spacing:0px;min-width: 590px; background-color: rgb(255, 255, 255); padding: 20px; border-collapse:separate; border-width:1px; border-style:solid; border-color:#e8eaed;" width="590" cellpadding="0" bgcolor="#ffffff">
			<tbody>
				<tr>
					<td style="font-family:Roboto,Tahoma,Verdana,Segoe,sans-serif; color: #555; font-size: 14px;" valign="top">
						<div>
							<p style="font-size:34px;color:rgb(57,150,220)"><?php echo Yii::t('app','Reset password request');?></p>
						</div>
						<div>
							<p style="margin-top: 28px;font-size: 14px;"><?php echo Yii::t('app','Hi');?>, <strong><?php echo ($user->first_name <> 0 ? $user->first_name : $user->email); ?></strong>.</p>
						</div>
						<div>
							<p style="margin-top: 28px;font-size: 14px;"><?php echo Yii::t('app','Follow the link below to reset your password.');?>
				          	<br>
				          	<b><?php echo Yii::t('app','If you did not make this request please ignore this email and do NOT follow the link below');?></b>.</p>
						</div>
						<div>
							<a href="<?php echo $resetLink;?>" data-method="POST">
                            	<button type="button" style="padding: 3px 3px 3px 3px;
                    				outline: none;
                              		cursor: pointer;
                    				background-color: blue;
									color: white;
                    				border: none;
                    				border-radius: 5px;
                    				box-shadow: 0 3px #555;
                    				min-width: 100px;
									min-height: 30px;
                    				text-shadow: 1px 1px 2px black;">
									<?php echo Yii::t('app','Reset password');?>
								</button>

							</a>
						</div>
						<div>
							<p style="margin-top: 28px;font-size: 14px;">Cheers,
							<br><strong><?php echo Yii::$app->params['adminName']; ?></strong></p>
						</div>
					</td>
				</tr>
			</tbody>
			</table>
		</td>
	</tr>
	<!-- FOOTER -->
	<?php
	 ?>
	<tr>
		<td style="min-width: 590px;" align="center">
			<table style="border-spacing:0px;min-width: 590px; background-color: rgb(248,249,250); padding: 20px; border-collapse:separate;" width="590" cellpadding="0" border="0" bgcolor="#F8F9FA">
				<tbody>
					<tr>
						<td style="color: #6c737f; padding-top: 10px; padding-bottom: 10px;" valign="middle" align="left">
							<div>
								<p style="font-size: 14px;">
									<strong><?php echo Yii::$app->name; ?></strong>
									<br>Tel. +39 081 19463570
									<br><?php echo Yii::$app->params['adminEmail'] .' | '. Yii::$app->params['website']; ?>
								</p>
							</div>
							<div>
								<p style="font-size: 10px;"><?php echo Yii::t('app','You receive this email because you have registered on our site and / or you have used our services and you have given consent to receive email communications from us.');?>
								</p>
							</div>
							<div>
								<p style="font-size: 10px;">---<br><strong><?php echo Yii::t('app','Confidentiality and security of the message');?></strong><br><?php echo Yii::t('app','The content of the e-mail is reserved and is addressed exclusively to the identified recipient (s). Therefore it is forbidden to read it, copy it, disclose it or use it by anyone except the recipient (s). If you are not the recipient, we invite you to delete the message and any attachments by immediately sending us written communication by e-mail. Although the sender undertakes to take the most appropriate measures to ensure the absence of viruses within any attachments to this e-mail communication, such measures do not constitute an absolute guarantee and therefore we invite you to put in place your antivirus checks before opening any attachment. The sender therefore assumes no responsibility for any damage that you may suffer due to viruses contained in the messages.'); ?>
								</p>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
</table>
<?php
  // exit;
?>
