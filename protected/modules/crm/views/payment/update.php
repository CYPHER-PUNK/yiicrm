<?php

/**
 * @var $this Controller
 * @var $payment Payment
 * @var $paymentMoney PaymentMoney
 */
$projectName = isset($payment->client) ? Project::model()->cache(3600)->findByPk($payment->client->project_id)->name : '';
$this->breadcrumbs = array(
	Yii::t('CrmModule.payment', 'Payments') => array('admin'),
    $projectName => array('admin', 'id' => $payment->projectId),
	$payment->id => array('view', 'id' => $payment->id),
	Yii::t('zii', 'Update'),
);

echo $this->renderPartial('_form', array('payment' => $payment, 'paymentMoney' => $paymentMoney));