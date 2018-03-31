<?php
Class CRM_JSMW_Import {

  public $civicrmPath = '/Users/kapil/Sites/wordpress/wp-content/plugins/civicrm/civicrm/';
  public $importFullFileName = '/Users/kapil/Sites/wordpress/wp-content/plugins/import.csv';

  function __construct() {
    // you can run this program either from an apache command, or from the cli
    $this->initialize();
  }

  function initialize() {
    $civicrmPath = $this->civicrmPath;
    require_once $civicrmPath .'civicrm.config.php';
    require_once $civicrmPath .'CRM/Core/Config.php';
    $config = CRM_Core_Config::singleton();
  }

  function createContacts() {
    $read = fopen($this->importFullFileName, 'r');
    ini_set('memory_limit', '2048M');
    ini_set('max_execution_time', 10000000);
    $rows = fgetcsv($read);
    $results = civicrm_api3('StateProvince', 'get', array(
      'return' => array("abbreviation"),
      'country_id' => 1228,
      'options' => array('limit' => 0),
    ));
    $states = [];
    foreach ($results['values'] as $result) {
      $states[$result['abbreviation']] = $result['id'];
    }
    $fields = CRM_Core_BAO_UFGroup::getFields(18);
    while ($rows = fgetcsv($read)) {
      $state = CRM_Utils_Array::value(trim($rows[9]), $states);
      $country = 1228;
      if (empty($state) && in_array($rows[9], ['India', 'Karnataka'])) {
        $country = 1101;
        if (trim($rows[8]) == 'Ahmedabad') {
          $state = 1208;
        }
        elseif (in_array(trim($rows[8]), ['Laxmeshwar', 'Bangalore'])) {
          $state = 1201;
        }
      }
      list($primaryHouseholdName, $secondaryHouseholdName) = $this->extractHouseholdName($rows[6]);
      $params = [
        'first_name' => ucfirst($primaryHouseholdName),
        'last_name' => ucfirst($rows[5]),
        'email-Primary' => strtolower($rows[22]),
        'job_title' => $rows[31],
        'phone-1-1' => $rows[11],
        'phone-Primary-2' => $rows[26],
        'phone-2-1' => $rows[24],
        'street_address-Primary' => $rows[7],
        'city-Primary' => $rows[8],
        'country-Primary' => $country,
        'state_province-Primary' => $state,
        'postal_code-Primary' => $rows[10],
        'custom_29' => ucfirst($secondaryHouseholdName),
        'custom_30' => strtolower($rows[23]),
        'custom_33' => $rows[39],
        'custom_32' => $rows[27],
        'custom_31' => $rows[25],
        'custom_40' => $rows[32],
        'custom_41' => $rows[35],
        'custom_42' => $rows[33],
        'custom_43' => $rows[34],
        'external_identifier' => $rows[0],
      ];
      if (!empty($secondaryHouseholdName)) {
        $params['custom_34'] = ucfirst($rows[5]);
      }
      $childCount = 0;
      if (!empty($rows[12])) {
        $params += [
          'custom_14' => ucfirst($rows[12]),
          'custom_15' => ucfirst($rows[5]),
          'custom_35' => strtolower($rows[37]),
          'custom_24' => $rows[13],
        ];
        $childCount++;
      }
      if (!empty($rows[14])) {
        $params += [
        'custom_16' => ucfirst($rows[14]),
        'custom_17' => ucfirst($rows[5]),
        'custom_36' => strtolower($rows[38]),
        'custom_25' => $rows[15],
        ];
        $childCount++;
      }
      if (!empty($rows[16])) {
        $params += [
        'custom_18' => ucfirst($rows[16]),
        'custom_19' => ucfirst($rows[5]),
        'custom_37' => NULL,
        'custom_26' => $rows[17],
        ];
        $childCount++;
      }
      if (!empty($rows[18])) {
        $params += [
        'custom_20' => ucfirst($rows[18]),
        'custom_22' => ucfirst($rows[5]),
        'custom_38' => NULL,
        'custom_27' => $rows[19],
        ];
        $childCount++;
      }
      if (!empty($rows[20])) {
        $params += [
        'custom_21' => ucfirst($rows[20]),
        'custom_23' => ucfirst($rows[5]),
        'custom_39' => NULL,
        'custom_28' => $rows[21],
        ];
        $childCount++;
      }
      $params['custom_13'] = $childCount;
      foreach ([24, 25, 26, 27, 28] as $key) {
        $key = 'custom_' . $key;
        if (!empty($params[$key])) {
          $params[$key] = date('Ymd', strtotime($params[$key]));
        }
      }
      $params = array_filter($params);
      CRM_Contact_BAO_Contact::createProfileContact($params, $fields);
    }
  }

  function extractHouseholdName($householdName) {
    $householdName = explode('&', $householdName);
    return array_map('trim', $householdName);
  }

  function createMemberships() {
    $read = fopen($this->importFullFileName, 'r');
    ini_set('memory_limit', '2048M');
    ini_set('max_execution_time', 10000000);
    $rows = fgetcsv($read);
    while ($rows = fgetcsv($read)) {
      $contactId = CRM_Core_DAO::singleValueQuery("
        SELECT cr.contact_id_b FROM civicrm_relationship cr
          INNER JOIN civicrm_contact cc ON cc.id = cr.contact_id_a
            AND cr.relationship_type_id = 7 AND cc.external_identifier = {$rows[0]}
      ");
      if (!$contactId) {
        $contactId = CRM_Core_DAO::singleValueQuery("
          SELECT id FROM civicrm_contact WHERE external_identifier = {$rows[0]}
        ");
      }
      $params = array(
        'contact_id' => $contactId,
        'membership_type_id' => 1,
        'is_override' => 1,
        'status_id' => 2,
        'custom_44' => $rows[3],
        'custom_45' => $rows[4],
      );
      if (!empty($rows[1])) {
        $params['join_date'] = date('Ymd', strtotime($rows[1]));
      }
      civicrm_api3('Membership', 'create', $params);
    }
  }
}

$import = new CRM_JSMW_Import();
//$import->createContacts();
$import->createMemberships();
exit;
