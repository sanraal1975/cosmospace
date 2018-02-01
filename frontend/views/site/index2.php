<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\grid\GridView;
$this->title = 'My Yii Application';

?>
<div class="site-index">
    <div class='error_message'>
        <?php echo $error_msg; ?>
    </div>
    <div class='message'>
        <?php echo $msg; ?>
    </div>
    <br/><br/>
    <div class="container">
		<?= Html::beginForm((["/"]),"post") ?>
		<b>Phone number</b>
		<?= Html::input('text','phone','', $options=['autofocus' => true,
													'class'=>'form-control',
													'maxlength'=>50, 
													'style'=>'width:200px',
													'required'=>true]) 
		?>
		<br/>
		<b>Last name</b>
		<?= Html::input('text','lastname','', $options=['autofocus' => true,
														'class'=>'form-control',
														'maxlength'=>255, 
														'style'=>'width:500px',
														'required'=>true]) 
		?>
		<br/>
		<?= Html::submitButton("Add entry",$options=['name'=>'add']) ?>
		<?= Html::endForm() ?>
		<hr/>
		<?php 
			if(isset($dataProvider))
			{
		?>
				<?= Html::beginForm((["/"]),"post") ?>
				<?= Html::radioList('radiosearch', 0, ['Phone','Lastname']) ?>
				<?= Html::input('text','inputsearch','', $options=['autofocus' => true,
																'class'=>'form-control',
																'maxlength'=>255, 
																'style'=>'width:500px',
																'required'=>true]) 
				?>
				<br/>
				<?= Html::submitButton("Search entry",$options=['name'=>'search']) ?>
				<?= Html::endForm() ?>
		<?php
				if(isset($dataProviderSearch))
				{
					echo "<br/>";
					echo GridView::widget([
					    'dataProvider' => $dataProviderSearch,
					    'columns' => [
					        ['class' => 'yii\grid\SerialColumn'],
					        [
						        'attribute' => 'phone', 
					    	    'value' => 'phone',
					        ],
					        [
						        'attribute' => 'lastname', 
					    	    'value' => 'lastname',
					        ],
					    ]
					]);
				}
		?>
				<hr/>
				<b>All entries</b>
				<br/><br/>
		<?php

				echo GridView::widget([
				    'dataProvider' => $dataProvider,
				    'columns' => [
				        ['class' => 'yii\grid\SerialColumn'],
				        [
					        'attribute' => 'phone', 
				    	    'value' => 'phone',
				        ],
				        [
					        'attribute' => 'lastname', 
				    	    'value' => 'lastname',
				        ],
				    ]
				]);
			}
		?>
    </div>
</div>
