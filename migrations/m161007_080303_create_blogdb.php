<?php

use yii\db\Migration;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;

/**
 * First migration for Sheepblog
 * execute with:
 * > yii migrate
 */
class m161007_080303_create_blogdb extends Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    /**
     * @return bool
     */
    protected function isMSSQL()
    {
        return $this->db->driverName === 'mssql' || $this->db->driverName === 'sqlsrv' || $this->db->driverName === 'dblib';
    }
    
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->db = Yii::$app->db;
        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'auto_increment=1 character set utf8 collate utf8_unicode_ci engine=InnoDB';
        }
        
        ////////////////////////////////////////////////
        // Data tables
        ////////////////////////////////////////////////
        
        // Table tbl_user
        
        $this->createTable('tbl_user', [
            'user_id' => $this->primaryKey(),
            'username' => $this->string(30)->notNull()->unique(),
            'email' => $this->string(40)->notNull(),
            'password' => $this->string(80)->notNull(),
            'authkey' => $this->string(50)->notNull(),
        ], $tableOptions);
        
        // Data insertion in tbl_user
        
        $this->insert('tbl_user', ['username' => 'nimmos', 'email' => 'nisanvera23@gmail.com', 'password' => '$2y$13$RU1b5BEx4Hfd/e0UMjXWKe9EQJmpSaB.4EInLu5yb2DE/Xvr51QU2', 'authkey' => 'JpAyjgRoz-HTJmCHxlLaw83otUrWTYM-']);
        $this->insert('tbl_user', ['username' => 'lucchi', 'email' => 'mitunacaptor@gmail.com', 'password' => '$2y$13$T7V2GoAyAnoCfoIORDLBt.ndajd5jv46xuyLU6ZTeCYMIUMIi8zba', 'authkey' => 'LcBq-bsA-GQ35qPVErGL3TNETVeHtrqO']);
        
        // Table tbl_post
        
        $this->createTable('tbl_post', [
            'post_id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'time' => 'timestamp default current_timestamp',
            'title' => $this->string(160)->notNull(),
            'content' => $this->text()->notNull(),
            'headerimage' => $this->string(11)->defaultValue(null),
        ], $tableOptions);
        $this->createIndex('post_user_idx', 'tbl_post', 'user_id');
        $this->addForeignKey('post_user_fk', 'tbl_post', 'user_id', 'tbl_user', 'user_id', 'CASCADE', 'CASCADE');
        
        // Table tbl_comment
                
        $this->createTable('tbl_comment', [
            'comment_id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'post_id' => $this->integer()->notNull(),
            'time' => 'timestamp default current_timestamp on update current_timestamp',
            'content' => $this->text()->notNull(),
        ], $tableOptions);
        $this->createIndex('comm_user_idx', 'tbl_comment', 'user_id');
        $this->addForeignKey('comm_user_fk', 'tbl_comment', 'user_id', 'tbl_user', 'user_id', 'CASCADE', 'CASCADE');
        $this->createIndex('comm_post_idx', 'tbl_comment', 'post_id');
        $this->addForeignKey('comm_post_fk', 'tbl_comment', 'post_id', 'tbl_post', 'post_id', 'CASCADE', 'CASCADE');
        
        ////////////////////////////////////////////////
        // RBAC Authentication tables
        ////////////////////////////////////////////////
        
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;
        
        // Table tbl_auth_rule
        
        $this->createTable($authManager->ruleTable, [
            'name' => $this->string(64)->notNull(),
            'data' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
        ], $tableOptions);
        
        // Data insertion in auth_rule
        
        $this->insert($authManager->ruleTable, [
            'name' => 'isAuthor',
            'data' => 'O:19:"app\rbac\AuthorRule":3:{s:4:"name";s:8:"isAuthor";s:9:"createdAt";i:1475077228;s:9:"updatedAt";i:1475077228;}',
            'created_at' => '1475077228',
            'updated_at' => '1475077228',
        ]);
        
        // Table tbl_auth_item
        
        $this->createTable($authManager->itemTable, [
            'name' => $this->string(64)->notNull(),
            'type' => $this->integer()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY (name)',
            'FOREIGN KEY (rule_name) REFERENCES ' . $authManager->ruleTable . ' (name)'.
                ($this->isMSSQL() ? '' : ' ON DELETE SET NULL ON UPDATE CASCADE'),
        ], $tableOptions);
        $this->createIndex('idx-auth_item-type', $authManager->itemTable, 'type');

        // Data insertion in auth_item
        
        $this->insert($authManager->itemTable, ['name' => 'admin', 'type' => '1', 'description' => 'Administrator', 'rule_name' => null, 'data' => null, 'created_at' => '1475077228', 'updated_at' => '1475077228']);
        $this->insert($authManager->itemTable, ['name' => 'author', 'type' => '1', 'description' => 'Post author', 'rule_name' => null, 'data' => null, 'created_at' => '1475077228', 'updated_at' => '1475077228']);
        $this->insert($authManager->itemTable, ['name' => 'comment', 'type' => '2', 'description' => 'Comment on a post', 'rule_name' => null, 'data' => null, 'created_at' => '1475077228', 'updated_at' => '1475077228']);
        $this->insert($authManager->itemTable, ['name' => 'createPost', 'type' => '2', 'description' => 'Create a post', 'rule_name' => null, 'data' => null, 'created_at' => '1475077228', 'updated_at' => '1475077228']);
        $this->insert($authManager->itemTable, ['name' => 'deleteComment', 'type' => '2', 'description' => 'Delete all comments', 'rule_name' => null, 'data' => null, 'created_at' => '1475085633', 'updated_at' => '1475085633']);
        $this->insert($authManager->itemTable, ['name' => 'deleteOwnComment', 'type' => '2', 'description' => 'Delete own comment', 'rule_name' => 'isAuthor', 'data' => null, 'created_at' => '1475085633', 'updated_at' => '1475085633']);
        $this->insert($authManager->itemTable, ['name' => 'deleteOwnPost', 'type' => '2', 'description' => 'Update own post', 'rule_name' => 'isAuthor', 'data' => null, 'created_at' => '1475085633', 'updated_at' => '1475085633']);
        $this->insert($authManager->itemTable, ['name' => 'deletePost', 'type' => '2', 'description' => 'Delete all posts', 'rule_name' => null, 'data' => null, 'created_at' => '1475085633', 'updated_at' => '1475085633']);
        $this->insert($authManager->itemTable, ['name' => 'updateOwnPost', 'type' => '2', 'description' => 'Update own post', 'rule_name' => 'isAuthor', 'data' => null, 'created_at' => '1475077228', 'updated_at' => '1475077228']);
        $this->insert($authManager->itemTable, ['name' => 'updatePost', 'type' => '2', 'description' => 'Update all posts', 'rule_name' => null, 'data' => null, 'created_at' => '1475077228', 'updated_at' => '1475077228']);
        
        // Table tbl_auth_item_child
        
        $this->createTable($authManager->itemChildTable, [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY (parent, child)',
            'FOREIGN KEY (parent) REFERENCES ' . $authManager->itemTable . ' (name)'.
                ($this->isMSSQL() ? '' : ' ON DELETE CASCADE ON UPDATE CASCADE'),
            'FOREIGN KEY (child) REFERENCES ' . $authManager->itemTable . ' (name)'.
                ($this->isMSSQL() ? '' : ' ON DELETE CASCADE ON UPDATE CASCADE'),
        ], $tableOptions);
        
        // Data insertion in auth_item_child
        
        $this->insert($authManager->itemChildTable, ['parent' => 'admin', 'child' => 'author']);
        $this->insert($authManager->itemChildTable, ['parent' => 'author', 'child' => 'comment']);
        $this->insert($authManager->itemChildTable, ['parent' => 'author', 'child' => 'createPost']);
        $this->insert($authManager->itemChildTable, ['parent' => 'admin', 'child' => 'deleteComment']);
        $this->insert($authManager->itemChildTable, ['parent' => 'author', 'child' => 'deleteOwnComment']);
        $this->insert($authManager->itemChildTable, ['parent' => 'author', 'child' => 'deleteOwnPost']);
        $this->insert($authManager->itemChildTable, ['parent' => 'admin', 'child' => 'deletePost']);
        $this->insert($authManager->itemChildTable, ['parent' => 'author', 'child' => 'updateOwnPost']);
        $this->insert($authManager->itemChildTable, ['parent' => 'admin', 'child' => 'updatePost']);
        
        // Table tbl_auth_assignment
        
        $this->createTable($authManager->assignmentTable, [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY (item_name, user_id)',
            'FOREIGN KEY (item_name) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        
        // Data insertion in auth_assignment
        
        $this->insert($authManager->assignmentTable, ['item_name' => 'admin', 'user_id' => '1', 'created_at' => '1475077228']);
        $this->insert($authManager->assignmentTable, ['item_name' => 'author', 'user_id' => '2', 'created_at' => '1475850401']);

        ////////////////////////////////////////////////
        // RBAC Authentication trigger
        ////////////////////////////////////////////////
        
        if ($this->isMSSQL()) {
            $this->execute("CREATE TRIGGER dbo.trigger_auth_item_child
            ON dbo.{$authManager->itemTable}
            INSTEAD OF DELETE, UPDATE
            AS
            DECLARE @old_name VARCHAR (64) = (SELECT name FROM deleted)
            DECLARE @new_name VARCHAR (64) = (SELECT name FROM inserted)
            BEGIN
            IF COLUMNS_UPDATED() > 0
                BEGIN
                    IF @old_name <> @new_name
                    BEGIN
                        ALTER TABLE auth_item_child NOCHECK CONSTRAINT FK__auth_item__child;
                        UPDATE auth_item_child SET child = @new_name WHERE child = @old_name;
                    END
                UPDATE auth_item
                SET name = (SELECT name FROM inserted),
                type = (SELECT type FROM inserted),
                description = (SELECT description FROM inserted),
                rule_name = (SELECT rule_name FROM inserted),
                data = (SELECT data FROM inserted),
                created_at = (SELECT created_at FROM inserted),
                updated_at = (SELECT updated_at FROM inserted)
                WHERE name IN (SELECT name FROM deleted)
                IF @old_name <> @new_name
                    BEGIN
                        ALTER TABLE auth_item_child CHECK CONSTRAINT FK__auth_item__child;
                    END
                END
                ELSE
                    BEGIN
                        DELETE FROM dbo.{$authManager->itemChildTable} WHERE parent IN (SELECT name FROM deleted) OR child IN (SELECT name FROM deleted);
                        DELETE FROM dbo.{$authManager->itemTable} WHERE name IN (SELECT name FROM deleted);
                    END
            END;");
        }
    }

    public function safeDown()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        ////////////////////////////////////////////////
        // DROPPING RBAC Authentication trigger
        ////////////////////////////////////////////////
        
        if ($this->isMSSQL()) {
            $this->execute('DROP TRIGGER dbo.trigger_auth_item_child;');
        }
        
        ////////////////////////////////////////////////
        // DROPPING RBAC Authentication tables
        ////////////////////////////////////////////////

        $this->dropTable($authManager->assignmentTable);
        $this->dropTable($authManager->itemChildTable);
        $this->dropTable($authManager->itemTable);
        $this->dropTable($authManager->ruleTable);
        
        ////////////////////////////////////////////////
        // DROPPING Data tables
        ////////////////////////////////////////////////
        
        $this->db = Yii::$app->db;
        
        $this->dropForeignKey('comm_post_fk', 'tbl_comment', 'post_id', 'tbl_post', 'post_id', 'CASCADE', 'CASCADE');
        $this->dropIndex('comm_post_idx', 'tbl_comment', 'post_id');
        $this->dropForeignKey('comm_user_fk', 'tbl_comment', 'user_id', 'tbl_user', 'user_id', 'CASCADE', 'CASCADE');
        $this->dropIndex('comm_user_idx', 'tbl_comment', 'user_id');
        
        $this->dropForeignKey('post_user_fk', 'tbl_post', 'user_id', 'tbl_user', 'user_id', 'CASCADE', 'CASCADE');
        $this->dropIndex('post_user_idx', 'tbl_post', 'user_id');
        
        $this->dropTable('tbl_comment');
        $this->dropTable('tbl_post');
        $this->dropTable('tbl_user');
    }
}
