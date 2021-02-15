

<aside class="shadow d-lg-block d-md-none d-none d-sm-none">
<?php echo \yii\bootstrap4\Nav::widget([
    'options' => [
        'class' => 'd-flex flex-column nav-pills ',
    ],
    'items' => [
        [
            'label' => Yii::t('lang','Wallet'),
            'url' => ['site/index']
        ],
        [
            'label' => Yii::t('lang','Transactions'),
            'url' => ['transactions/index']
        ],
        [
            'label' => Yii::t('lang','Bug report'),
            'url' => ['site/contact-form']
        ],
        [
            'label' => Yii::t('lang','Settings'),
            'url' => ['settings/index']
        ],
        [
            'label' => Yii::t('lang','Profile'),
            'url' => ['users/view']
        ],
        [
            'label' => Yii::t('lang','Logout'),
            'url' => ['sitre/logout']
        ],
    ]
]);
?>
</aside>
