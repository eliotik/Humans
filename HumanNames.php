<?php

/**
 * Generating Human readable parameters:<br>
 * Name Surname Last name, Age, Sex, Email, Password
 *
 * @author eliotik
 * @copyright (c) 2012, e3t
 */
class HumanNames
{
    private $namesAmount = 50;
    private $boys_names = array();
    private $girls_names = array();
    private $last_names = array();
    private $min_age = 26;
    private $max_age = 58;
    private $generatedFirstNames = array();
    private $generatedLastNames = array();
    private $generatedHumanNames = array();
    private $generatedHumans = array();
    private $generateNameTries = 0;
    private $generateHumanTries = 0;
    private $quiteWork = false;
    private $count_girls_generated = 0;
    private $count_boys_generated = 0;
    private $emailDomain = 'trash-mail.com';
    private $logMain = false;

    /**
     * loading boys/girls names and last names on construct
     */
    public function __construct()
    {
        $this->fillBoysNames();
        $this->fillGirlsNames();
        $this->fillLastNames();
    }

    /**
     * logging work
     * @param string $value
     */
    public function log($value)
    {
        if ($this->quiteWork == false) {
            echo $value;
        }
    }

    public function eol()
    {
        return chr(13) . chr(10);
    }

    public function setNamesAmount($value)
    {
        $value = intval($value);
        $this->namesAmount = (is_null($value) or ($value == 0) or ($value < 0)) ? 50 : $value;
    }

    /**
     * rendering sex type
     * @return string
     */
    private function getSex()
    {
        //b - boy, g - girl(girls always more than boys)
        $range = array('g', 'g', 'g', 'b', 'b');
        shuffle($range);

        return ($range[rand(0, count($range) - 1)] == 'b') ? 'male' : 'female';
    }

    /**
     * rendering name type
     * @return string
     */
    private function isDoubleName()
    {
        //d - double, s - solo name
        $range = array('d', 's', 's', 's', 'd');
        shuffle($range);

        return ($range[rand(0, count($range) - 1)] == 'd') ? true : false;
    }

    /**
     * rendering age
     * @return integer
     */
    private function getAge()
    {
        echo("Return age!");

        return rand($this->min_age, $this->max_age);
    }

    /**
     * generating humans by divided $amount
     * @param integer $amount amount of humans to generate
     * @param boolean $logMain log only main events
     * @return array array of humans
     */
    public function generateHumans($amount = null, $logMain = false)
    {
        $this->logMain = $logMain;
        $this->setNamesAmount($amount);
        $this->generatedFirstNames = array();
        $this->generatedHumans = array();
        $this->generatedHumanNames = array();

        $this->log($this->eol() . '>>>>> Starting First run...' . $this->eol());
        $this->generateHuman($this->namesAmount);

        $count = count($this->generatedHumans);
        if ($count < $this->namesAmount) {
            $this->generateHumanTries = 0;
            while ($count < $this->namesAmount) {
                $this->log(
                    $this->eol(
                    ) . '>>>>> Starting Additional run. We have generated ' . $count . ' humans...' . $this->eol()
                );
                $this->generateHuman($this->namesAmount - $count);
                $count = count($this->generatedHumans);
                if ($this->generateHumanTries >= 10) {
                    break;
                }
                ++$this->generateHumanTries;
            }
        }
        $this->log(
            $this->eol() . 'Generation done. Returning ' . count($this->generatedHumans) . ' humans...' . $this->eol()
        );
        $this->log('Female: ' . $this->count_girls_generated . $this->eol());
        $this->log('Males: ' . $this->count_boys_generated . $this->eol());

        return $this->generatedHumans;
    }

    /**
     * generate humans for $amount
     * @param integer $amount amount of humans to be generated
     */
    private function generateHuman($amount)
    {
        $added = count($this->generatedHumans);
        for ($i = 0; $i <= $amount; ++$i) {
            $human = array();

            $human['age'] = $this->getAge();
            $human['sex'] = $this->getSex();
            $human['first_name'] = $this->getFirstName($human['sex'] == 'male');

            if (is_null($human['first_name'])) {
                continue;
            }

            $this->generatedFirstNames[] = $human['first_name'];
            $human['first_name'] = $this->beautifyName($human['first_name']);
            $human['last_name'] = $this->getLastName();

            if (is_null($human['last_name'])) {
                continue;
            }

            $human['last_name'] = $this->beautifyName($human['last_name']);

            $fio = $human['first_name'] . ' ' . $human['last_name'];
            if (in_array($fio, $this->generatedHumanNames)) {
                continue;
            }
            $this->generatedHumanNames[] = $fio;

            $human['email'] = $this->getEmail($human);
            $human['login'] = $this->getLogin($human);
            $human['password'] = $this->getPassword($human);

            $this->generatedHumans[] = $human;
            ++$added;
            if ($human['sex'] == 'male') {
                ++$this->count_boys_generated;
            } else {
                ++$this->count_girls_generated;
            }
            if ($this->logMain == false) {
                $this->log('Added new human #' . $added . $this->eol());
            }
        }
    }

    /**
     * generate password
     * @return string
     */
    private function getPassword()
    {
        return '123456';
    }

    /**
     * generate last name for human
     * @return null|string
     */
    private function getLastName()
    {
        if ($this->logMain == false) {
            $this->log('Generating last name name...');
        }
        $double = $this->isDoubleName();
        $count = count($this->last_names) - 1;
        $lastName = $this->last_names[rand(0, $count)];
        $this->generateNameTries = 0;
        if ($double == true) {
            $part = $lastName;
            while ($part == $lastName) {
                $part = $this->last_names[rand(0, $count)];
                if ($this->generateNameTries >= 3) {
                    $this->generateNameTries = 0;
                    break;
                }
                ++$this->generateNameTries;
            }
            $this->generateNameTries = 0;
            $lastName .= '-' . $part;
        }
        if ($this->logMain == false) {
            $this->log(
                '[' . ((in_array($lastName, $this->generatedLastNames)) ? 'UNDEFINED' : $lastName) . ']' . $this->eol()
            );
        }

        return (in_array($lastName, $this->generatedLastNames)) ? null : $lastName;
    }

    /**
     * touch magically name
     * @param string $name
     * @return string
     */
    private function beautifyName($name)
    {
        $names = explode('-', $name);
        for ($i = 0, $len = count($names); $i < $len; ++$i) {
            $names[$i] = ucfirst(strtolower($names[$i]));
        }

        return implode('-', $names);
    }

    /**
     * generate first name by sex type
     * @param boolean $boy sex type
     * @return string
     */
    private function getFirstName($boy = false)
    {
        return ($boy == true) ? $this->getBoyName() : $this->getGirlName();
    }

    /**
     * generating login
     * @param array $human
     * @return string
     */
    private function getLogin($human)
    {
        $login = strtolower(str_replace('-', '.', $human['first_name']));
        $login .= strtolower('.' . str_replace('-', '.', $human['last_name']));

        return $login;
    }

    /**
     * generate email
     * @param array $human
     * @return string
     */
    private function getEmail($human)
    {
        if (!isset($human['login']) or empty($human['login'])) {
            $email = strtolower(str_replace('-', '.', $human['first_name']));
            $email .= strtolower('.' . str_replace('-', '.', $human['last_name']));
        } else {
            $email = $human['login'];
        }

        return $email . '@' . $this->emailDomain;
    }

    /**
     * generate boy name
     * @return string
     */
    private function getBoyName()
    {
        if ($this->logMain == false) {
            $this->log('Generating boy name...');
        }
        $double = $this->isDoubleName();
        $count = count($this->boys_names) - 1;
        $name = $this->boys_names[rand(0, $count)];
        $this->generateNameTries = 0;
        if ($double == true) {
            $part = $name;
            while ($part == $name) {
                $part = $this->boys_names[rand(0, $count)];
                if ($this->generateNameTries >= 3) {
                    $this->generateNameTries = 0;
                    break;
                }
                ++$this->generateNameTries;
            }
            $this->generateNameTries = 0;
            $name .= '-' . $part;
        }
        if ($this->logMain == false) {
            $this->log(
                '[' . ((in_array($name, $this->generatedFirstNames)) ? 'UNDEFINED' : $name) . ']' . $this->eol()
            );
        }

        return (in_array($name, $this->generatedFirstNames)) ? null : $name;
    }

    /**
     * generate girl name
     * @return string
     */
    private function getGirlName()
    {
        if ($this->logMain == false) {
            $this->log('Generating girl name...');
        }
        $double = $this->isDoubleName();
        $count = count($this->girls_names) - 1;
        $name = $this->girls_names[rand(0, $count)];
        $this->generateNameTries = 0;
        if ($double == true) {
            $part = $name;
            while ($part == $name) {
                $part = $this->girls_names[rand(0, $count)];
                if ($this->generateNameTries >= 3) {
                    $this->generateNameTries = 0;
                    break;
                }
                ++$this->generateNameTries;
            }
            $this->generateNameTries = 0;
            $name .= '-' . $part;
        }
        if ($this->logMain == false) {
            $this->log(
                '[' . ((in_array($name, $this->generatedFirstNames)) ? 'UNDEFINED' : $name) . ']' . $this->eol()
            );
        }

        return (in_array($name, $this->generatedFirstNames)) ? null : $name;
    }

    /**
     * load boys names from txt file
     * @throws \Exception
     */
    private function fillBoysNames()
    {
        $this->log('Loading Boys names...');
        $this->boys_names = array();

        $data = file_get_contents('Resources/boys.txt', true);
        if ($data == false) {
            throw new \Exception('Cannot load Boys names.');
        }

        $this->boys_names = explode("\n", $data);

        shuffle($this->boys_names);
        $this->log('[DONE] Loaded: ' . count($this->boys_names) . $this->eol());
    }

    /**
     * load girls names from txt file
     * @throws \Exception
     */
    private function fillGirlsNames()
    {
        $this->log('Loading Girls names...');
        $this->girls_names = array();

        $data = file_get_contents('Resources/girls.txt', true);
        if ($data == false) {
            throw new \Exception('Cannot load Girls names.');
        }

        $this->girls_names = explode("\n", $data);

        shuffle($this->girls_names);
        $this->log('[DONE] Loaded: ' . count($this->girls_names) . $this->eol());
    }

    /**
     * load last names from txt file
     * @throws \Exception
     */
    private function fillLastNames()
    {
        $this->log('Loading Last names...');
        $this->last_names = array();

        $data = file_get_contents('Resources/last_names.txt', true);
        if ($data == false) {
            throw new \Exception('Cannot load Last names.');
        }

        $this->last_names = explode("\n", $data);

        shuffle($this->last_names);
        $this->log('[DONE] Loaded: ' . count($this->last_names) . $this->eol());
    }
}
