<?php

require_once 'CRM/Report/Form.php';

class CRM_Deelnemerlijst_Form_Report_Deelnemerlijst extends CRM_Deelnemerlijst_Form_Report_DeelnemerlijstBase {

	function __construct() {
		parent::__construct();
		
		foreach($this->_columns as $table_name => $table) {
			foreach($table['fields'] as $field_name => $field) {
				$unset_field = true;
				if ($table_name == 'civicrm_contact') {
					if ($field_name == 'first_name') {
						$this->_columns[$table_name]['fields'][$field_name]['required'] = TRUE;
						$this->_columns[$table_name]['fields'][$field_name]['title'] = ts('Name');
						$unset_field = false;
					}
					if ($field_name == 'last_name') {
						$this->_columns[$table_name]['fields'][$field_name]['required'] = TRUE;
						$this->_columns[$table_name]['fields'][$field_name]['no_display'] = TRUE;
						$unset_field = false;
					}	
				} elseif ($table_name == $this->vkw_inschrijving_table) {
					if ($field_name == 'custom_'.$this->vkw_inschrijving_fields['Functie_deelnemer']['id'] ) {
						$unset_field = false;
						$this->_columns[$table_name]['fields'][$field_name]['required'] = TRUE;
						$this->_columns[$table_name]['fields'][$field_name]['no_display'] = TRUE;
					}
					if ($field_name == 'custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id'] ) {
						$unset_field = false;
						$this->_columns[$table_name]['fields'][$field_name]['required'] = TRUE;
						$this->_columns[$table_name]['fields'][$field_name]['title'] = ts('Organisatie');
					}
				} elseif ($table_name == 'civicrm_organisation_address') {
					if ($field_name == 'city') {
						$this->_columns[$table_name]['fields'][$field_name]['required'] = TRUE;
						$this->_columns[$table_name]['fields'][$field_name]['no_display'] = TRUE;
						$unset_field = false;
					}
				}
				
				if ($unset_field) {
					unset($this->_columns[$table_name]['fields'][$field_name]);
				}
			}
		}
	}
	
	function alterDisplay(&$rows) {
		// custom code to alter rows

		$entryFound = FALSE;
		foreach($rows as $index => $row) {
			if (isset($row['civicrm_contact_first_name']) && isset($row['civicrm_contact_last_name']) && isset($row[$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Functie_deelnemer']['id']])) {
				$entryFound = TRUE;
				$rows[$index]['civicrm_contact_first_name'] = "<span style=\"font-weight: bold;\">".$row['civicrm_contact_last_name']."</span> <span>".$row['civicrm_contact_first_name']."</span>\n<br />".$row[$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Functie_deelnemer']['id']];
			}
			
			if (isset($row[$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id']]) && isset($row['civicrm_organisation_address_city'])) {
				$entryFound = TRUE;
				$rows[$index][$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id']] = "<span style=\"font-weight: bold;\">".$row[$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id']]."</span>\n<br />".$row['civicrm_organisation_address_city'];
			}
			
			if (!$entryFound) {
				break;
			}
		}
	}
}
