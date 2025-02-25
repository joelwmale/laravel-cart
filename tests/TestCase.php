<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as DB;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected DB $db;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a Laravel container
        $container = Container::getInstance();
        $dispatcher = new Dispatcher;

        // Setup Capsule (Eloquent ORM)
        $this->db = new DB($container);
        $this->db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $this->db->setAsGlobal();
        $this->db->bootEloquent();
        $this->db->getConnection()->setEventDispatcher($dispatcher);

        // Run migrations (optional, for testing models)
        $this->migrateDatabase();
    }

    protected function migrateDatabase()
    {
        $schema = $this->db->schema();

        $schema->create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->text('items');
            $table->text('conditions');
            $table->timestamps();
        });
    }
}
