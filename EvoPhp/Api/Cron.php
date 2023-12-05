<?php

namespace EvoPhp\Api;

use EvoPhp\Database\Query;

class Cron
{
    public $query;

    public function __construct()
    {
        $this->query = new Query;
    }

    public static function createTable() {
        $self = new self;

        $statement = "CREATE TABLE IF NOT EXISTS crontabs (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            callback TEXT NOT NULL,
            args JSON NOT NULL,
            exact_last_runtime BIGINT DEFAULT 0 NOT NULL,
            next_runtime BIGINT NOT NULL,
            expression TEXT NOT NULL,
            status VARCHAR(20) DEFAULT 'active' NOT NULL
            )";
        $self->query->query($statement)->execute();

        $statement = "CREATE TABLE IF NOT EXISTS cron_logs (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            crontab_id BIGINT NOT NULL,
            initiator BIGINT NOT NULL,
            exact_runtime BIGINT NOT NULL,
            status VARCHAR(20) NOT NULL,
            feedback TEXT NOT NULL
            )";
        $self->query->query($statement)->execute();
    }

    public static function test($expression) {
		$cron = new \Cron\CronExpression($expression);
		$cron->isDue();
		echo 'Next run: '.$cron->getNextRunDate()->format('Y-m-d H:i:s').'<br/>';
		echo 'Previous run: '.$cron->getPreviousRunDate()->format('Y-m-d H:i:s');
	}

    public function schedule($expression, $callback, ...$args) {
		if (!function_exists($callback)) {
			throw new \Exception("You have not created a function \"".$callback."\" to handle this schedule", 1);
		}
		$cron = new \Cron\CronExpression($expression);
		$next_runtime = $cron->getNextRunDate()->getTimestamp();
		return $this->query->insert('crontabs', "ssis",
					[
                        'callback' => $callback, 
                        'args' => json_encode($args), 
                        'next_runtime' => $next_runtime, 
                        'expression' => $expression
                    ])->execute();
	}
}


?>