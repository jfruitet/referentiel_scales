<?php  // $Id: import_pedagogie..php,v 0.1 2011/03/07/ 00:00:00 jfruitet Exp $
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2005 Martin Dougiamas  http://dougiamas.com             //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
* Importation d'un fichier de pedagogie
* D'apres import_pedagogie.php
*
* @package referentiel
*/

    require_once('../../config.php');
    require_once('class/import_form.php'); // formulaires de choix de fichiers
    require_once("$CFG->dirroot/repository/lib.php"); // Moodle 2.0
    require_once('import_export_lib.php');	// IMPORT / EXPORT
    require_once('locallib.php');
	require_once('lib_pedagogie.php');

    $id    = optional_param('id', 0, PARAM_INT);    // course module id
    $d     = optional_param('d', 0, PARAM_INT);    // referentiel base id
    $mode  = optional_param('mode','', PARAM_ALPHANUMEXT);
    $format = optional_param('format','', PARAM_FILE );
    $courseid = optional_param('courseid', 0, PARAM_INT);
    $select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/import_pedagogie.php');

	if ($d) {     // referentiel_referentiel_id
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$d"))) {
            print_error('Referentiel instance is incorrect');
        }
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
            print_error('Réferentiel id is incorrect');
        }

		if (! $course = $DB->get_record("course", array("id" => "$referentiel->course"))) {
	            print_error('Course is misconfigured');
    	}

		if (! $cm = get_coursemodule_from_instance('referentiel', $referentiel->id, $course->id)) {
    	        print_error('Course Module ID is incorrect');
		}
		$url->param('d', $d);
	}
	elseif ($id) {
        if (! $cm = get_coursemodule_from_id('referentiel', $id)) {
        	print_error('Course Module ID was incorrect');
        }
        if (! $course = $DB->get_record("course", array("id" => "$cm->course"))) {
            print_error('Course is misconfigured');
        }
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$cm->instance"))) {
            print_error('Referentiel instance is incorrect');
        }
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
            print_error('Referentiel is incorrect');
        }
        $url->param('id', $id);
    }
	else{
		print_error(get_string('erreurscript','referentiel','Erreur01 : import_pedagogie.php'));
	}


    require_login($course->id, false, $cm);   // pas d'autologin guest

    if (!isloggedin() or isguestuser()) {
        redirect($CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
    }

    // check role capability
    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}

    require_capability('mod/referentiel:import', $context);

        /// If it's hidden then it's don't show anything.  :)
	/// Some capability checks.
    if (empty($cm->visible)
    && (
        !has_capability('moodle/course:viewhiddenactivities', $context)
            &&
        !has_capability('mod/referentiel:managecomments', $context)
        )

    ) {
        print_error(get_string("activityiscurrentlyhidden"),'error',"$CFG->wwwroot/course/view.php?id=$course->id");
    }

    // ensure the files area exists for this course
    // make_upload_directory( "$course->id/$CFG->moddata/referentiel" );


    if ($usehtmleditor = can_use_html_editor()) {
    	$defaultformat = FORMAT_HTML;
        $editorfields = '';
    }
    else {
    	$defaultformat = FORMAT_MOODLE;
    }

    /// Print the tabs
    if (empty($mode)){
		$mode='importpedago'; // un seul mode possible
	}

	$currenttab = $mode;

    if ($referentiel->id) {
    	$editentry = true;  //used in tabs
    }


    $url->param('mode', $mode);
    $action='importpedagogies'; // une seule action possible
    $url->param('action', $action);

    // AFFICHAGE DE LA PAGE Moodle 2
	$strreferentiels = get_string('modulenameplural','referentiel');
	$strreferentiel = get_string('referentiel','referentiel');
	$strmessage = get_string('importpedagogies','referentiel');
    $strpagename = get_string('importpedagogies', 'referentiel');

    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    $PAGE->set_url($url);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    $PAGE->set_title($pagetitle);
    $PAGE->navbar->add($strpagename);
    $PAGE->set_heading($course->fullname);

    // formulaire de saisie d'un fichier
    $fileformatnames = referentiel_get_import_export_formats( 'import', 'pformat' );
    $options = array('subdirs'=>0, 'maxbytes'=>get_max_upload_file_size($CFG->maxbytes, $course->maxbytes, 0), 'maxfiles'=>1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);
    $mform = new referentiel_import_form(null, array('d'=>$referentiel->id, 'contextid'=>$context->id, 'filearea'=>'pedagogie', 'fileformats' => $fileformatnames, 'stoponerror' => 1, 'deleteall'=>0, 'msg' =>  get_string('importpedagogies', 'referentiel'),  'options'=>$options));

    // recuperer le fichier charge
    if ($mform->is_cancelled()) {
        // redirect(new moodle_url('/course/view.php', array('id'=>$course->id)));
        redirect(new moodle_url('/mod/referentiel/pedagogie.php', array('id'=>$cm->id, 'mode' => 'listpedago', 'select_acc' => 0)));
    }
    else if ($mform->get_data()) {
        $returnlink=new moodle_url('/mod/referentiel/pedagogie.php', array('id'=>$cm->id, 'mode' => 'listpedago', 'select_acc' => 0));

        if ($formdata = $mform->get_data()) {
            // DEBUG
            // echo "<br />DEBUG :: import_instance.php :: 193 :: FORMDATA\n";
            // print_object($formdata);

            // Vider
            if (isset($formdata->deleteall) && ($formdata->deleteall!=0)){
    			if (referentiel_vider_pedagos_assos()){
	       			add_to_log($course->id, 'referentiel', 'pedagos deleted', "import_pedagogie.php?d=$referentiel->id", $cm->id);
                    notify(get_string('recorddeleted','referentiel'), 'notifysuccess');
                }
            }
            //
            $fileareas = array('referentiel', 'document', 'consigne', 'activite', 'task', 'certificat', 'scolarite', 'pedagogie');
            if (!empty($formdata->filearea) && ($formdata->filearea == 'pedagogie')) {

                $fs = get_file_storage();
                // suppression du fichier existant ?   NON
                // $fs->delete_area_files($formdata->contextid, 'mod_referentiel', $formdata->filearea, 0);

                if ($newfilename= $mform->get_new_filename('referentiel_file')) {

				    echo $OUTPUT->header();
                    $contents = $mform->get_file_content('referentiel_file');
                    if (!empty($contents)){

                        $format=$formdata->format;

                        // echo "<br />DEBUG :: 197 :: $format<br />\n";

                        if (! is_readable("format/$format/format.php")) {
                            print_error( get_string('formatnotfound','referentiel', $format) );
                        }

                        require_once("format.php");  // Parent class
                        require_once("format/$format/format.php");
                        $classname = "pformat_$format";
                        // echo "<br />DEBUG :: 232 :: $classname<br />\n";

                        $pformat = new $classname();
                        // load data into class
                        $pformat->setIReferentiel( $referentiel ); // instance
                        // $pformat->setRefRerentiel( $referentiel->ref_referentiel );
                        $pformat->setRReferentiel( $referentiel_referentiel ); // referenteil_referentiel
                        $pformat->setCourse( $course );
                        $pformat->setContext( $context );
                        $pformat->setCoursemodule( $cm);
                        $pformat->setContents( $contents );
                        $pformat->setStoponerror( $formdata->stoponerror );

                        // Do anything before that we need to
                        if (! $pformat->importpreprocess()) {
                            print_error( get_string('importerror','referentiel') , $returnlink);
                        }

                        // Process the uploaded file
                        if (! $pformat->importprocess() ) {
                            print_error( get_string('importerror','referentiel') , $returnlink);
                        }

                        // In case anything needs to be done after
                        if (! $pformat->importpostprocess()) {
                            print_error( get_string('importerror','referentiel') , $returnlink);
                        }

                        echo "<hr />";
                        if (isset($pformat->returnpage) && ($pformat->returnpage!="")){
                            print_continue($pformat->returnpage);
                        }
                        else{
                            print_continue($CFG->wwwroot.'/mod/referentiel/pedagogie.php?id='.$cm->id);
                        }
                    }
                    else{
                        print_error( get_string('cannotread','referentiel') );
                    }

                    echo $OUTPUT->footer();
                    die();
                }
            }
        }
        redirect($returnlink);
    }

    // afficher la page
    echo $OUTPUT->header();

    require_once('onglets.php'); // menus sous forme d'onglets
    $tab_onglets = new Onglets($context, $referentiel, $referentiel_referentiel, $cm, $course, $currenttab, $select_acc, NULL, $mode);
    $tab_onglets->display();

    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title=""  alt="" /> '.$strmessage.' '.$OUTPUT->help_icon('importpedagoh','referentiel').'</h2></div>'."\n";

    // boite de saisie
    echo $OUTPUT->box_start('generalbox');
    $mform->display();
    echo $OUTPUT->box_end();

    echo $OUTPUT->footer();
    die();


?>
