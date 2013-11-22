<?php
/*********************************************************************************
 * Copyright (C) 2011-2013 X2Engine Inc. All Rights Reserved.
 *
 * X2Engine Inc.
 * P.O. Box 66752
 * Scotts Valley, California 95067 USA
 *
 * Company website: http://www.x2engine.com
 * Community and support website: http://www.x2community.com
 *
 * X2Engine Inc. grants you a perpetual, non-exclusive, non-transferable license
 * to install and use this Software for your internal business purposes.
 * You shall not modify, distribute, license or sublicense the Software.
 * Title, ownership, and all intellectual property rights in the Software belong
 * exclusively to X2Engine.
 *
 * THIS SOFTWARE IS PROVIDED "AS IS" AND WITHOUT WARRANTIES OF ANY KIND, EITHER
 * EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, TITLE, AND NON-INFRINGEMENT.
 ********************************************************************************/

$this->actionMenu = $this->formatMenu(array(
	array('label'=>Yii::t('quotes','Quotes List'),'url'=>array('index')),
	array('label'=>Yii::t('quotes','Invoice List'), 'url'=>array('indexInvoice')),
	array('label'=>Yii::t('quotes','Create')),
));

$title = CHtml::tag('h2',array(),Yii::t('quotes','Create Quote'));
echo $quick?$title:CHtml::tag('div',array('class'=>'page-title icon quotes'),$title);
?>

<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'quotes-form',
	'enableAjaxValidation'=>false,
));

if($model->hasLineItemErrors): ?>
<div class="errorSummary">
	<h3><?php echo Yii::t('quotes','Could not save quote due to line item errors:'); ?></h3>
	<ul>
	<?php foreach($model->lineItemErrors as $error): ?>
		<li><?php echo CHtml::encode($error); ?></li>
	<?php endforeach; ?>
	</ul>
</div>
<?php endif;

echo $this->renderPartial('application.components.views._form',
	array(
		'model'=>$model,
		'form'=>$form,
		'users'=>$users,
		'modelName'=>'Quote',
		'isQuickCreate'=>true, // let us create the CActiveForm in this file
		'scenario' => $quick ? 'Inline' : 'Default',
	)
);

echo $this->renderPartial('_lineItems', array(
	'model' => $model,
	'products' => $products,
	'readOnly' => false
		)
);

$templateRec = Yii::app()->db->createCommand()->select('id,name')->from('x2_docs')->where("type='quote'")->queryAll();
$templates = array();
$templates[null] = '(none)';
foreach($templateRec as $tmplRec){
	$templates[$tmplRec['id']] = $tmplRec['name'];
}
if(!$quick){
	echo '<div style="display:inline-block">';
	echo '<strong>'.$form->label($model, 'template').'</strong>&nbsp;';
	echo $form->dropDownList($model, 'template', $templates).'&nbsp;'.CHtml::tag('span', array('class' => 'x2-hint', 'title' => Yii::t('quotes', 'To create a template for quotes and invoices, go to the Docs module and select "{crQu}".', array('{crQu}' => Yii::t('docs', 'Create Quote')))), '[?]');
	echo '</div><br />';
}
echo '	<div class="row buttons" style="padding-left:0">'."\n";
echo CHtml::submitButton(Yii::t('app', 'Create'), array('class' => 'x2-button'.($quick?' highlight':''), 'id' => 'quote-save-button', 'tabindex' => 25))."\n";
echo $quick?CHtml::button(Yii::t('app','Cancel'),array('class'=>'x2-button right','id'=>'quote-cancel-button','tabindex'=>24))."\n":'';
echo "	</div>\n";
echo '<div id="quotes-errors"></div>';
$this->endWidget();

if($quick){
	echo '<br /><br /><hr /><script id="quick-quote-form">'."\n";
	foreach(Yii::app()->clientScript->scripts[CClientScript::POS_READY] as $id => $script) {
		if(strpos($id,'logo')===false)
			echo "$script\n";
	}
	echo "</script>";
}

?>
