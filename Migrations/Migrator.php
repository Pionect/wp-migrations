<?php

namespace WP_Migrations\Migrations;

use WP_Migrations\Libraries\Helper;
use WP_Migrations\Migrations\Types\OptionMigration;

class Migrator
{
    /**
     * The migration repository implementation.
     *
     */
    protected $repository;

    /**
     * The migration validator implementation.
     */
    protected $validator;

    /**
     * The notes for the current operation.
     *
     * @var array
     */
    protected $notes = [];

    /**
     * Store the namespace in which the migrations are stored
     * @var string
     */
    protected $migrations_namespace;

    /**
     * Create a new migrator instance.
     *
     * @param  \WP_Migrations\Migrations\Repository  $repository
     * @param  \WP_Migrations\Migrations\Validator   $validator
     */
    public function __construct(Repository $repository,
                                Validator $validator,
                                $migrations_namespace)
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->migrations_namespace = $migrations_namespace;
    }

    /**
     * Run the outstanding migrations at a given path.
     *
     * @param  string  $path
     * @return void
     */
    public function run($path)
    {
        $files = $this->getMigrationFiles($path);

        // Once we grab all of the migration files for the path, we will compare them
        // against the migrations that have already been run then run each of the
        // outstanding migrations.
        $ran = $this->repository->getRan();

        $migrations = array_diff($files, $ran);

        $this->requireFiles($path, $migrations);

        $this->runMigrationList($migrations);
    }

    /**
     * Run an array of migrations.
     *
     * @param  array  $migrations
     * @return void
     */
    public function runMigrationList($migrations)
    {
        // First we will just make sure that there are any migrations to run. 
        if (count($migrations) == 0) {
            return;
        }

        $batch = $this->repository->getNextBatchNumber();

        // Once we have the array of migrations, we will spin through them and run the
        // migrations "up" so the changes are made to the databases. We'll then log
        // that the migration was run so we don't repeat it next time we execute.
        foreach ($migrations as $file) {
            $this->runUp($file, $batch);
        }
    }

    /**
     * Run "up" a migration instance.
     *
     * @param  string  $file
     * @param  int     $batch
     * @return void
     */
    protected function runUp($file, $batch)
    {
        // First we will resolve a "real" instance of the migration class from this
        // migration file name. Once we have the instances we can run the actual
        // command such as "up" or "down", or we can just simulate the action.
        $migration = $this->resolve($file);

        if(method_exists($migration, 'get_validation_rules')) {
            $result = $this->validator->validate($migration);

            if ($result == false) {
                $this->note("<info>Validation failed:</info> $file");

                return;
            }
        }

        if(is_subclass_of($migration,OptionMigration::class)) {
            $migration->run();
        } else {
            $migration->up();
        }

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a migration
        // in the application. A migration repository keeps the migrate order.
        $this->repository->log($file, $batch);

        $this->note("<info>Migrated:</info> $file");
    }
    
    /**
     * Get all of the migration files in a given path.
     *
     * @param  string  $path
     * @return array
     */
    public function getMigrationFiles($path)
    {
        $files = glob($path.'/*_*.php');

        // Once we have the array of files in the directory we will just remove the
        // extension and take the basename of the file which is all we need when
        // finding the migrations that haven't been run against the databases.
        if ($files === false) {
            return [];
        }

        $files = array_map(function ($file) {
            return str_replace('.php', '', basename($file));

        }, $files);

        // Once we have all of the formatted file names we will sort them and since
        // they all start with a timestamp this should give us the migrations in
        // the order they were actually created by the application developers.
        sort($files);

        return $files;
    }

    /**
     * Require in all the migration files in a given path.
     *
     * @param  string  $path
     * @param  array   $files
     * @return void
     */
    public function requireFiles($path, array $files)
    {
        foreach ($files as $file) {
            require_once $path.'/'.$file.'.php';
        }
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param  string  $file
     * @return object
     */
    public function resolve($file)
    {
        $file = implode('_', array_slice(explode('_', $file), 4));

        $class = $this->migrations_namespace.'\\'. Helper::studly($file);

        return new $class;
    }

    /**
     * Raise a note event for the migrator.
     *
     * @param  string  $message
     * @return void
     */
    protected function note($message)
    {
        $this->notes[] = $message;
    }

    /**
     * Get the notes for the last operation.
     *
     * @return array
     */
    public function getNotes()
    {
        return $this->notes;
    }

}