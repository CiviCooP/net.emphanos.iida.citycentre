<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Citycentre_AutomatchConfig {

  private static $singleton;

  private $custom_group;

  private $zip_code_range_from_field;

  private $zip_code_range_to_field;

  private function __construct()
  {
    $this->custom_group = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'Automatch_citycentre'));
    $this->zip_code_range_from_field = civicrm_api3('CustomField', 'getsingle', array('name' => 'Citycentre_zip_code_range_from', 'custom_group_id' => $this->custom_group['id']));
    $this->zip_code_range_to_field = civicrm_api3('CustomField', 'getsingle', array('name' => 'Citycentre_zip_code_range_to', 'custom_group_id' => $this->custom_group['id']));

  }

  public static function singelton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Citycentre_AutomatchConfig();
    }
    return self::$singleton;
  }

  public function getZipCodeRangeFromField($key='id') {
    return $this->zip_code_range_from_field[$key];
  }

  public function getZipCodeRangeToField($key='id') {
    return $this->zip_code_range_to_field[$key];
  }

  public function getCustomGroup($key='id') {
    return $this->custom_group[$key];
  }

}