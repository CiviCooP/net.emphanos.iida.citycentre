<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Citycentre_Page_AutomatchCitycentre extends CRM_Core_Page {

  public function run() {
    $cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE, 0);
    $config = CRM_Citycentre_AutomatchConfig::singelton();
    $sql = "SELECT id, `".$config->getZipCodeRangeFromField('column_name')."` AS `zipcode_from`, `".$config->getZipCodeRangeToField('column_name')."` AS `zipcode_to` FROM `".$config->getCustomGroup('table_name')."` WHERE entity_id = %1";
    $params[1] = array($cid, 'Integer');
    $rows = array();
    $dao = CRM_Core_DAO::executeQuery($sql, $params);

    while($dao->fetch()) {
      $row = array();
      $row['zipcode_from'] = $dao->zipcode_from;
      $row['zipcode_to'] = $dao->zipcode_to;
      $row['id'] = $dao->id;
      $rows[] = $row;
    }

    $this->assign('rows', $rows);
    $this->assign('cid', $cid);

    parent::run();
  }

}