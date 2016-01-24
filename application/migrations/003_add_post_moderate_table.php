<?php
defined('BASEPATH') OR exit('No direct script access allowed.');

class Migration_Add_post_moderate_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
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
            )
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('posts_moderate', TRUE);
        $this->db->query("ALTER TABLE posts_moderate
                          ADD CONSTRAINT FK__posts_moderate_authors
                          FOREIGN KEY (author_id)
                          REFERENCES authors(id)
                          ON UPDATE CASCADE;");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE posts_moderate
                          DROP FOREIGN KEY FK__posts_moderate_authors;");
        $this->dbforge->drop_table('posts_moderate');
    }
}