<?php
/* @var $this UserController */
/* @var $dataProvider CActiveDataProvider */ 

$this->breadcrumbs=array(
	'Users',
);

$this->menu=array(
	array('label'=>'Create User', 'url'=>array('create')),
	//array('label'=>'Manage User', 'url'=>array('admin')),
);

?>

<h1>Users</h1>

<p>Для поиска пользователя введите имя или фамилию (можно производить совместный поиск).


<?php 

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'name',
		'surname',
		array(
			'class'=>'CButtonColumn',
			'template'=>'{entrance}',
			'buttons'=> array(
				'entrance' => array(
            		'label'=>'Войти',
            		'url'=>'Yii::app()->createUrl("user/mail", array("mail"=>$data->mail))'
					//'url'=>'Yii::app()->createUrl("user/mail")',
					//'click'=>'function(){return false;}',
       			),
			),
		),
	),
));
//echo CHtml::encode($data->mail);

/*
$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
));
*/
?>
