<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tbl_tags`.
 */
class m161024_143040_create_tbl_tags_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'auto_increment=1 character set utf8 collate utf8_unicode_ci engine=InnoDB';
        }
        
        $this->createTable('tbl_tags', [
            'tag_id' => $this->primaryKey(),
            'tagname' => $this->string(30)->notNull(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('tbl_tags');
    }
}
