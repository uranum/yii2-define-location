<?php

namespace frontend\modules\location\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%userIp}}".
 * @property integer      $id
 * @property integer      $user_id
 * @property integer      $ip
 * @property string       $location
 * @property ActiveRecord $user
 */
class UserIp extends ActiveRecord
{
	private $_userClass;
	
	public function init()
	{
		parent::init();
		$this->_userClass = Yii::$app->getModule('location')->userModelClass;
	}
	
	public function behaviors()
	{
		return [
			'attributeIp' => [
			    'class' => AttributeBehavior::className(),
			    'attributes' => [
			    	ActiveRecord::EVENT_BEFORE_VALIDATE => 'ip'
			    ],
			    'value' => ip2long(Yii::$app->request->userIP)
			],
			'attributeUserId' => [
			    'class' => AttributeBehavior::className(),
			    'attributes' => [
			    	ActiveRecord::EVENT_BEFORE_VALIDATE => 'user_id'
			    ],
			    'value' => Yii::$app->user->id
			]
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%userIp}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'ip'], 'integer'],
			[['location'], 'required'],
			[['user_id'], 'unique'],
			[['location'], 'string', 'max' => 255],
			['location', 'match', 'pattern' => '/^[а-яё0-9 \-]+$/isu'],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => $this->_userClass, 'targetAttribute' => ['user_id' => 'id']],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'       => 'ID',
			'user_id'  => 'User ID',
			'ip'       => 'Реальный ip',
			'location' => 'Населенный пункт',
		];
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne($this->_userClass, ['id' => 'user_id']);
	}
}
