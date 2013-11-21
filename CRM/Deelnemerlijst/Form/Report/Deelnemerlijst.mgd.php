<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'CRM_Deelnemerlijst_Form_Report_Deelnemerlijst',
    'entity' => 'ReportTemplate',
    'params' => 
    array (
      'version' => 3,
      'label' => 'Deelnemerlijst',
      'description' => 'Deelnemerlijst (be.vkw.deelnemerlijst)',
      'class_name' => 'CRM_Deelnemerlijst_Form_Report_Deelnemerlijst',
      'report_url' => 'be.vkw.deelnemerlijst/deelnemerlijst',
      'component' => 'CiviEvent',
    ),
  ),
);