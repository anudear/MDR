<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Handles uploading files
 *
 * @package    local_hospitalreport
 * @copyright  Mallamma<mallamma@elearn10.com>
 * @copyright  Dhruv Infoline Pvt Ltd <lmsofindia.com>
 * @license    http://www.lmsofindia.com 2017 or later
 */

require_once('../../config.php');
require_once('form/coursecompletionrpt_form.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');
global $DB;
defined('MOODLE_INTERNAL') || die();
require_login();
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url($CFG->wwwroot . '/local/hospitalreport/coursecompletionrpt.php');
$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/hospitalreport/js/custom.js'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/hospitalreport/js/jquery.dataTables.min.js'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/hospitalreport/js/dataTables.buttons.min.js'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/hospitalreport/js/jszip.min.js'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/hospitalreport/js/pdfmake.min.js'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/hospitalreport/js/vfs_fonts.js'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/hospitalreport/js/buttons.html5.min.js'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/hospitalreport/js/buttons.print.min.js'),true);
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/local/hospitalreport/css/buttons.dataTables.min.css'),true);
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/local/hospitalreport/css/jquery.dataTables.min.css'),true);
//Instantiate departmentreport_form 
$mform = new coursecompletionrpt_form();
//variables initialisation.
//Form processing and displaying is done here
if ($mform->is_cancelled()) {
//Handle form cancel operation, if cancel button is present on form
} else if ($data = $mform->get_data()) {
  // print_object($data);die;
  $status = 0;
  $cid = $data->course;
  $startdate = $data->fromdate;
  $enddate = $data->todate;
  $employeegroup = $data->employeegroup;
  if(!empty($data->status)){
      $status = $data->status;
  }

  //calling course completion report table function

  $coursereport = local_coursecompletion($cid,$startdate,$enddate,$employeegroup,$status);

if($data->coursename){
        $htmlp ='';
        $htmlp.=html_writer::start_div('container');
        $htmlp.=html_writer::start_div('row');
        $htmlp.=html_writer::start_div('col-md-12');
        $htmlp.=html_writer::start_tag('a',array('class'=>'btn btn-lg btn-primary','href'=>$CFG->wwwroot.'/local/hospitalreport/coursecompletionrptpdf.php?id='.$cid.'&fromdate='.$startdate.'&todate='.$enddate.'&employeegroup='.$employeegroup.'&$status='.$status.''));
        $htmlp.=get_string('pdf','local_hospitalreport');
        $htmlp.=html_writer::end_tag('a');
        $htmlp.=html_writer::end_div();
        $htmlp.=html_writer::end_div();
        $htmlp.=html_writer::end_div();
} 
}
else {

}
echo $OUTPUT->header();
//displaying the form here.
$mform->display();
//checking if the courserpttable is empty or not.
  if(!empty($coursereport)){
  //report table containing all the course userdetails.
  $html='';
  $html.=html_writer::start_div('container');
  $html.=html_writer::start_div('col-md-12');
  $html.=html_writer::start_div('row');
  $html.=html_writer::table($coursereport);
  $html.="<script>$(document).ready(function() {
    $('#coursecompltbl').DataTable( {
        dom: 'Bfrtip',
        buttons: [
          'excel'
        ]
    } );
} );</script>";
  $html.=html_writer::end_div();
  $html.=html_writer::end_div();
  $html.=html_writer::end_div();
  echo $html;
  echo $htmlp;
}
echo $OUTPUT->footer();