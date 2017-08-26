<?php

use yii\db\Migration;

class m161201_185845_createTableUserIp extends Migration
{
	public function up()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}
		
		$this->createTable('{{%userIp}}', [
			'id'       => $this->primaryKey(),
			'user_id'  => $this->integer(11)->unsigned()->unique(),
			'ip'       => $this->bigInteger()->comment('Реальный ip'),
			'location' => $this->string()->comment('Населенный пункт'),
		], $tableOptions);
		
		$this->createIndex('idx-ip', '{{%userIp}}', 'ip');
		$this->createIndex('idx-location', '{{%userIp}}', 'location');
		$this->addForeignKey('fk-user_ip-user', '{{%userIp}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
	}
	
	public function down()
	{
		$this->dropForeignKey('fk-user_ip-user', '{{%userIp}}');
		$this->dropTable('{{%userIp}}');
	}
}
