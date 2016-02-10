<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Citycentre_Form_AutomatchCitycentre extends CRM_Core_Form {

  protected $contact_id;

  protected $id;

  function preProcess()
  {
    parent::preProcess();
    $this->contact_id = CRM_Utils_Request::retrieve('cid', 'Integer', CRM_Core_DAO::$_nullObject, true);
    $this->id = CRM_Utils_Request::retrieve('id', 'Integer', CRM_Core_DAO::$_nullObject, false, 0);
  }

  public function buildForm()
  {
    parent::buildForm();
    $config = CRM_Citycentre_AutomatchConfig::singelton();

    $this->addElement('hidden', 'cid', $this->contact_id);
    if ($this->id) {
      $this->addElement('hidden', 'id', $this->id);
    }

    if ($this->_action & CRM_Core_Action::DELETE) {
      $this->addButtons(array(
          array(
            'type' => 'next',
            'name' => ts('Delete'),
            'isDefault' => TRUE,
          ),
          array(
            'type' => 'cancel',
            'name' => ts('Cancel'),
          ),
        )
      );
      return;
    }

    $this->addElement('text', 'zipcode_from', $config->getZipCodeRangeFromField('label'));
    $this->addElement('text', 'zipcode_to', $config->getZipCodeRangeToField('label'));

    $this->addButtons(array(
        array(
          'type' => 'next',
          'name' => ts('Save'),
          'isDefault' => TRUE,
        ),
        array(
          'type' => 'cancel',
          'name' => ts('Cancel'),
        ),
      )
    );
  }

  public function setDefaultValues()
  {
    $defaults = parent::setDefaultValues();
    if ($this->id) {
      $config = CRM_Citycentre_AutomatchConfig::singelton();
      $sql = "SELECT id, `".$config->getZipCodeRangeFromField('column_name')."` AS `zipcode_from`, `".$config->getZipCodeRangeToField('column_name')."` AS `zipcode_to` FROM `".$config->getCustomGroup('table_name')."` WHERE id = %1";
      $params[1] = array($this->id, 'Integer');
      $dao = CRM_Core_DAO::executeQuery($sql, $params);
      if ($dao->fetch()) {
        $defaults['zipcode_from'] = $dao->zipcode_from;
        $defaults['zipcode_to'] = $dao->zipcode_to;
      }
    }
    return $defaults;
  }

  public function addRules() {
    if ($this->_action & CRM_Core_Action::DELETE) {
      return;
    }
    $this->addFormRule(array('CRM_Citycentre_Form_AutomatchCitycentre', 'validateType'));
  }

  static function validateType($fields) {
    $errors = array();
    if (empty($fields['zipcode_from']) || !is_numeric($fields['zipcode_from']) || strlen($fields['zipcode_from']) != 5) {
      $errors['zipcode_from'] = ts('Zipcode from is not a valid US zip code. Please enter a 5 digit zipcode');
    }
    if (empty($fields['zipcode_to']) || !is_numeric($fields['zipcode_to']) || strlen($fields['zipcode_to']) != 5) {
      $errors['zipcode_to'] = ts('Zipcode to is not a valid US zip code. Please enter a 5 digit zipcode');
    }

    if (count($errors)) {
      return $errors;
    }

    return true;
  }

  public function postProcess() {
    $config = CRM_Citycentre_AutomatchConfig::singelton();

    if ($this->_action & CRM_Core_Action::DELETE) {
      CRM_Core_BAO_CustomValue::deleteCustomValue($this->id, $config->getCustomGroup('id'));
      $this->updateAllContacts();
      return;
    }

    $cg = 'custom_';
    $id = ':-1';
    if ($this->id) {
      $id = ':'.$this->id;
    }

    $data['entity_id'] = $this->contact_id;
    $data[$cg.$config->getZipCodeRangeFromField('id').$id] = $this->_submitValues['zipcode_from'];
    $data[$cg.$config->getZipCodeRangeToField('id').$id] = $this->_submitValues['zipcode_to'];

    civicrm_api3('CustomValue', 'create', $data);

    $this->updateAllContacts();
  }

  protected function updateAllContacts() {
    $queue = CRM_Citycentre_UpdateQueue::singleton()->getQueue();
    $max_contacts = CRM_Core_DAO::singleValueQuery("SELECT COUNT(*) FROM civicrm_contact WHERE is_deleted = 0");
    for($i=0; $i < $max_contacts; $i = $i + 500) {
      //create a task without parameters
      $task = new CRM_Queue_Task(
        array('CRM_Citycentre_Matcher', 'updateAllContacts'), //call back method
        array($i, 500) //parameters
      );
      //now add this task to the queue
      $queue->createItem($task);
    }

    $redirectUrl = CRM_Utils_System::url('civicrm/citycentre/update_all_contacts', 'reset=1&cid='.$this->contact_id, TRUE);
    CRM_Utils_System::redirect($redirectUrl);
  }

}