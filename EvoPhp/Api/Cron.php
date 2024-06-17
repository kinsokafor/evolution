<?php

namespace EvoPhp\Api;

use EvoPhp\Database\Query;
use EvoPhp\Api\Operations;
use EvoPhp\Database\Session;

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

    public static function timestampToExpression(int|null $timestamp = null) {
        $config = new Config;
        $timestamp = $timestamp ?? time();
        $dt = new \DateTime("now", new \DateTimeZone($config->timezone ?? "UTC"));
        $dt->setTimestamp($timestamp);
        return date('i H j n *', $dt->getTimestamp());
    }

    public static function test($expression) {
        $config = new Config;
		$cron = new \Cron\CronExpression($expression);
		$cron->isDue();
		echo 'Next run: '.$cron->getNextRunDate("now", 0, false, $config->timezone ?? "UTC")->format('Y-m-d H:i:s').'<br/>';
		echo 'Previous run: '.$cron->getPreviousRunDate("now", 0, false, "UTC")->format('Y-m-d H:i:s');
	}

    public static function schedule($expression, $callback, ...$args) {
        $config = new Config;
        $self = new self;
        if($self::scheduleExists($expression, $callback, ...$args)) {
            return false;
        }
		$cron = new \Cron\CronExpression($expression);
		$next_runtime = $cron->getNextRunDate("now", 0, false, $config->timezone ?? "UTC")->getTimestamp();
		return $self->query->insert('crontabs', "ssis",
					[
                        'callback' => $callback, 
                        'args' => json_encode($args), 
                        'next_runtime' => $next_runtime, 
                        'expression' => $expression
                    ])->execute();
	}

    public static function scheduleExists($expression, $callback, ...$args) {
        $self = new self;
        $res = $self->query->select('crontabs', 'COUNT(*) AS count')
            ->where('callback', $callback, 's')
            ->where('expression', $expression, 's')
            ->where('args', json_encode($args), 's')
            ->execute()->row();
        return $res->count == 0 ? false : true;
    }

    public static function testCb($args = []) {
        var_dump($args);
    }

    public static function executeDueJobs(int $limit = 20) {
        $config = new Config;
        $self = new self;
        $timestamp = time();
        $dt = new \DateTime("now", new \DateTimeZone($config->timezone ?? "UTC"));
        $dt->setTimestamp($timestamp);
		$jobs = $self->query->select('crontabs')
                    ->where("status", "active")
                    ->where("next_runtime", $dt->getTimestamp(), "i", "<")
                    ->limit($limit)
                    ->execute()->rows();
		foreach ($jobs as $job) {
            $self->query->update('crontabs')
                    ->set('status', 'in queue')
                    ->where('id', $job->id)
                    ->execute();
		}
		foreach ($jobs as $job) {
			$self->execute($job);
		}
        $sql = "DELETE FROM `cron_logs`
					WHERE id NOT IN (
					  SELECT id
					  FROM (
					    SELECT id
					    FROM `cron_logs`
					    ORDER BY id DESC
					    LIMIT 1000
					  ) foo
					)";
		$self->query->query($sql)->execute();
	}

    private function execute($job) {
        $config = new Config;
		ignore_user_abort(true);
		$feedback = "";
		$status = "success";
        $runtime = time();
		$cron = new \Cron\CronExpression($job->expression);
		$next_runtime = $cron->getNextRunDate($config->timezone ?? "UTC")->getTimestamp();
		$this->query->update('crontabs')
                    ->set('status', 'running')
                    ->where('id', $job->id)->execute();
        $test = explode("::", $job->callback);
        $args = json_decode($job->args);
        $upd = $this->query->update('crontabs')
                    ->set("next_runtime", $next_runtime)
                    ->set("exact_last_runtime", $runtime);
        try {
            if(Operations::count($test) > 1) {
                list($class, $method) = $test;
                if(method_exists($class, $method)) {
                    call_user_func(array($class, $method), $job->id, $args);
                } else {
                    $status = "failed";
                    $feedback = "Callback function \"".$job->callback."\" does not exist";
                }
            } else {
                if(function_exists($job->callback)) {
                    call_user_func($job->callback, $job->id, $args);
                } else {
                    $status = "failed";
                    $feedback = "Callback function \"".$job->callback."\" does not exist";
                }
            }
        } catch (\Exception $feedback) {
            $status = "failed";
        }
		if(!$this->isCancelled($job->id)) {
			$upd->set("status", "active");
		}
        $upd->where('id', $job->id)->execute();
        $session = Session::getInstance();
        $this->query->insert('cron_logs', 'iiiss', [
            'crontab_id' => (int) $job->id, 
            'initiator' => $session->getResourceOwner() ? (int) $session->getResourceOwner()->user_id : 0, 
            'exact_runtime' => $runtime, 
            'status' => $status, 
            'feedback' => $feedback])->execute();
	}

    public function isCancelled($id) {
		$r = $this->query->select('crontabs', 'COUNT(id) AS cancelled')
            ->where('id', $id)->where('status', 'cancelled')->execute()->row();
        return $r->cancelled > 0 ? true : false;
	}

    public static function cancel($id) {
        $self = new self;
		$self->query->update('crontabs')
                    ->set('status', 'cancelled')
                    ->where('id', (int) $id)->execute();
	}
}
?>