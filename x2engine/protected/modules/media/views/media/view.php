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
	array('label'=>Yii::t('media', 'All Media'), 'url'=>array('index')),
	array('label'=>Yii::t('media', 'Upload'), 'url'=>array('upload')),
	array('label'=>Yii::t('media', 'View')),
	array('label'=>Yii::t('media', 'Update'), 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>Yii::t('media', 'Delete'), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>Yii::t('media','Are you sure you want to delete this item?'))),
));

?>
<div class="page-title icon media"><h2><span class="no-bold"><?php echo Yii::t('media','File: '); ?></span> <?php echo $model->drive?$model->title:$model->fileName; ?></h2></div>
<div id="main-column" class="half-width">
<?php

$parts = explode('.',$model->fileName);			// split filename on '.'

$file = Yii::app()->file->set('uploads/'.$model->fileName);

$file_ext = strtolower($file->getExtension());	// extension is the last part

$legal_extensions = array('jpg','gif','png','bmp','jpeg','jpe');

$fileView = '';

if(file_exists("uploads/media/{$model->uploadedBy}/{$model->fileName}")) {
	$file = Yii::app()->file->set("uploads/media/{$model->uploadedBy}/{$model->fileName}");
	$file_ext = strtolower($file->getExtension());	// extension is the last part
	$fileURL = Yii::app()->request->baseUrl.'/uploads/media/'. $model->uploadedBy . '/'.urlencode($model->fileName);
	if(in_array($file_ext,$legal_extensions))
		$fileView .= CHtml::link(CHtml::image($fileURL,'',array('class'=>'attachment-img', 'style'=>'display: block; margin-left: auto; margin-right: auto; padding: 5px')),$fileURL);

} else if (file_exists("uploads/{$model->fileName}")) {
	$fileURL = Yii::app()->request->baseUrl.'/uploads/'.urlencode($model->fileName);
	if(in_array($file_ext,$legal_extensions))
		$fileView .= CHtml::link(CHtml::image($fileURL,'',array('class'=>'attachment-img', 'style'=>'display: block; margin-left: auto; margin-right: auto; padding: 5px')),$fileURL);
}
?>

		<?php if(!empty($fileView)) { ?>
			<div style="float: left; margin-right: 5px;">
				<div class="formItem" style="line-height: 200px; border: 1px solid #CCC; background: #FAFAFA; display: table-cell; -moz-border-radius: 4px; -o-border-radius: 4px; -webkit-border-radius: 4px; border-radius: 4px;">
					<?php echo $fileView; ?>
				</div>
				<?php echo CHtml::link(Yii::t('media', 'Download File'),array('download','id'=>$model->id),array('class'=>'x2-button', 'style'=>'margin-top: 5px;')); ?>
			</div>
		<?php } ?>

			<div class="x2-layout form-view" style="margin-bottom: 0;">

				<div class="formSection showSection">
					<div class="tableWrapper noTitle">
						<table>
							<tbody>
								<tr class="formSectionRow">
									<td style="width: 300px">
										<div class="formItem leftLabel">
											<label><?php echo Yii::t('media', 'Association Type'); ?></label>
											<div class="formInputBox" style="width: 200px; height: auto;">
												<?php if($model->associationType) { ?>
													<?php echo ($model->associationType == 'bg'? Yii::t('media', 'Background') : ucfirst($model->associationType)); ?>
												<?php } ?>
											</div>
										</div>

									</td>
								</tr>

								<tr class="formSectionRow">
									<td style="width: 300px">
										<div class="formItem leftLabel">
											<label><?php echo Yii::t('media', 'Association Name'); ?></label>
											<div class="formInputBox" style="width: 200px; height: auto;">
												<?php if($model->associationType && $model->associationType != 'bg') { ?>
													<?php
														if(!empty($model->associationId) && is_numeric($model->associationId) && $modelName=X2Model::getModelName($model->associationType)) {
															$linkModel = X2Model::model($modelName)->findByPk($model->associationId);
															if(isset($linkModel)){
                                                                echo CHtml::link($linkModel->name, array('/'.$model->associationType.'/'.$model->associationId));
                                                            }else
																echo '';
														} else {
															echo '';
														}
													?>
												<?php } ?>
											</div>
										</div>

									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div class="formSection showSection">
					<div class="tableWrapper">
						<table>
							<tbody>
								<tr class="formSectionRow">
									<td style="width: 300px">
										<div class="formItem leftLabel">
											<label><?php echo Yii::t('media', 'Private'); ?></label>
											<div class="formInputBox" style="width: 200px; height: auto;">
												<?php echo CHtml::checkbox('private', $model->private, array( 'onclick'=>"return false", 'onkeydown'=>"return false")); ?>
											</div>
										</div>

									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

                <div class="formSection showSection">
					<div class="tableWrapper">
						<table>
							<tbody>
								<tr class="formSectionRow">
									<td style="width: 300px">
										<div class="formItem leftLabel">
											<label><?php echo Yii::t('media', 'Google Drive'); ?></label>
											<div class="formInputBox" style="width: 200px; height: auto;">
												<?php echo CHtml::checkbox('drive', $model->drive, array( 'onclick'=>"return false", 'onkeydown'=>"return false")); ?>
											</div>
										</div>

									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div class="formSection showSection">
					<div class="tableWrapper">
						<table>
							<tbody>
								<tr class="formSectionRow">
									<td style="width: 300px">
										<div class="formItem leftLabel">
                                            <label><?php echo Yii::t('media', 'Description'); ?></label>
											<div class="formInputBox" style="height: auto;">
												<?php echo $model->description; ?>
											</div>
										</div>

									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
<?php
if(!$model->drive && empty($fileView)){
    echo CHtml::link(Yii::t('media', 'Download File'),array('download','id'=>$model->id),array('class'=>'x2-button', 'style'=>'margin-top: 5px; margin-left: 5px; margin-bottom: 10px'));
}elseif(empty($fileView)){
    echo CHtml::link(Yii::t('media', 'View in Google Drive'),"https://drive.google.com/file/d/".$model->fileName,array('class'=>'x2-button', 'style'=>'margin-top: 5px;','target'=>'_blank'));
}?>


</div>
<style>
.half-width {
    clear: none !important;
}
</style>
<div class="history half-width" style="clear: both;">
<?php $this->widget('Publisher',
	array(
		'associationType'=>'media',
		'associationId'=>$model->id,
		'assignedTo'=>Yii::app()->user->getName(),
		'halfWidth'=>true
	)
);
$this->widget('History',array('associationType'=>'media','associationId'=>$model->id));

?>
</div>
