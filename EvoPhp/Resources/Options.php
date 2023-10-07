<?php 

namespace EvoPhp\Resources;

use EvoPhp\Database\Query;
use EvoPhp\Database\Session;
use EvoPhp\Api\Operations;
use EvoPhp\Database\DataType;

/**
 * summary
 */
class Options
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
        if($self->query->checkTableExist("options")) {
            $self->maintainTable();
            return;
        }

        $statement = "CREATE TABLE IF NOT EXISTS options (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                option_name VARCHAR(100) NOT NULL,
                option_value TEXT NOT NULL,
                data_type VARCHAR(6) DEFAULT 'string',
                option_int BIGINT NOT NULL DEFAULT 0,
                option_double DOUBLE(20,4) NOT NULL DEFAULT 0,
                option_blob BLOB NOT NULL
                )";
        $self->query->query($statement)->execute();
    }

    private function maintainTable() {
        $statement = "ALTER TABLE options ADD 
                        (
                            data_type VARCHAR(6) DEFAULT 'string',
                            option_int BIGINT NOT NULL,
                            option_double DOUBLE(20,4) NOT NULL,
                            option_blob BLOB NOT NULL
                        )";
        $this->query->query($statement)->execute();
    }

    public static function get($option_name, $cache = true, $expiry = 300) {
        $instance = new self;
        return $instance->getOption($option_name, $cache, $expiry);
    }

    public function getOption($option_name, $cache = true, $expiry = 300) {
        if(is_array($option_name)) {
            return array_combine($option_name, array_map(fn($v) => (new \EvoPhp\Resources\Options)->getOption($v), $option_name));
        }
        if($cache && isset($this->session->{"options_".$option_name})) {
            if($this->session->{"optionTS_".$option_name} + $expiry > time()) {
                return $this->session->{"options_".$option_name};
            }
        }
        $res = $this->query->select("options")->where("option_name", $option_name)->execute()->rows();
        if(Operations::count($res)) {
            $v = $res[0];
            switch($v->data_type) {
                case "int":
                case "intege":
                case "integer":
                case "number":
                    $option_value = $v->option_int;
                    break;

                case "boolean":
                case "boolea":
                    $option_value = ($v->option_int === 0) ? false : true;
                    break;

                case "double":
                case "float":
                    $option_value = $v->option_double;
                    break;

                case "blob":
                    $option_value = $v->option_blob;
                    break;

                case "array":
                    $option_value = Operations::unserialize($v->option_value);
                    break;

                case "object":
                    $option_value = (object) Operations::unserialize($v->option_value);
                    break;

                default:
                    if(Operations::is_serialized($v->option_value)) {
                        $option_value = Operations::unserialize($v->option_value);
                    } else {
                        $option_value = htmlspecialchars_decode($v->option_value);
                        $option_value = Operations::removeslashes($option_value);
                    }
                    break;
            }
            if($cache) {
                $this->session->{"options_".$option_name} = $option_value;
                $this->session->{"optionTS_".$option_name} = time();
            }
            return $option_value;
        } else return NULL; // option not found;
        
    }

    public static function update($option_name, $option_value) {
        $instance = new self;
        return $instance->updateOption($option_name, $option_value);
    }

    public function updateOption($option_name, $option_value) {
        $option_name = strtolower($option_name);
        Operations::doAction("before_update_".$option_name, $option_value);
        $existing_option = $this->getOption($option_name);
        if($existing_option != NULL) {
            //update option
            if(isset($this->session->{"options_".$option_name})) {
                $this->session->{"options_".$option_name} = $option_value;
                $this->session->{"optionTS_".$option_name} = time();
            }
            $ev = $this->evaluateData($option_value);
            $this->query->update('options')->set("option_value", (string) $ev->value)->set("data_type", $ev->realType);
            if($ev->field) {
                $this->query->set("option_".$ev->field, $ev->value, $ev->valueType);
            }
            return $this->query->where("option_name", $option_name)->execute();
        }
        //add_option
        return $this->addOption($option_name, $option_value);
    }

    public function addOption($option_name, $option_value) {
        $option_name = strtolower($option_name);
        Operations::doAction("before_add_".$option_name, $option_value);
        $ev = $this->evaluateData($option_value);
        var_dump($ev);
        $args = [
            "option_name" => (string) $option_name,
            "option_value" => (string) $ev->value, 
            "data_type" => $ev->realType
        ];
        $types = "sss";
        if($ev->field) {
            $args["option_".$ev->field] = $ev->value;
            $types .= $ev->valueType;
        }
        if($ev->field !== 'blob') {
            $args['option_blob'] = "";
            $types .= "s";
        }
        $this->query->insert("options", $types, $args)->execute();
    }

}