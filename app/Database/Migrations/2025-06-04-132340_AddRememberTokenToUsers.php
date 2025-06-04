<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRememberTokenToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'remember_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Token pour la fonctionnalité "Se souvenir de moi"'
            ],
            'remember_token_expires' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Date d\'expiration du token "Se souvenir de moi"'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['remember_token', 'remember_token_expires']);
    }
}
