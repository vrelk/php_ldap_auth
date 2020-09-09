<?php
    class AdUser {
        private $valid;
        private $authError;
        private $authDebug;
        private $username;
        private $firstName;
        private $lastName;
        private $memberOf; //array of AdGroup
        
        //username, first name, last name, groups array
        function __construct($valid, $error, $debug, $user, $fn, $ln, $groups){
            $this->valid = $valid;
            $this->authError = $error;
            $this->username = $user;
            $this->firstName = $fn;
            $this->lastName = $ln;
            $this->memberOf = $groups;
        }
        
        public static function newValid($user, $fn, $ln, $groups){
            $instance = new self(true, NULL, NULL, $user, $fn, $ln, $groups);
            return $instance;
        }
        
        public static function newError($user, $error, $debug){
            $instance = new self(false, $error, $debug, $user, null, null, null);
            return $instance;
        }
        
        function isValid(){
            return $this->valid;
        }
        
        function getError(){
            return $this->authError;
        }
        
        function getDebug(){
            return $this->authDebug;
        }
        
        function getUsername(){
            return $this->username;
        }
        
        function getFirstName(){
            return $this->firstName;
        }
        
        function getLastName(){
            return $this->lastName;
        }
        
        function getGroupsCSV(){
            $tmp = "";
            foreach($this->memberOf as $group){
                $tmp .= $group->getName() . ",";
            }
            return trim($tmp, ",");
        }
    }

    class AdGroup {
        private $valid = false;
        private $groupName;
        private $baseDN;
        
        function __construct(string $DN){
            $re = '/^CN=(\w+),(.*)$/m';
            preg_match_all($re, $DN, $matches, PREG_SET_ORDER, 0);
            if(count($matches) == 1){
                $this->groupName = $matches[0][1];
                $this->baseDN = $matches[0][2];
                $this->valid = true;
            }
        }
        
        function isValid(){
            return $this->valid;
        }
        
        function getName(){
            return $this->groupName;
        }
        
        function getBase(){
            return $this->baseDN;
        }
        
        function getDN(){
            return "CN={$this->groupName},{$this->baseDN}";
        }
    }