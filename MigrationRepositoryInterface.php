<?php

namespace WP_Migrations;

interface MigrationRepositoryInterface
{
    /**
     * Get the ran migrations for a given package.
     *
     * @return array
     */
    public function getRan();

    /**
     * Get the last migration batch.
     *
     * @return array
     */
    public function getLast();

    /**
     * Log that a migration was run.
     *
     * @param  string  $file
     * @param  int     $batch
     * @return void
     */
    public function log($file, $batch);

    /**
     * Get the next migration batch number.
     *
     * @return int
     */
    public function getNextBatchNumber();

    /**
     * Create the migration repository data store.
     *
     * @return void
     */
    public static function createRepository();
}
