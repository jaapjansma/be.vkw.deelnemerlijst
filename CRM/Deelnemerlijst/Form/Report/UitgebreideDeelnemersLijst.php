<?php

require_once 'CRM/Report/Form.php';

class CRM_Deelnemerlijst_Form_Report_UitgebreideDeelnemersLijst extends CRM_Report_Form {

  protected $_addressField = FALSE;

  protected $_emailField = FALSE;

  protected $_summary = NULL;

  protected $_customGroupExtends = array('Membership', 'Participant');
  protected $_customGroupGroupBy = FALSE; 
  protected $_customGroupFilters = FALSE;
  
	protected $vkw_inschrijving_table = false;
	protected $vkw_inschrijving_fields = array();
	
	protected $vkw_lidmaatschap_table = false;
	protected $vkw_lidmaatschap_fields = array();
  
	function __construct() {
	
		$gid = false;
		$table_name = false;
		
		$fields = array();
		
		$result = civicrm_api('CustomGroup', 'getsingle', array('version'=>3, 'name' => 'VKW_lidmaatschap')); 
		if (!isset($result['is_error']) || !$result['is_error']) {
			$this->vkw_lidmaatschap_table = $result['table_name'];
			if ($result['id']) {
				$result = civicrm_api('CustomField', 'get', array('version'=>3, 'custom_group_id' => $result['id']));
				if (isset($result['values']) && is_array($result['values'])) {					
					foreach($result['values'] as $field) {
						$fields[$field['name']] = $field;
					}
				}
			}
		}
		
		$this->vkw_lidmaatschap_fields = $fields;
	
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
  
		$this->_columns = array(
		  'civicrm_contact' => array(
			'dao' => 'CRM_Contact_DAO_Contact',
			'fields' => array(
			  'sort_name' => array(
				'title' => ts('Contact Name'),
				'required' => TRUE,
				'default' => TRUE,
				'no_repeat' => TRUE,
			  ),
			  'id' => array(
				'no_display' => TRUE,
				'required' => TRUE,
			  ),
			  'first_name' => array(
				'title' => ts('First Name'),
				'no_repeat' => TRUE,
			  ),
			  'id' => array(
				'no_display' => TRUE,
				'required' => TRUE,
			  ),
			  'last_name' => array(
				'title' => ts('Last Name'),
				'no_repeat' => TRUE,
			  ),
			  'id' => array(
				'no_display' => TRUE,
				'required' => TRUE,
			  ),
			),
			'grouping' => 'contact-fields',
		  ),
		  'civicrm_membership' => array(
			'dao' => 'CRM_Member_DAO_Membership',
			'fields' => array(
			  'membership_type_id' => array(
				'title' => 'Membership Type',
				'required' => TRUE,
				'no_repeat' => TRUE,
			  ),
			  'join_date' => array('title' => ts('Join Date'),
				'default' => FALSE,
			  ),
			  'source' => array('title' => 'Source'),
			),
			'grouping' => 'member-fields',
		  ),
		  'civicrm_membership_status' => array(
			'dao' => 'CRM_Member_DAO_MembershipStatus',
			'alias' => 'mem_status',
			'fields' => array(
			  'name' => array(
				'title' => ts('Status'),
				'default' => FALSE,
			  ),
			),
			'grouping' => 'member-fields',
		  ),
		  'civicrm_email' => array(
			'dao' => 'CRM_Core_DAO_Email',
			'fields' => array('email' => NULL),
			'grouping' => 'contact-fields',
		  ),
		  'civicrm_participant' =>
		  array(
			'dao' => 'CRM_Event_DAO_Participant',
			'fields' =>
			array('participant_id' => array('title' => 'Participant ID'),
			  'participant_record' => array(
				'name' => 'id',
				'no_display' => TRUE,
				'required' => TRUE,
			  ),
			  'event_id' => array(
				'default' => FALSE,
				'type' => CRM_Utils_Type::T_STRING,
			  ),
			  'status_id' => array('title' => ts('Status'),
				'default' => FALSE,
			  ),
			  'role_id' => array('title' => ts('Role'),
				'default' => FALSE,
			  ),
			  'fee_currency' => array(
				 'required' => TRUE,
				 'no_display' => TRUE,
			  ),
			  'participant_fee_level' => NULL,
			  'participant_fee_amount' => NULL,
			  'participant_register_date' => array('title' => ts('Registration Date')),
			),
			'grouping' => 'event-fields',
			'filters' =>
			array(
			  'event_id' => array('name' => 'event_id',
				'title' => ts('Event'),
				'operatorType' => CRM_Report_Form::OP_SELECT,
				'options' => $this->getEventFilterOptions(),
			  ),
			  'rid' => array(
				'name' => 'role_id',
				'title' => ts('Participant Role'),
				'operatorType' => CRM_Report_Form::OP_MULTISELECT,
				'options' => CRM_Event_PseudoConstant::participantRole(),
				'default' => CRM_Utils_Array::implodePadded(array_keys(CRM_Event_PseudoConstant::participantRole(null, 'v.name = \'Attendee\'')), ','),
			  ),
			),
			'order_bys' =>
			array(
			  'event_id' =>
			  array('title' => ts('Event'), 'default_weight' => '1', 'default_order' => 'ASC'),
			),
		  ),
		   'civicrm_event' =>
		  array(
			'dao' => 'CRM_Event_DAO_Event',
			'fields' => array(
			  'event_type_id' => array('title' => ts('Event Type')),
			  'event_start_date' => array('title' => ts('Event Start Date')),
			),
			'grouping' => 'event-fields',
			'order_bys' =>
			array(
			  'event_type_id' =>
			  array('title' => ts('Event Type'), 'default_weight' => '2', 'default_order' => 'ASC'),
			),
		  ),
		  'civicrm_note' => array (
			'dao' => 'CRM_Core_DAO_Note',
			'fields' => array (
				'note' => array (
					'name' => 'note',
					'title' => ts('Note'),
					'default' => TRUE,
					'dbAlias' => 'GROUP_CONCAT(DISTINCT note_civireport.note ORDER BY note_civireport.modified_date DESC SEPARATOR \' <br>\r\n \')'
				)
			),
			//this filter should go with HAVING and not with where
			 /*'filters' =>
			array(
			  'subject' => array(
				'title' => ts('Onderwerp notitie'),
				'operator' => 'like',
				'default' => 'Deelnemerslijst',
				'type'         => CRM_Utils_Type::T_STRING,
			  ),
			)*/
		  ),
		  'civicrm_group_contact' => array (
			'dao' => 'CRM_Contact_DAO_GroupContact',
		  ),
		  'civicrm_group' => array (
			'dao' => 'CRM_Contact_DAO_Group',
			'fields' => array (
				'title' => array (
					'name' => 'title',
					'title' => ts('Groups'),
					'default' => TRUE,
					'dbAlias' => 'GROUP_CONCAT(DISTINCT group_civireport.title ORDER BY group_civireport.title ASC SEPARATOR \' <br>\r\n \')'
				)
			),
			'grouping' => 'contact-fields',
		  ),
		  'civicrm_participant_count' =>
		  array(
			'alias' => 'civicrm_participant_count',
			'fields' => array(
				'id' => array(
					'title' => ts('Aantal bezochte evenemnten'),
					'default' => TRUE,
					'dbAlias' => 'COUNT(civicrm_participant_count_civireport.id)',
				),
			),
			'grouping' => 'event-fields',
		  ),
		);
		$this->_groupFilter = FALSE;
		$this->_tagFilter = FALSE;
		parent::__construct();
		
		if ($table_name && isset($fields['Organisatie_deelnemer'])) {
			$this->_columns[$table_name]['fields']['custom_'.$fields['Organisatie_deelnemer']['id']]['default'] = TRUE;
			$this->_columns[$table_name]['fields']['custom_'.$fields['Organisatie_deelnemer']['id']]['name'] = 'nick_name';
			
			$this->_columns['civicrm_organisation_address'] = array(
				'dao' => 'CRM_Core_DAO_Address',
				'alias' => 'civicrm_organisation_address',
				'fields' => array(
					'street_address' => NULL,
					'city' => array (
						'title' => ts('City'),
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
			
			$this->_columns['civicrm_organisation_contact'] = array(
				'dao' => 'CRM_Contact_DAO_Contact',
				'alias' => 'civicrm_organisation_contact',
				'fields' => array(
					'display_name' => array (
						'default' => TRUE,
						'no_display' => TRUE,
						'required' => TRUE,
					),
				),
				'grouping' => $this->_columns[$table_name]['grouping'],
			);
		}
		if ($table_name && isset($fields['Functie_deelnemer'])) {
			$this->_columns[$table_name]['fields']['custom_'.$fields['Functie_deelnemer']['id']]['default'] = TRUE;
		}
		
		if ($this->vkw_lidmaatschap_table && isset($this->vkw_lidmaatschap_fields['Soort_VKW_lidmaatschap'])) {
			$this->_columns[$this->vkw_lidmaatschap_table]['fields']['custom_'.$this->vkw_lidmaatschap_fields['Soort_VKW_lidmaatschap']['id']]['default'] = TRUE;
		}
	}
	
	function customDataFrom() {
		parent::customDataFrom();
		if (isset($this->_aliases['civicrm_organisation_address']) && $this->vkw_inschrijving_table && isset($this->vkw_inschrijving_fields['Organisatie_deelnemer'])) {
			$field_alias = $this->_columns[$this->vkw_inschrijving_table]['fields']['custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id']]['alias'];
			$this->_from .= "
				LEFT JOIN civicrm_address {$this->_aliases['civicrm_organisation_address']}
                    ON {$field_alias}.id = {$this->_aliases['civicrm_organisation_address']}.contact_id AND
                       {$this->_aliases['civicrm_organisation_address']}.is_primary = 1
			";
		}
		if (isset($this->_aliases['civicrm_organisation_contact']) && $this->vkw_inschrijving_table && isset($this->vkw_inschrijving_fields['Organisatie_deelnemer'])) {
			$field_alias = $this->_columns[$this->vkw_inschrijving_table]['fields']['custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id']]['alias'];
			$this->_from .= "
				LEFT JOIN civicrm_contact {$this->_aliases['civicrm_organisation_contact']}
                    ON {$field_alias}.id = {$this->_aliases['civicrm_organisation_contact']}.id
			";
		}
	}
  
	function addOrderBys() {
		$this->assign('orderByOptions', array());
	}

  function preProcess() {
    $this->assign('reportTitle', ts('Membership Detail Report'));
    parent::preProcess();
  }

  function select() {
    $select = $this->_columnHeaders = array();

    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('fields', $table)) {
        foreach ($table['fields'] as $fieldName => $field) {
          if (CRM_Utils_Array::value('required', $field) ||
            CRM_Utils_Array::value($fieldName, $this->_params['fields'])
          ) {
            if ($tableName == 'civicrm_address') {
              $this->_addressField = TRUE;
            }
            elseif ($tableName == 'civicrm_email') {
              $this->_emailField = TRUE;
            }
            $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = CRM_Utils_Array::value('title', $field);
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = CRM_Utils_Array::value('type', $field);
          }
        }
      }
    }

    $this->_select = "SELECT " . implode(', ', $select) . " ";
  }

  function from() {
    $this->_from = NULL;

    $this->_from = "
         FROM  civicrm_participant {$this->_aliases['civicrm_participant']}
             LEFT JOIN civicrm_event {$this->_aliases['civicrm_event']}
                    ON ({$this->_aliases['civicrm_event']}.id = {$this->_aliases['civicrm_participant']}.event_id ) AND
                       ({$this->_aliases['civicrm_event']}.is_template IS NULL OR
                        {$this->_aliases['civicrm_event']}.is_template = 0)
			  LEFT JOIN civicrm_contact {$this->_aliases['civicrm_contact']}
					ON ({$this->_aliases['civicrm_participant']}.contact_id  = {$this->_aliases['civicrm_contact']}.id  )
					{$this->_aclFrom}
			  INNER JOIN civicrm_participant {$this->_aliases['civicrm_participant_count']}
				ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_participant_count']}.contact_id  )
              LEFT JOIN civicrm_membership {$this->_aliases['civicrm_membership']}
                          ON {$this->_aliases['civicrm_contact']}.id =
                             {$this->_aliases['civicrm_membership']}.contact_id AND {$this->_aliases['civicrm_membership']}.is_test = 0
               LEFT  JOIN civicrm_membership_status {$this->_aliases['civicrm_membership_status']}
                          ON {$this->_aliases['civicrm_membership_status']}.id =
                             {$this->_aliases['civicrm_membership']}.status_id 
              LEFT JOIN civicrm_note {$this->_aliases['civicrm_note']}
                          ON {$this->_aliases['civicrm_contact']}.id =
                             {$this->_aliases['civicrm_note']}.contact_id
			  LEFT JOIN civicrm_group_contact {$this->_aliases['civicrm_group_contact']}
                          ON {$this->_aliases['civicrm_contact']}.id =
                             {$this->_aliases['civicrm_group_contact']}.contact_id
			  LEFT JOIN civicrm_group {$this->_aliases['civicrm_group']}
                          ON {$this->_aliases['civicrm_group_contact']}.group_id =
                             {$this->_aliases['civicrm_group']}.id
	";
	
    //used when email field is selected
    if ($this->_emailField) {
      $this->_from .= "
              LEFT JOIN civicrm_email {$this->_aliases['civicrm_email']}
                        ON {$this->_aliases['civicrm_contact']}.id =
                           {$this->_aliases['civicrm_email']}.contact_id AND
                           {$this->_aliases['civicrm_email']}.is_primary = 1\n";
    }
  }

  function where() {
    $clauses = array();
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('filters', $table)) {
        foreach ($table['filters'] as $fieldName => $field) {
          $clause = NULL;
          if (CRM_Utils_Array::value('operatorType', $field) & CRM_Utils_Type::T_DATE) {
            $relative = CRM_Utils_Array::value("{$fieldName}_relative", $this->_params);
            $from     = CRM_Utils_Array::value("{$fieldName}_from", $this->_params);
            $to       = CRM_Utils_Array::value("{$fieldName}_to", $this->_params);

            $clause = $this->dateClause($field['name'], $relative, $from, $to, $field['type']);
          }
          else {
            $op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);
            if ($op) {
              $clause = $this->whereClause($field,
                $op,
                CRM_Utils_Array::value("{$fieldName}_value", $this->_params),
                CRM_Utils_Array::value("{$fieldName}_min", $this->_params),
                CRM_Utils_Array::value("{$fieldName}_max", $this->_params)
              );
            }
          }

          if (!empty($clause)) {
            $clauses[] = $clause;
          }
        }
      }
    }

    if (empty($clauses)) {
      $this->_where = "WHERE {$this->_aliases['civicrm_participant']}.is_test = 0 ";
    }
    else {
      $this->_where = "WHERE {$this->_aliases['civicrm_participant']}.is_test = 0 AND " . implode(' AND ', $clauses);
    }

    if ($this->_aclWhere) {
      $this->_where .= " AND {$this->_aclWhere} ";
    }
  }

  function groupBy() {
    $this->_groupBy = " GROUP BY {$this->_aliases['civicrm_contact']}.id, {$this->_aliases['civicrm_membership']}.membership_type_id, {$this->_aliases['civicrm_participant_count']}.contact_id";
  }

  function orderBy() {
    $this->_orderBy = " ORDER BY {$this->_aliases['civicrm_contact']}.sort_name, {$this->_aliases['civicrm_contact']}.id, {$this->_aliases['civicrm_membership']}.membership_type_id";
  }

  function postProcess() {

    $this->beginPostProcess();

    // get the acl clauses built before we assemble the query
    $this->buildACLClause($this->_aliases['civicrm_contact']);
    $sql = $this->buildQuery(TRUE);

    $rows = array();
    $this->buildRows($sql, $rows);

    $this->formatDisplay($rows);
    $this->doTemplateAssignment($rows);
    $this->endPostProcess($rows);
  }

  function alterDisplay(&$rows) {
    // custom code to alter rows
    $entryFound = FALSE;
	
	$eventType = CRM_Core_OptionGroup::values('event_type');
	
    $checkList = array();
    foreach ($rows as $rowNum => $row) {
	
	  if (array_key_exists('civicrm_participant_event_id', $row)) {
        if ($value = $row['civicrm_participant_event_id']) {
          $rows[$rowNum]['civicrm_participant_event_id'] = CRM_Event_PseudoConstant::event($value, FALSE);
        }
        $entryFound = TRUE;
      }
	
	   // handle event type id
      if (array_key_exists('civicrm_event_event_type_id', $row)) {
        if ($value = $row['civicrm_event_event_type_id']) {
          $rows[$rowNum]['civicrm_event_event_type_id'] = $eventType[$value];
        }
        $entryFound = TRUE;
      }

      // handle participant status id
      if (array_key_exists('civicrm_participant_status_id', $row)) {
        if ($value = $row['civicrm_participant_status_id']) {
          $rows[$rowNum]['civicrm_participant_status_id'] = CRM_Event_PseudoConstant::participantStatus($value, FALSE, 'label');
        }
        $entryFound = TRUE;
      }

      // handle participant role id
      if (array_key_exists('civicrm_participant_role_id', $row)) {
        if ($value = $row['civicrm_participant_role_id']) {
          $roles = explode(CRM_Core_DAO::VALUE_SEPARATOR, $value);
          $value = array();
          foreach ($roles as $role) {
            $value[$role] = CRM_Event_PseudoConstant::participantRole($role, FALSE);
          }
          $rows[$rowNum]['civicrm_participant_role_id'] = implode(', ', $value);
        }
        $entryFound = TRUE;
      }

      if (!empty($this->_noRepeats) && $this->_outputMode != 'csv') {
        // not repeat contact display names if it matches with the one
        // in previous row
        $repeatFound = FALSE;
        foreach ($row as $colName => $colVal) {
          if (CRM_Utils_Array::value($colName, $checkList) &&
            is_array($checkList[$colName]) &&
            in_array($colVal, $checkList[$colName])
          ) {
            $rows[$rowNum][$colName] = "";
            $repeatFound = TRUE;
          }
          if (in_array($colName, $this->_noRepeats)) {
            $checkList[$colName][] = $colVal;
          }
        }
      }

      if (array_key_exists('civicrm_membership_membership_type_id', $row)) {
        if ($value = $row['civicrm_membership_membership_type_id']) {
          $rows[$rowNum]['civicrm_membership_membership_type_id'] = CRM_Member_PseudoConstant::membershipType($value, FALSE);
        }
        $entryFound = TRUE;
      }

      if (array_key_exists('civicrm_address_state_province_id', $row)) {
        if ($value = $row['civicrm_address_state_province_id']) {
          $rows[$rowNum]['civicrm_address_state_province_id'] = CRM_Core_PseudoConstant::stateProvince($value, FALSE);
        }
        $entryFound = TRUE;
      }

      if (array_key_exists('civicrm_address_country_id', $row)) {
        if ($value = $row['civicrm_address_country_id']) {
          $rows[$rowNum]['civicrm_address_country_id'] = CRM_Core_PseudoConstant::country($value, FALSE);
        }
        $entryFound = TRUE;
      }

      if (array_key_exists('civicrm_contact_sort_name', $row) &&
        $rows[$rowNum]['civicrm_contact_sort_name'] &&
        array_key_exists('civicrm_contact_id', $row)
      ) {
        $url = CRM_Utils_System::url("civicrm/contact/view",
          'reset=1&cid=' . $row['civicrm_contact_id'],
          $this->_absoluteUrl
        );
        $rows[$rowNum]['civicrm_contact_sort_name_link'] = $url;
        $rows[$rowNum]['civicrm_contact_sort_name_hover'] = ts("View Contact Summary for this Contact.");
        $entryFound = TRUE;
      }
	  
	  if (isset($row[$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id']]) || isset($row['civicrm_organisation_address_city'])) {
		$entryFound = TRUE;
		if (!strlen($rows[$rowNum][$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id']])) {
			$rows[$rowNum][$this->vkw_inschrijving_table.'_custom_'.$this->vkw_inschrijving_fields['Organisatie_deelnemer']['id']] = $rows[$rowNum]['civicrm_organisation_contact_display_name'];
		}
	  }

      if (!$entryFound) {
        break;
      }
    }
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
