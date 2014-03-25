<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'CRM_Deelnemerlijst_Form_Report_UitgebreideDeelnemersLijst',
    'entity' => 'ReportTemplate',
    'params' => 
    array (
      'version' => 3,
      'label' => 'Deelnemerslijst voor organisator',
      'description' => 'Uitgebreide deelnemerslijst (be.vkw.deelnemerlijst) t.b.v organisator',
      'class_name' => 'CRM_Deelnemerlijst_Form_Report_UitgebreideDeelnemersLijst',
      'report_url' => 'be.vkw.deelnemerlijst/uitgebreidedeelnemerslijst',
      'component' => 'CiviEvent',
    ),
  ),
);