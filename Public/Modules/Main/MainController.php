<?php 

namespace Public\Modules\Main;

use EvoPhp\Api\Controllers;
use EvoPhp\Api\Operations;

/**
 * summary
 */
class MainController extends Controllers
{
    /**
     * summary
     */
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = __DIR__.'/Views';
    }

    public function getData($data) {
        if(method_exists($this, $this->dataMethod)) {
            $callback = $this->dataMethod;
            return $this->$callback($data);
        }
        return false;
    }

    public function addResources() {
        if(method_exists($this, $this->resourceMethod)) {
            $callback = $this->resourceMethod;
            return $this->$callback();
        }
        return false;
    }

    public function logoutData($data) {
        $token = parent::signOut();
        return [
            "name" => Operations::getFullname($token[0]->user_id ?? 'dear', "TO")
        ];
    }

    public function Accounts__indexData() {
        return [
            // "registrationLink" => $this->config->Links['insuranceRegistration'],
        ];
    }
    
}

?>