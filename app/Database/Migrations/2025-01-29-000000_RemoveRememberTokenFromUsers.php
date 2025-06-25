<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveRememberTokenFromUsers extends Migration
{
    public function up()
    {
        // Supprimer les colonnes remember_token et remember_token_expires
        $this->forge->dropColumn('users', ['remember_token', 'remember_token_expires']);
    }

    public function down()
    {
        // Ajouter les colonnes remember_token et remember_token_expires si besoin de rollback
        $fields = [
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
        ];
        
        $this->forge->addColumn('users', $fields);
    }
} 