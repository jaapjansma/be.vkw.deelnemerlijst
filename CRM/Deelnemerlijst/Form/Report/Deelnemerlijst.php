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
						
						$this->_columns[$table_name]['fields'][$field_name.'_orig']['required'] = TRUE;
						$this->_columns[$table_name]['fields'][$field_name.'_orig']['no_display'] = TRUE;
						$this->_columns[$table_name]['fields'][$field_name.'_orig']['dbAlias'] = "''";
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
						
						$this->_columns[$table_name]['fields'][$field_name.'_orig']['required'] = TRUE;
						$this->_columns[$table_name]['fields'][$field_name.'_orig']['title'] = ts('Organisatie');
						$this->_columns[$table_name]['fields'][$field_name.'_orig']['dbAlias'] = "''";
						$this->_columns[$table_name]['fields'][$field_name.'_orig']['no_display'] = TRUE;
					}
				} elseif ($table_name == 'civicrm_organisation_address') {
					if ($field_name == 'city') {
						$this->_columns[$table_name]['fields'][$field_name]['required'] = TRUE;
						$this->_columns[$table_name]['fields'][$field_name]['no_display'] = TRUE;
						$unset_field = false;
					}
				} elseif ($table_name == 'civicrm_organisation_contact') {
					if ($field_name == 'display_name') {
						$unset_field = false;
					}
				}
	
				if ($unset_field) {
					unset($this->_columns[$table_name]['fields'][$field_name]);
				}
			}
		}
    }
	
	function filterStat(&$statistics) {
		
	}
	
	function preProcessCommon() {
		parent::preProcessCommon();
		$this->_docButtonName = $this->getButtonName('submit', 'doc');
	}
	
	function buildInstanceAndButtons() {
		parent::buildInstanceAndButtons();
		$this->addElement('submit', $this->_docButtonName, ts('Export to Word'));
	}
	
	function processReportMode() {
		$buttonName = $this->controller->getButtonName();

		$output = CRM_Utils_Request::retrieve(
		  'output',
		  'String',
		  CRM_Core_DAO::$_nullObject
		);

		$this->_sendmail = CRM_Utils_Request::retrieve(
			'sendmail',
			'Boolean',
			CRM_Core_DAO::$_nullObject
		);

		$this->_absoluteUrl = FALSE;
		$printOnly = FALSE;
		$this->assign('printOnly', FALSE);

		if ($this->_printButtonName == $buttonName || $output == 'print' || ($this->_sendmail && !$output)) {
			$this->assign('printOnly', TRUE);
			$printOnly = TRUE;
			$this->assign('outputMode', 'print');
			$this->_outputMode = 'print';
			if ($this->_sendmail) {
				$this->_absoluteUrl = TRUE;
			}
		}
		elseif ($this->_pdfButtonName == $buttonName || $output == 'pdf') {
			$this->assign('printOnly', TRUE);
			$printOnly = TRUE;
			$this->assign('outputMode', 'pdf');
			$this->_outputMode = 'pdf';
			$this->_absoluteUrl = TRUE;
		}
		elseif ($this->_csvButtonName == $buttonName || $output == 'csv') {
			$this->assign('printOnly', TRUE);
			$printOnly = TRUE;
			$this->assign('outputMode', 'csv');
			$this->_outputMode = 'csv';
			$this->_absoluteUrl = TRUE;
		}
		elseif ($this->_docButtonName == $buttonName || $output == 'doc') {
			$this->assign('printOnly', TRUE);
			$printOnly = TRUE;
			$this->assign('outputMode', 'doc');
			$this->_outputMode = 'doc';
			$this->_absoluteUrl = TRUE;
		}
		elseif ($this->_groupButtonName == $buttonName || $output == 'group') {
			$this->assign('outputMode', 'group');
			$this->_outputMode = 'group';
		}
		elseif ($output == 'create_report' && $this->_criteriaForm) {
			$this->assign('outputMode', 'create_report');
			$this->_outputMode = 'create_report';
		}
		else {
			$this->assign('outputMode', 'html');
			$this->_outputMode = 'html';
		}

		// Get today's date to include in printed reports
		if ($printOnly) {
			$reportDate = CRM_Utils_Date::customFormat(date('Y-m-d H:i'));
			$this->assign('reportDate', $reportDate);
		}
	}
	
	function alterDisplay(&$rows) {
		// custom code to alter rows

		$entryFound = FALSE;
		foreach($rows as $index => $row) {
			if (isset($row['civicrm_contact_first_name']) && isset($row['civicrm_contact_last_name']) || isset($row[$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Functie_deelnemer']['id']])) {
				$entryFound = TRUE;
				$rows[$index]['civicrm_contact_first_name_orig'] = $rows[$index]['civicrm_contact_first_name'];
				$rows[$index]['civicrm_contact_first_name'] = "<span style=\"font-weight: bold;\">".$row['civicrm_contact_last_name']."</span> <span>".$row['civicrm_contact_first_name']."</span>\n<br />".$row[$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Functie_deelnemer']['id']];
			}
			
			if (isset($row[$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id']]) || isset($row['civicrm_organisation_address_city'])) {
				$entryFound = TRUE;
				$rows[$index][$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id'].'_orig'] = $rows[$index][$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id']];
				$organisatie = $rows[$index][$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id'].'_orig'];
				if (!strlen($organisatie)) {
					$organisatie = $rows[$index]['civicrm_organisation_contact_display_name'];
				}
				
				$rows[$index][$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id']] = "<span style=\"font-weight: bold;\">".$organisatie."</span>\n<br />".$row['civicrm_organisation_address_city'];
			}
			
			if (!$entryFound) {
				break;
			}
		}
	}
	
	function endPostProcess(&$rows = NULL) {
		/*if ( $this->_storeResultSet ) {
			$this->_resultSet = $rows;
		}*/
		
		if ($this->_outputMode == 'doc') {
			$this->generateDocx($rows);
			CRM_Utils_System::civiExit();
		}
		parent::endPostProcess($rows);
	}
  
  function orderBy() {
    $this->_orderBy = " ORDER BY `civicrm_contact_last_name` ASC, `civicrm_contact_first_name` ASC";
  }
	
	function compileContent(){
		if ($this->_outputMode == 'doc') {
			$templateFile = $this->getHookedTemplateFileName();
			return CRM_Core_Form::$_template->fetch($templateFile);
		}
		return parent::compileContent();
	}
	
	function generateDocx($rows) {		
		header("Content-type: application/vnd.ms-word");
		header('Content-Disposition: attachment;filename="document.doc"');
		echo "<html>";
		echo "<head>";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">";
		echo "</head><body>";
		echo '<table style="width: 100%; font-family: Arial; font-size: 10pt; border-style: none; border-size: 0px;">';
		//echo '<tr><td><b>Naam</b></td><td><b>Organisatie</b></td></tr>';
		foreach ($rows as $index=> $row) {
			$firstName = $rows[$index]['civicrm_contact_first_name_orig'];
			$lastName = $rows[$index]['civicrm_contact_last_name'];
			$functie = $row[$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Functie_deelnemer']['id']];
			
			$organisatie = $row[$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id'].'_orig'];
			$plaats = $row['civicrm_organisation_address_city'];
			
			if (!strlen($organisatie)) {
				$organisatie = $rows[$index]['civicrm_organisation_contact_display_name'];
			}
			
			echo '<tr><td><b>'.$lastName.'</b> '.$firstName.'</td><td><b>'.$organisatie.'</b></td></tr>';
			echo '<tr><td>'.$functie.'</td><td>'.$plaats.'</td></tr>';
			echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
			
		}
		
		echo '</table>';
		echo '</body></html>';
		
		CRM_Utils_System::civiExit();
	}
	
}
