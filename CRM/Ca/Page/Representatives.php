<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2016                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2016
 * $Id$
 *
 */
class CRM_Ca_Page_Representatives extends CRM_Core_Page {

  function run() {
    $geocode = $_POST['geocode'];
    $representatives = $targets = $reps = array();
    $url = ENDPOINT . "/representatives/?point=" . $geocode[0] . "," . $geocode[1];

    // Get fixed group targets.
    $fixed = civicrm_api3('GroupContact', 'get', array(
      'sequential' => 1,
      'return' => array("contact_id"),
      'group_id' => "Petition_Targets",
      'status' => "Added",
    ));

    if ($fixed['count'] > 0) {
      foreach ($fixed['values'] as $contact) {
        $targets[] = CRM_Ca_BAO_Represent::getContactDetails($contact['contact_id']);
      }
    }

    $representatives = CRM_Ca_BAO_Represent::getInfo($url);
    if (!empty($representatives) && $representatives->meta->total_count > 0) {
      foreach ($representatives->objects as $key => $values) {
        $reps[] = array(
          'display_name' => $values->name,
          'url' => $values->url,
          'district_name' => $values->district_name,
          'party_name' => $values->party_name,
          'elected_office' => $values->elected_office,
          'email' => $values->email,
        );
      }
    }
    $master =  array_merge($targets, $reps);
    if (!empty($master)) {
      echo json_encode($master);
    }
    else {
      echo 0;
    }
    CRM_Utils_System::civiExit();
  }

}
