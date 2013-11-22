<?php

require_once 'CRM/Report/Form.php';

class CRM_Deelnemerlijst_Form_Report_DeelnemerlijstBase extends CRM_Report_Form_Event_ParticipantListing {

	protected $vkw_inschrijving_table = false;
	protected $vkw_inschrijving_fields = array();
	
	protected $_customGroupFilters = FALSE;

	function __construct() {
		$gid = false;
		$table_name = false;
		
		$fields = array();
		
		$result = civicrm_api('CustomGroup', 'getsingle', array('version'=>3, 'name' => 'VKW_inschrijving')); 
		if (!isset($result['is_error']) || !$result['is_error']) {
			$gid = $result['id'];
			$table_name = $result['table_name'];
			if ($gid) {
				$result = civicrm_api('CustomField', 'get', array('version'=>3, 'custom_group_id' => $gid));
				if (isset($result['values']) && is_array($result['values'])) {					
					foreach($result['values'] as $field) {
						$fields[$field['name']] = $field;
					}
				}
			}
		}
		
		$this->vkw_inschrijving_table = $table_name;
		$this->vkw_inschrijving_fields = $fields;
	
		parent::__construct();
		
		$this->_columns['civicrm_contact']['fields']['sort_name_linked']['required'] = FALSE;
		$this->_columns['civicrm_contact']['fields']['sort_name_linked']['no_repeat'] = FALSE;
		unset($this->_columns['civicrm_contact']['fields']['sort_name_linked']);	
		$this->_columns['civicrm_contact']['fields']['last_name']['default']  = TRUE;
		$this->_columns['civicrm_contact']['fields']['first_name']['default']  = TRUE;
		$this->_columns['civicrm_participant']['fields']['status_id']['default'] = FALSE;
		$this->_columns['civicrm_participant']['fields']['event_id']['default'] = FALSE;
		$this->_columns['civicrm_participant']['fields']['role_id']['default'] = FALSE;
		unset($this->_columns['civicrm_participant']['fields']['fee_currency']);
		unset($this->_columns['civicrm_participant']['fields']['participant_fee_level']);
		unset($this->_columns['civicrm_participant']['fields']['participant_fee_amount']);
		$this->_columns['civicrm_phone']['fields']['phone']['default'] = FALSE;
		unset($this->_columns['civicrm_contribution']);
		
		
		if ($table_name && isset($fields['Organisatie_deelnemer'])) {
			$this->_columns[$table_name]['fields']['custom_'.$fields['Organisatie_deelnemer']['id']]['default'] = TRUE;
			$this->_columns['civicrm_organisation_address'] = array(
				'dao' => 'CRM_Core_DAO_Address',
				'alias' => 'civicrm_organisation_address',
				'fields' => array(
					'street_address' => NULL,
					'city' => array (
						'default' => TRUE,
					),
					'postal_code' => NULL,
					'state_province_id' =>
						array('title' => ts('State/Province'),
					),
					'country_id' =>
						array('title' => ts('Country'),
					),
				),
				'grouping' => $this->_columns[$table_name]['grouping'],
			);
		}
		if ($table_name && isset($fields['Functie_deelnemer'])) {
			$this->_columns[$table_name]['fields']['custom_'.$fields['Functie_deelnemer']['id']]['default'] = TRUE;
		}
		
		//resetting filters
		$this->_columns['civicrm_participant']['filters']['event_id']['operatorType'] = CRM_Report_Form::OP_SELECT;
		$this->_columns['civicrm_participant']['filters']['event_id']['required'] = TRUE;
		$this->_columns['civicrm_participant']['filters']['rid']['default'] = CRM_Utils_Array::implodePadded(array_keys(CRM_Event_PseudoConstant::participantRole(null, 'v.name = \'Attendee\'')), ',');
		unset($this->_columns['civicrm_participant']['filters']['sid']);
		unset($this->_columns['civicrm_participant']['filters']['participant_register_date']);
		unset($this->_columns['civicrm_participant']['filters']['fee_currency']);
		unset($this->_columns['civicrm_email']['filters']);
		unset($this->_columns['civicrm_contact']['filters']);
		unset($this->_columns['civicrm_event']['filters']);
		unset($this->_columns['civicrm_contribution']['filters']);
		
		unset($this->_columns['civicrm_participant']['order_bys']);
		unset($this->_columns['civicrm_event']['order_bys']);
		
		$this->_options = array();
	}
	
	function addOrderBys() {
		$this->assign('orderByOptions', array());
	}
	
	function customDataFrom() {
		parent::customDataFrom();
		if (isset($this->_aliases['civicrm_organisation_address']) && $this->vkw_inschrijving_table && isset($this->vkw_inschrijving_fields['Organisatie_deelnemer'])) {
			$this->_from .= "
				LEFT JOIN civicrm_address {$this->_aliases['civicrm_organisation_address']}
                    ON {$this->_aliases[$this->vkw_inschrijving_table]}.{$this->vkw_inschrijving_fields['Organisatie_deelnemer']['column_name']} = {$this->_aliases['civicrm_organisation_address']}.contact_id AND
                       {$this->_aliases['civicrm_organisation_address']}.is_primary = 1
			";
		}
	}
	
	/**
	 * Zorg ervoor dat eerst achternaam en dan voornaam getoond wordt
	 */
	function modifyColumnHeaders() {
		// use this method to modify $this->_columnHeaders
		$this->_columnHeaders = $this->array_swap_assoc('civicrm_contact_first_name', 'civicrm_contact_last_name', $this->_columnHeaders);
	}
	
	private function array_swap_assoc($key1='',$key2='',$arrOld=array()) {
		$arrNew = array ();
		
		$elementA = $arrOld[$key1];
		$elementB = $arrOld[$key2];
		
		foreach($arrOld as $key => $el) {
			if ($key == $key1) {
				$arrNew[$key2] = $elementB;
			} elseif ($key == $key2) {
				$arrNew[$key1] = $elementA;
			} else {
				$arrNew[$key] = $el;
			}
		}
		
		return $arrNew;
	}
	
	function getEventFilterOptions() {
		$events = array();
		$query = "
			select id, start_date, title from civicrm_event
			where (is_template IS NULL OR is_template = 0) AND is_active
			order by start_date DESC, title ASC 
		";
		$dao = CRM_Core_DAO::executeQuery($query);
		while($dao->fetch()) {
		$events[$dao->id] = CRM_Utils_Date::customFormat(substr($dao->start_date, 0, 10)) . " :: {$dao->title} (ID {$dao->id})";
		}
		return $events;
	}
}
