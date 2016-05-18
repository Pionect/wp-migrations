<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 18-5-16
 * Time: 23:58
 */

namespace WPMigrations;


class MigrationRepository implements MigrationRepositoryInterface
{
    const OPTION_NAME = "wp-migrations-applied";

    /**
     * Create the migration repository data store.
     *
     * @return void
     */
    public static function createRepository()
    {
        add_option(self::OPTION_NAME, serialize([]));
    }

    /**
     * Get the ran migrations for a given package.
     *
     * @return array
     */
    public function getRan()
    {
        $migrations_ran = get_option(self::OPTION_NAME);
        return unserialize($migrations_ran);
    }

    /**
     * Get the last migration batch.
     *
     * @return array
     */
    public function getLast()
    {
        // TODO: Implement getLast() method.
    }

    /**
     * Log that a migration was run.
     *
     * @param  string $file
     * @param  int    $batch
     * @return void
     */
    public function log($file, $batch)
    {
        // TODO: Implement log() method.
    }

    /**
     * Get the next migration batch number.
     *
     * @return int
     */
    public function getNextBatchNumber()
    {
        // TODO: Implement getNextBatchNumber() method.
    }
}