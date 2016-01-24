<?php
defined('BASEPATH') OR exit('No direct script access allowed.');

class Migration_Add_post_main_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'title' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => FALSE,
                'default' => '',
            ),
            'text' => array(
                'type' => 'TEXT',
                'null' => FALSE,
            ),
            'img' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
                'default' => null,
            ),
            'author_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE,
            ),
            'tags' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'is_moderate' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
            )
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('posts_main', TRUE);
        $this->db->query("ALTER TABLE posts_main
                          ADD CONSTRAINT FK__posts_main_authors
                          FOREIGN KEY (author_id)
                          REFERENCES authors(id)
                          ON UPDATE CASCADE;");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE posts_main
                          DROP FOREIGN KEY FK__posts_main_authors;");
        $this->dbforge->drop_table('posts_main');
    }
}