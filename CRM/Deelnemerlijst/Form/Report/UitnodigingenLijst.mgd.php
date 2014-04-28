<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'CRM_Deelnemerlijst_Form_Report_UitnodigingenLijst',
    'entity' => 'ReportTemplate',
    'params' => 
    array (
      'version' => 3,
      'label' => 'Uitnodigingen lijst (e-mailadressen)',
      'description' => 'Uitnodigingen lijst (be.vkw.deelnemerlijst)',
      'class_name' => 'CRM_Deelnemerlijst_Form_Report_UitnodigingenLijst',
      'report_url' => 'be.vkw.deelnemerlijst/uitnodigingenlijst',
      'component' => 'CiviEvent',
    ),
  ),
);