<?php
defined('BASEPATH') OR exit('No direct script access allowed.');

class Migration_Add_session_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => FALSE,
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => FALSE,
            ),
            'user_agent' => array(
                'type' => 'VARCHAR',
                'constraint' => '120',
                'null' => TRUE,
            ),
            'timestamp' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
            ),
            'data' => array(
                'type' => 'BLOB',
            )
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('timestamp');
        $this->dbforge->create_table('ci_sessions', TRUE);
    }

    public function down()
    {
        $this->dbforge->drop_table('ci_sessions');
    }
}