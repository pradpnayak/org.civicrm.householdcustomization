<?php

require_once 'householdcustomization.civix.php';
use CRM_Householdcustomization_ExtensionUtil as E;

define('MEMBERSHIP_ONLINE_FORM_ID', 1);
define('HOUSEHOLD_PROFILE_ID', 18);
define('HEAD_OF_HOUSEHOLD_RTYPE', 7);
define('MEMBER_OF_HOUSEHOLD_RTYPE', 8);
define('CHILD_OF_RTYPE', 1);
define('SIBLINGS_RTYPE', 4);
define('SPOUSE_OF_RTYPE', 2);

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function householdcustomization_civicrm_config(&$config) {
  global $wp;
  if ($wp && $wp->request == 'user-dashboard') {
    $householdId = householdcustomization_getHouseholdId();
    if ($householdId) {
      $_REQUEST['id']= $householdId;
    }
  }
  _householdcustomization_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function householdcustomization_civicrm_xmlMenu(&$files) {
  _householdcustomization_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function householdcustomization_civicrm_install() {
  _householdcustomization_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function householdcustomization_civicrm_postInstall() {
  _householdcustomization_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function householdcustomization_civicrm_uninstall() {
  _householdcustomization_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function householdcustomization_civicrm_enable() {
  _householdcustomization_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function householdcustomization_civicrm_disable() {
  _householdcustomization_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function householdcustomization_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _householdcustomization_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function householdcustomization_civicrm_managed(&$entities) {
  _householdcustomization_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function householdcustomization_civicrm_caseTypes(&$caseTypes) {
  _householdcustomization_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function householdcustomization_civicrm_angularModules(&$angularModules) {
  _householdcustomization_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function householdcustomization_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _householdcustomization_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function householdcustomization_civicrm_buildForm($formName, &$form) {
  if ((in_array($formName, [
      'CRM_Contribute_Form_Contribution_Main',
      'CRM_Contribute_Form_Contribution_Confirm',
      'CRM_Contribute_Form_Contribution_ThankYou',
    ]) && $form->_id == MEMBERSHIP_ONLINE_FORM_ID)
    || ('CRM_Profile_Form_Edit' == $formName
      && $form->getVar('_gid') == HOUSEHOLD_PROFILE_ID
    )
  ) {
    CRM_Core_Resources::singleton()->addScriptFile('org.civicrm.householdcustomization', 'js/hideFields.js');
    $customFields = householdcustomization_getHouseHoldCustomFields();
    CRM_Core_Resources::singleton()->addVars('customFields', $customFields);
    CRM_Core_Resources::singleton()->addVars('noOfChildrenField', ['hideField' => 'custom_13']);
  }
}

/**
 * Implements hook_civicrm_post().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_post
 */
function householdcustomization_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ('Profile' == $objectName && in_array($op, ['create', 'edit'])) {
    // create spouse records
    $spouseId = NULL;
    if ((!empty($objectRef['custom_29']) && !empty($objectRef['custom_34']))
      || (!empty($objectRef['custom_30']))
    ) {
      $params = [
        'first_name' => CRM_Utils_Array::value('custom_29', $objectRef),
        'last_name' => CRM_Utils_Array::value('custom_34', $objectRef),
        'email' => CRM_Utils_Array::value('custom_30', $objectRef),
        'job_title' => CRM_Utils_Array::value('custom_33', $objectRef),
        'contact_type' => 'Individual',
      ];
      $spouse = householdcustomization_createContact($params);
      $spouseId = $spouse['id'];
      if ($spouseId) {
        householdcustomization_createRelationships($spouseId, $objectId, SPOUSE_OF_RTYPE);
        householdcustomization_shareAddress($objectId, $spouseId);
        if (!empty($objectRef['custom_32'])) {
          $phoneParams = [
            'contact_id' => $spouseId,
            'location_type_id' => 1,
            'phone' => $objectRef['custom_32'],
            'is_primary' => 1,
            'phone_type_id' => 2,
          ];
          householdcustomization_createContactPhone($phoneParams);
        }
        if (!empty($objectRef['custom_31'])) {
          $phoneParams = [
            'contact_id' => $spouseId,
            'location_type_id' => 2,
            'phone' => $objectRef['custom_31'],
            'phone_type_id' => 1,
          ];
          householdcustomization_createContactPhone($phoneParams);
        }
      }
    }
    if (!empty($spouseId) || !empty($objectRef['custom_13'])) {
      $houseHoldId = householdcustomization_createHousehold($objectRef, $objectId, $spouseId);
    }
    // create child records
    if (!empty($objectRef['custom_13'])) {
      $customFields = householdcustomization_getHouseHoldCustomFields();
      $childContactIds = [];
      $count = 0;
      foreach ($customFields as $key => $value) {
        if ($count >= $objectRef['custom_13']) {
          break;
        }
        if ((empty($objectRef[$value[0]]) || empty($objectRef[$value[1]])) && empty($objectRef[$value[2]])) {
          continue;
        }
        $params = [
          'first_name' => CRM_Utils_Array::value($value[0], $objectRef),
          'last_name' => CRM_Utils_Array::value($value[1], $objectRef),
          'email' => CRM_Utils_Array::value($value[2], $objectRef),
          'birth_date' => CRM_Utils_Array::value($value[3], $objectRef),
          'contact_type' => 'Individual',
        ];
        $contact = householdcustomization_createContact($params);
        if (!empty($contact['id'])) {
          householdcustomization_createRelationships($contact['id'], $houseHoldId, MEMBER_OF_HOUSEHOLD_RTYPE);
          householdcustomization_createRelationships($contact['id'], $objectId, CHILD_OF_RTYPE);
          householdcustomization_createRelationships($contact['id'], $spouseId, CHILD_OF_RTYPE);
          householdcustomization_shareAddress($objectId, $contact['id']);
          foreach ($childContactIds as $childContactId) {
            householdcustomization_createRelationships($contact['id'], $childContactId, SIBLINGS_RTYPE);
          }
          $childContactIds[] = $contact['id'];
          $count++;
        }
      }
    }
  }
}

function householdcustomization_createHousehold($params, $contactId, $spouseId) {
  $houseHoldName = householdcustomization_createHouseholdName($contactId, $spouseId);
  $houseHoldparams = [
    'contact_type' => 'Household',
    'household_name' => $houseHoldName,
    'primary_contact_id' => $contactId,
  ];
  $relParams = [
    'contact_id_a' => $contactId,
    'relationship_type_id' => HEAD_OF_HOUSEHOLD_RTYPE,
    'is_active' => TRUE,
  ];
  try {
    $rel = civicrm_api3('Relationship', 'getsingle', $relParams);
    $houseHoldparams['id'] = $rel['contact_id_b'];
  }
  catch (CiviCRM_API3_Exception $e) {
    // do nothing
  }
  $houseHold = householdcustomization_createContact($houseHoldparams, 'Household');
  $houseHoldId = $houseHold['id'];
  householdcustomization_createRelationships($contactId, $houseHoldId, HEAD_OF_HOUSEHOLD_RTYPE);
  householdcustomization_createRelationships($spouseId, $houseHoldId, MEMBER_OF_HOUSEHOLD_RTYPE);
  householdcustomization_shareAddress($contactId, $houseHoldId);
  return $houseHoldId;
}

function householdcustomization_createContact($params, $contactType = 'Individual') {
  if (empty($params['id'])) {
    $dedupeParams = CRM_Dedupe_Finder::formatParams($params, $contactType);
    $dedupeParams['check_permission'] = FALSE;
    $dupes = CRM_Dedupe_Finder::dupesByParams($dedupeParams, $contactType);
    $contactId = CRM_Utils_Array::value('0', $dupes, NULL);
    if ($contactId) {
      $params['id'] = $contactId;
    }
  }
  return civicrm_api3('Contact', 'create', $params);
}

function householdcustomization_createRelationships($contactA, $contactB, $relationshipType) {
  if (empty($contactA) || empty($contactB)) {
    return NULL;
  }
  $params = [
    'contact_id_a' => $contactA,
    'contact_id_b' => $contactB,
    'relationship_type_id' => $relationshipType,
    'is_active' => 1,
  ];
  try {
    civicrm_api3('Relationship', 'getsingle', $params);
  }
  catch (CiviCRM_API3_Exception $e) {
    civicrm_api3('Relationship', 'create', $params);
  }
}

function householdcustomization_getHouseHoldCustomFields() {
  return [
    1 => ['custom_14', 'custom_15', 'custom_35', 'custom_24'],
    2 => ['custom_16', 'custom_17', 'custom_36', 'custom_25'],
    3 => ['custom_18', 'custom_19', 'custom_37', 'custom_26'],
    4 => ['custom_20', 'custom_22', 'custom_38', 'custom_27'],
    5 => ['custom_21', 'custom_23', 'custom_39', 'custom_28'],
  ];
}

function householdcustomization_createContactPhone($params) {
  try {
    civicrm_api3('Phone', 'getsingle', $params);
  }
  catch (CiviCRM_API3_Exception $e) {
    civicrm_api3('Phone', 'create', $params);
  }
}

function householdcustomization_civicrm_pre($op, $objectName, $id, &$params) {
  if ($op == 'create' && in_array($objectName,
      ['Contribution', 'Membership', 'Pledge']
    )
  ) {
    if (!empty($params['owner_membership_id'])
      || ($objectName != 'Membership' && empty($params['contribution_page_id']))
      || ($objectName == 'Membership' && (empty($params['contribution'])
      || (!empty($params['contribution'])
        && empty($params['contribution']->contribution_page_id)))
      )
    ) {
      return NULL;
    }
    if (!empty($params['contact_id'])) {
      $relParams = [
        'contact_id_a' => $params['contact_id'],
        'relationship_type_id' => HEAD_OF_HOUSEHOLD_RTYPE,
        'is_active' => 1,
        'return' => 'contact_id_b',
      ];
      try {
        $houseHoldId = civicrm_api3('Relationship', 'getvalue', $relParams);
        if ($houseHoldId) {
          $params['contact_id'] = $houseHoldId;
        }
      }
      catch (CiviCRM_API3_Exception $e) {
        // do nothing
      }
    }
  }
}

function householdcustomization_createHouseholdName($contactId, $spouseId) {
  $contactIds = [$contactId, $spouseId];
  $contactIds = array_filter($contactIds);
  $query = 'SELECT first_name, last_name FROM civicrm_contact WHERE id IN (' . implode(',', $contactIds) . ') ORDER BY gender_id DESC';
  $contactDAO = CRM_Core_DAO::executeQuery($query);
  $householdName = NULL;
  $records = [];
  while ($contactDAO->fetch()) {
    $records[trim($contactDAO->last_name)][] = trim($contactDAO->first_name);
  }
  foreach ($records as $key => $value) {
    if (!empty($householdName)) {
      $householdName = $householdName . " & " . $key . ", " . implode(' & ', $value);
    }
    else {
      $householdName = $key . ", " . implode(' & ', $value);
    }
  }
  return $householdName;
}

function householdcustomization_shareAddress($contactId, $secondaryContactID) {
  try {
    $address = civicrm_api3('Address', 'getsingle', ['contact_id' => $contactId, 'is_primary' => 1]);
    $address['contact_id'] = $secondaryContactID;
    $address['master_id'] = $address['id'];
    unset($address['id']);
    civicrm_api3('Address', 'create', $address);
  }
  catch (CiviCRM_API3_Exception $e) {
    // do nothing
  }
}

function householdcustomization_civicrm_pageRun(&$page) {
  if (get_class($page) == 'CRM_Contact_Page_View_Summary') {
    CRM_Core_Resources::singleton()->addStyle('#customFields div.Household_Data { display:none !important }');
  }

  if (get_class($page) == 'CRM_Contact_Page_View_UserDashBoard'
    && $page->_contactId != CRM_Core_Session::singleton()->get('userID')
  ) {
    $pledges = $page->get_template_vars('pledge_rows');
    $contactChecksum = CRM_Contact_BAO_Contact_Utils::generateChecksum($page->_contactId);
    $paymentLinks = [];
    foreach ($pledges as $pledge) {
      if ($pledge['pledge_status'] == 'Completed' || empty($pledge['pledge_contribution_page_id'])) {
        continue;
      }
      $paymentLinks[$pledge['pledge_id']] = CRM_Utils_System::url(
        "civicrm/contribute/transact",
        [
          'reset' => 1,
          'cid' => $page->_contactId,
          'cs' => $contactChecksum,
          'pledgeId' => $pledge['pledge_id'],
          'id' => $pledge['pledge_contribution_page_id'],
        ],
        TRUE
      );
    }
    if (!empty($paymentLinks)) {
      CRM_Core_Resources::singleton()->addScriptFile('org.civicrm.householdcustomization', 'js/showPaymentLink.js');
      CRM_Core_Resources::singleton()->addVars('paymentLinks', $paymentLinks);
    }
    $eventRows = $page->get_template_vars('event_rows');
    $result = CRM_Core_DAO::executeQuery("
      SELECT contact_id_a FROM civicrm_relationship WHERE contact_id_b = {$page->_contactId}
        AND relationship_type_id IN (7, 8)
    ");
    while ($result->fetch()) {
      $contactId = $result->contact_id_a;
      $controller = new CRM_Core_Controller_Simple(
        'CRM_Event_Form_Search',
        ts('Events'),
        NULL,
        FALSE, FALSE, TRUE, FALSE
      );
      $controller->setEmbedded(TRUE);
      $controller->reset();
      $controller->set('context', 'user');
      $controller->set('cid', $contactId);
      $controller->set('force', 1);
      $controller->process();
      $controller->run();
      $eventRows = array_merge($eventRows, $page->get_template_vars('event_rows'));
    }
    $page->assign('event_rows', $eventRows);
  }
}

function householdcustomization_getHouseholdId() {
  $userContactId = CRM_Core_Session::singleton()->get('userID');
  if (!$userContactId) {
    return NULL;
  }
  $query = "SELECT contact_id_b FROM civicrm_relationship
    WHERE contact_id_a = {$userContactId}
      AND relationship_type_id IN (" . HEAD_OF_HOUSEHOLD_RTYPE . ',' . MEMBER_OF_HOUSEHOLD_RTYPE . ")";
  return CRM_Core_DAO::singleValueQuery($query);
}
