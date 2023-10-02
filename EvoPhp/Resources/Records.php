<?php 

namespace EvoPhp\Resources;

use EvoPhp\Database\Query;
use EvoPhp\Database\Session;
use EvoPhp\Api\Operations;
use EvoPhp\Database\DataType;

/**
 * summary
 */
class Records
{
    use DataType;
    
    /**
     * summary
     */

    public $error = "";

    public $errorCode = 200;

    public $query;

    public $session;

    public function __construct()
    {
        $this->query = new Query;
        $this->session = Session::getInstance();
    }

    public function __destruct() {
        // $this->execute();
    }

    public static function createTable() {
        $self = new self;
        if($self->query->checkTableExist("records")) {
            $self->maintainTable();
            return;
        }

        $statement = "CREATE TABLE IF NOT EXISTS records (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                record_name VARCHAR(100) NOT NULL,
                record_value TEXT NOT NULL,
                data_type VARCHAR(6) DEFAULT 'string',
                record_int BIGINT NOT NULL DEFAULT 0,
                record_double DOUBLE(20,4) NOT NULL DEFAULT 0,
                record_blob BLOB NOT NULL
                )";
        $self->query->query($statement)->execute();
    }

    private function maintainTable() {
        $statement = "ALTER TABLE records ADD 
                        (
                            data_type VARCHAR(6) DEFAULT 'string',
                            record_int BIGINT NOT NULL,
                            record_double DOUBLE(20,4) NOT NULL,
                            record_blob BLOB NOT NULL
                        )";
        $this->query->query($statement)->execute();
    }

    public static function get($record_name, $cache = true, $expiry = 300) {
        $instance = new self;
        return $instance->getRecord($record_name, $cache, $expiry);
    }

    public function getRecord($record_name, $cache = true, $expiry = 300) {
        if(is_array($record_name)) {
            return array_combine($record_name, array_map(fn($v) => (new \EvoPhp\Resources\Options)->getOption($v), $record_name));
        }
        if($cache && isset($this->session->{"records_".$record_name})) {
            if($this->session->{"recordTS_".$record_name} + $expiry > time()) {
                return $this->session->{"records_".$record_name};
            }
        }
        $res = $this->query->select("records")->where("record_name", $record_name)->execute()->rows();
        if(Operations::count($res)) {
            $v = $res[0];
            switch($v->data_type) {
                case "int":
                case "intege":
                case "integer":
                case "number":
                    $record_value = $v->record_int;
                    break;

                case "boolean":
                case "boolea":
                    $record_value = ($v->record_int === 0) ? false : true;
                    break;

                case "double":
                case "float":
                    $record_value = $v->record_double;
                    break;

                case "blob":
                    $record_value = $v->record_blob;
                    break;

                case "array":
                    $record_value = Operations::unserialize($v->record_value);
                    break;

                case "object":
                    $record_value = (object) Operations::unserialize($v->record_value);
                    break;

                default:
                    if(Operations::is_serialized($v->record_value)) {
                        $record_value = Operations::unserialize($v->record_value);
                    } else {
                        $record_value = htmlspecialchars_decode($v->record_value);
                        $record_value = Operations::removeslashes($record_value);
                    }
                    break;
            }
            if($cache) {
                $this->session->{"records_".$record_name} = $record_value;
                $this->session->{"recordTS_".$record_name} = time();
            }
            return $record_value;
        } else return NULL; // record not found;
        
    }

    public static function update($record_name, $record_value) {
        $instance = new self;
        return $instance->updateRecord($record_name, $record_value);
    }

    public function updateRecord($record_name, $record_value) {
        $record_name = strtolower($record_name);
        Operations::doAction("before_update_".$record_name, $record_value);
        $existing_record = $this->getRecord($record_name);
        if($existing_record !== NULL) {
            //update record
            if(isset($this->session->{"records_".$record_name})) {
                $this->session->{"records_".$record_name} = $record_value;
                $this->session->{"recordTS_".$record_name} = time();
            }
            $ev = $this->evaluateData($record_value);
            $this->query->update('records')->set("record_value", (string) $ev->value)->set("data_type", $ev->realType);
            if($ev->field) {
                $this->query->set("record_".$ev->field, $ev->value, $ev->valueType);
            }
            return $this->query->where("record_name", $record_name)->execute();
        }
        //add_record
        return $this->addrecord($record_name, $record_value);
    }

    public function addrecord($record_name, $record_value) {
        $record_name = strtolower($record_name);
        Operations::doAction("before_add_".$record_name, $record_value);
        $ev = $this->evaluateData($record_value);
        $args = [
            "record_name" => (string) $record_name,
            "record_value" => (string) $ev->value, 
            "data_type" => $ev->realType
        ];
        $types = "sss";
        if($ev->field) {
            $args["record_".$ev->field] = $ev->value;
            $types .= $ev->valueType;
        }
        if($ev->field !== 'blob') {
            $args['record_blob'] = "";
            $types .= "s";
        }
        $this->query->insert("records", $types, $args)->execute();
    }

}