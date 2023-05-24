<?php

// Файлы phpmailer
require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';
require '../phpmailer/Exception.php';

class UserController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','mail'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new User;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	public function actionMail()
	{

		// Переменные, которые отправляет пользователь
		//$name = $_POST['name'];
		$email = $_GET['mail'];
		$text = 'Добро пожаловать!';

		// Формирование самого письма
		$title = "Вход в систему";
		$body = "
		<h2>Важное оповещение!</h2>
		<b>Приветствуем на нашем сайте.</b>
		<br>
		<b>$text<br>
		<b>Ваша почта: $email<br>
		";

		// Настройки PHPMailer
		$mail = new PHPMailer\PHPMailer\PHPMailer();
		try {
			
			$mail->isSMTP();   
			$mail->CharSet = "UTF-8";
			$mail->SMTPAuth   = true;
			//$mail->SMTPDebug = 2;
			$mail->Debugoutput = function($str, $level) {$GLOBALS['status'][] = $str;};

			// Настройки вашей почты
			$mail->Host       = 'smtp.gmail.com'; // SMTP сервера вашей почты
			$mail->Username   = '****'; // Логин на почте
			$mail->Password   = '****'; // Пароль на почте
			$mail->SMTPSecure = 'ssl';
			$mail->Port       = 465;
			$mail->setFrom('*Логин на почте*', 'My_site.com'); // Адрес самой почты и имя отправителя

			// Получатель письма
			$mail->addAddress($email);
			//$mail->addAddress('youremail@gmail.com'); // Ещё один, если нужен
  
			// Отправка сообщения
			$mail->isHTML(true);
			$mail->Subject = $title;
			$mail->Body = $body;    

			// Проверяем отравленность сообщения
			if ($mail->send()) 
			{
				$result = "success";

			} else 
			{
				$result = "error";
			}

		} catch (Exception $e) {
			$result = "error";
			$status = "Сообщение не было отправлено. Причина ошибки: {$mail->ErrorInfo}";
		}

		// Отображение результата
		//echo json_encode(["result" => $result, "status" => $status]);
		
		$this->redirect(['index']);
		
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		
		$model=new User('search');
		$model->unsetAttributes();
		
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];
		
		$this->render('index',array(
			'model'=>$model,
		));
		/*
		$dataProvider=new CActiveDataProvider('User');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
		*/
	}

	/**
	 * Manages all models.
	 */
	/*
	public function actionAdmin()
	{
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	*/

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return User the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=User::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}


	/**
	 * Performs the AJAX validation.
	 * @param User $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
