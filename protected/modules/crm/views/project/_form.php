
<?php
/**
 * @var $form TbActiveForm
 * @var $this Controller
 * @var $model Project
 */
$form = $this->beginWidget(
    'bootstrap.widgets.TbActiveForm',
    array(
        'id'                   => 'project-form',
        'enableAjaxValidation' => false,
    )
); ?>

<?php echo $form->errorSummary($model); ?>
<?php echo $form->textFieldRow($model, 'name', array('class' => 'span5')); ?>
<?php echo $form->textFieldRow($model, 'name_short', array('class' => 'span5')); ?>
<div class="form-actions">
    <?php $this->widget(
    'bootstrap.widgets.TbButton',
    array(
        'buttonType' => 'submit',
        'type'       => 'primary',
        'label'      => $model->isNewRecord ? Yii::t('CrmModule.project', 'Create') : Yii::t('CrmModule.project', 'Save'),
    )
); ?>
</div>

<?php $this->endWidget(); ?>
