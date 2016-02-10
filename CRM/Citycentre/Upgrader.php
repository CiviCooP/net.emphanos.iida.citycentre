<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Citycentre_Upgrader extends CRM_Citycentre_Upgrader_Base {

  public function install() {
    $this->executeCustomDataFile('xml/Automatch_citycentre.xml');
    $this->executeCustomDataFile('xml/Citycentre.xml');
  }

}
