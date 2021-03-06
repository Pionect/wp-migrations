<?php

namespace WP_Migrations\Migrations;


class Repository implements RepositoryInterface
{
    const OPTION_NAME = "wp-migrations-ran";

    private $migrations_ran;

    /**
     * Create the migration repository data store.
     *
     * @return void
     */
    public static function createRepository()
    {
        \add_option(self::OPTION_NAME, []);
    }

    public function getRanObjects()
    {
        if (is_null($this->migrations_ran)) {
            $option_value         = get_option(self::OPTION_NAME);
            $this->migrations_ran = $option_value;
        }

        return $this->migrations_ran;
    }

    /**
     * Get the ran migrations for a given package.
     *
     * @return array
     */
    public function getRan()
    {
        $migrations = $this->getRanObjects();

        $files = [];
        foreach ($migrations as $migration) {
            array_push($files, $migration->file);
        }

        return $files;
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
        $migrations_ran = $this->getRanObjects();
        array_push($migrations_ran, (object)[
            'file'  => $file,
            'batch' => $batch
        ]);
        $this->migrations_ran = $migrations_ran;
        update_option(self::OPTION_NAME, $migrations_ran);
    }

    /**
     * Get the next migration batch number.
     *
     * @return int
     */
    public function getNextBatchNumber()
    {
        $migrations_ran = $this->getRanObjects();
        if (count($migrations_ran) == 0) {
            return 1;
        } else {
            $migration = end($migrations_ran);

            return $migration->batch + 1;
        }
    }
}