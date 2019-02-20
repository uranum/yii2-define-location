<?php

use uranum\location\Module;
use yii\db\Migration;

class m161201_185845_createTableUserIp extends Migration
{
    protected $userTableName;
    
    public function init()
    {
        parent::init();
        $instance = Module::getInstance();
        /** @var Module $module */
        $module = Yii::$app->getModule($instance->id);
        $this->userTableName = $module->userTableName;
    }

    public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable(Module::TABLE_NAME, [
			'id'       => $this->primaryKey(),
			'user_id'  => $this->integer(11)->unique(), // добавьте ->unsigned(), если у вашей таблицы user PR имеет атрибут unsigned
			'ip'       => $this->bigInteger()->comment('Реальный ip'),
			'location' => $this->string()->comment('Населенный пункт'),
		], $tableOptions);

		$this->createIndex('idx-ip', Module::TABLE_NAME, 'ip');
		$this->createIndex('idx-location', Module::TABLE_NAME, 'location');
		$this->addForeignKey('fk-user_ip-user', Module::TABLE_NAME, 'user_id', $this->userTableName, 'id', 'CASCADE');
	}

	public function down()
	{
		$this->dropForeignKey('fk-user_ip-user', Module::TABLE_NAME);
		$this->dropTable(Module::TABLE_NAME);
	}
}
