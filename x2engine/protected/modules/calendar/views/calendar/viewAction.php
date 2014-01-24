<?php
/*****************************************************************************************
 * X2CRM Open Source Edition is a customer relationship management program developed by
 * X2Engine, Inc. Copyright (C) 2011-2014 X2Engine Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY X2ENGINE, X2ENGINE DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact X2Engine, Inc. P.O. Box 66752, Scotts Valley,
 * California 95067, USA. or at email address contact@x2engine.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * X2Engine" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by X2Engine".
 *****************************************************************************************/
 ?>

<?php
$users = User::getNames();
$form=$this->beginWidget('CActiveForm', array(
    'enableAjaxValidation'=>false,
));
?>

<style type="text/css">

.dialog-label {
	font-weight: bold;
	display: block;
}

.cell {
	float: left;
}

.dialog-cell {
	padding: 5px;
}

</style>

<div class="row">
	<div class="cell dialog-cell" style="float: none;">
		<?php echo $model->actionDescription; ?>
	</div>
</div>

<div class="row">
	<div class="cell dialog-cell">
		<?php echo $form->label($model,($isEvent?'startDate':'dueDate'), array('class'=>'dialog-label'));
		echo Formatter::formatDateTime($model->dueDate);	//format date from DATETIME

		if($isEvent) {
			echo $form->label($model, 'endDate', array('class'=>'dialog-label'));
			echo Formatter::formatDateTime($model->completeDate);	//format date from DATETIME
		}

		?>


		<?php echo $form->label($model, 'allDay', array('class'=>'dialog-label')); ?>
		<?php echo $form->checkBox($model, 'allDay', array('onChange'=>'giveSaveButtonFocus();', 'disabled'=>'disabled')); ?>
	</div>

	<div class="cell dialog-cell">
		<?php echo $form->label($model,'priority', array('class'=>'dialog-label')); ?>
		<?php
		$priorityArray = array(
				'1'=>Yii::t('actions','Low'),
				'2'=>Yii::t('actions','Medium'),
				'3'=>Yii::t('actions','High')
			);
		echo isset($priorityArray[$model->priority])?$priorityArray[$model->priority]:""; ?>
		<?php /*
		<?php echo $form->dropDownList($model,'priority',
			array(
				'Low'=>Yii::t('actions','Low'),
				'Medium'=>Yii::t('actions','Medium'),
				'High'=>Yii::t('actions','High')
			),
			array('onChange'=>'giveSaveButtonFocus();')); */
		?>
	</div>
	<div class="cell dialog-cell">
		<?php
		if($model->assignedTo == null && is_numeric($model->calendarId)) { // assigned to calendar instead of user?
		    $model->assignedTo = $model->calendarId;
		}
		?>
		<?php echo $form->label($model,'assignedTo', array('class'=>'dialog-label')); ?>
		<?php
		$assignedToArray = $users;
		echo $assignedToArray[$model->assignedTo];
		?>
</div>

<?php $this->endWidget(); ?>
