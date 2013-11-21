<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'CRM_Deelnemerlijst_Form_Report_DeelnemerlijstBase',
    'entity' => 'ReportTemplate',
    'params' => 
    array (
      'version' => 3,
      'label' => 'DeelnemerlijstBase',
      'description' => 'DeelnemerlijstBase (be.vkw.deelnemerlijst)',
      'class_name' => 'CRM_Deelnemerlijst_Form_Report_DeelnemerlijstBase',
      'report_url' => 'be.vkw.deelnemerlijst/deelnemerlijstbase',
      'component' => 'CiviEvent',
    ),
  ),
);