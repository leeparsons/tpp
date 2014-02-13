<?php
/**
 * User: leeparsons
 * Date: 11/01/2014
 * Time: 14:55
 */
 
class TppStoreModelMentorSpecialisms extends TppStoreAbstractModelResource {

    public $mentor_id;
    public $specialities = null;

    protected $_table = 'shop_mentor_specialisms';

    public function getSpecialism_one() {
        exit();
    }

    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->specialities = array_filter(array(
                filter_input(INPUT_POST, 'specialism_one', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'specialism_two', FILTER_SANITIZE_STRING),
                filter_input(INPUT_POST, 'specialism_three', FILTER_SANITIZE_STRING)
            ));
            return true;
        } else {
            return false;
        }
    }

    public function __get($name)
    {

        if (property_exists($this, $name)) {
            return $this->name;
        }

        switch ($name) {
            case 'specialism_one':
                if (isset($this->specialities[0])) {
                    return $this->specialities[0];
                }
                break;

            case 'specialism_two':
                if (isset($this->specialities[1])) {
                    return $this->specialities[1];
                }

                break;

            case 'specialism_three':
                if (isset($this->specialities[2])) {
                    return $this->specialities[2];
                }

                break;

            default:
                break;
        }
        return null;

    }



    /*
     * Called by mentor controller to set the specilisms on bulk from one sql query to save on performance
     */
    public function setSpecialisms($specialisms = array())
    {
        if (is_array($specialisms)) {

            $this->specialities = array();

            foreach ($specialisms as $specialism) {
                $this->specialities[] = $specialism;
            }

        } else {
            $this->specialities = array();
        }
    }

    public function getSpecialisms()
    {

        if (is_null($this->specialities)) {
            $this->specialities = array();
            if (intval($this->mentor_id) > 0) {
                global $wpdb;

                $wpdb->query(
                    $wpdb->prepare(
                        "SELECT * FROM " . $this->getTable() . " WHERE mentor_id = %d",
                        $this->mentor_id
                    ),
                    OBJECT_K
                );


                if ($wpdb->num_rows > 0) {

                    $this->specialities = array();

                    foreach ($wpdb->last_result as $row) {
                        $this->specialities[] = $row->speciality;
                    }
                }
            }
        }




        return $this;

    }

    public function getSpecialities()
    {
        $this->getSpecialisms();

        $e = $this->specialism_one;

        print_r($e);

    }

    /*
     * bespoke method called and used only on mentors controller to save on overheads
     */
    public function getSpecialismsByMentors($mentor_ids = array())
    {

        if (!is_array($mentor_ids) || empty($mentor_ids)) {
            return false;
        }

        global $wpdb;


        $wpdb->query(
            "SELECT GROUP_CONCAT(speciality) AS specialties, mentor_id FROM " . $this->getTable() . "
             WHERE mentor_id IN (" . implode(',', $mentor_ids) . ")
             GROUP BY mentor_id"
        );

        if ($wpdb->num_rows == 0) {
            return false;
        }

        $return = array();

        foreach ($wpdb->last_result as $row) {
            $return[$row->mentor_id] = explode(',', $row->specialties);
        }

        return $return;

    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        global $wpdb;

        $sql_array = array();


        $this->delete();

        if (!empty($this->specialities)) {
            foreach ($this->specialities as $speciality) {
                $sql_array[] = intval($this->mentor_id) . ",'" . esc_attr(trim($speciality)) . "'";
            }



            $wpdb->query(
                "INSERT INTO " . $this->getTable() . "
                (mentor_id, speciality)
                VALUES
                (" . implode("),(", $sql_array) . ")"
            );

        }




    }

    public function delete()
    {
        global $wpdb;

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM " . $this->getTable() . " WHERE mentor_id = %d",
                $this->mentor_id
            )
        );

    }

    private function validate()
    {
        if (empty($this->specialities)) {
            TppStoreMessages::getInstance()->addMessage('error', 'No Specialities Set');
            return false;
        }


        return true;
    }


}