<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Citycentre_CitycentreConfig {

  private static $singleton;

  private $custom_group;

  private $chapter_field;

  private $manual_field;

  private function __construct()
  {
    $this->custom_group = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'Citycentre'));
    $this->citycentre_field = civicrm_api3('CustomField', 'getsingle', array('name' => 'Citycentre', 'custom_group_id' => $this->custom_group['id']));
    $this->manual_field = civicrm_api3('CustomField', 'getsingle', array('name' => 'Manual_link_citycentre', 'custom_group_id' => $this->custom_group['id']));
  }

  /**
   * @return CRM_Citycentre_CitycentreConfig
   */
  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Citycentre_CitycentreConfig();
    }
    return self::$singleton;
  }

  public function getCustomGroup($key='id') {
    return $this->custom_group[$key];
  }

  public function getManualField($key='id') {
    return $this->manual_field[$key];
  }

  public function getCitycentreField($key='id') {
    return $this->citycentre_field[$key];
  }

}