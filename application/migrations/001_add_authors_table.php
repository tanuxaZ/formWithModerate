<?php
defined('BASEPATH') OR exit('No direct script access allowed.');

class Migration_Add_authors_table extends CI_Migration
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
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => FALSE,
                'default' => ''
            )
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('authors', TRUE);

        $data = array(
            array(
                'name' => 'Автор 1'
            ),
            array(
                'name' => 'Автор 2'
            ),
            array(
                'name' => 'Автор 3'
            )
        );

        $this->db->insert_batch('authors', $data);
    }

    public function down()
    {
        $this->dbforge->drop_table('authors');
    }
}