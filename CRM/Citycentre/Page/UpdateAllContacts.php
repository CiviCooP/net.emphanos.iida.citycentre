<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Citycentre_Page_UpdateAllContacts extends CRM_Core_Page {

  function run() {
    //retrieve the queue
    $cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE, 0);
    $queue = CRM_Citycentre_UpdateQueue::singleton()->getQueue();
    $runner = new CRM_Queue_Runner(array(
      'title' => ts('Update contacts'), //title fo the queue
      'queue' => $queue, //the queue object
      'errorMode'=> CRM_Queue_Runner::ERROR_ABORT, //abort upon error and keep task in queue
      'onEnd' => array('CRM_Citycentre_Page_UpdateAllContacts', 'onEnd'), //method which is called as soon as the queue is finished
      'onEndUrl' => CRM_Utils_System::url('civicrm/contact/view', 'reset=1&selectedChild=tab_automatch_citycentre&cid='.$cid, true, null, false), //go to page after all tasks are finished
    ));

    $runner->runAllViaWeb(); // does not return
  }

  /**
   * Handle the final step of the queue
   */
  static function onEnd(CRM_Queue_TaskContext $ctx) {
    //set a status message for the user
    CRM_Core_Session::setStatus('All contacts are updated', 'Queue', 'success');
  }

}