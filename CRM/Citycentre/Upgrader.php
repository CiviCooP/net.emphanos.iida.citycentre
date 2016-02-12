<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Citycentre_Upgrader extends CRM_Citycentre_Upgrader_Base {

  public function install() {
    $this->executeCustomDataFile('xml/Citycentre.xml');
  }

  public function upgrade_1001() {
    $this->deleteCustomField('Automatch_citycentre', 'Citycentre_zip_code_range_from');
    $this->deleteCustomField('Automatch_citycentre', 'Citycentre_zip_code_range_to');
    $this->deleteCustomGroup('Automatch_citycentre');

    $this->deleteCustomField('Citycentre', 'Manual_link_citycentre');
    return true;
  }

  public function deleteCustomField($custom_group_name, $custom_field_name) {
    $cg_id = civicrm_api3('CustomGroup', 'getvalue', array('name' => $custom_group_name, 'return' => 'id'));
    try {
      $cf_id = civicrm_api3('CustomField', 'getvalue', array('name' => $custom_field_name, 'custom_group_id' => $cg_id, 'return' => 'id'));
    } catch (Exception $e) {
      //do nothing
    }
    if ($cf_id) {
      civicrm_api3('CustomField', 'delete', array('id' => $cf_id));
    }
  }

  public function deleteCustomGroup($custom_group_name) {
    $cg_id = civicrm_api3('CustomGroup', 'getvalue', array('name' => $custom_group_name, 'return' => 'id'));
    civicrm_api3('CustomGroup', 'delete', array('id' => $cg_id));
  }

}
