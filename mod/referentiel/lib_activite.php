<?php  // $Id:  lib_activite.php,v 1.0 2012/10/05 00:00:00 jfruitet Exp $
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


// Affiche une entete activite
// *****************************************************************
// *
// output string                                                    *
// *****************************************************************
function referentiel_print_entete_activite_complete(){
// Affiche une entete activite complete
$s='';
	$s.='<table class="activite" width="100%"><tr>'."\n";
	$s.='<tr>'."\n";
	$s.='<th>'.get_string('id','referentiel').'</th>';
	$s.='<th>'.get_string('auteur','referentiel').'</th>';
	$s.='<th>'.get_string('course').'</th>';	
	$s.='<th>'.get_string('type_activite','referentiel').'</th>';
	$s.='<th colspan="2">'.get_string('description','referentiel').'</th>';
	$s.='<th rowspan="3">'.get_string('menu','referentiel').'</th>'."\n";		
	$s.='</tr>'."\n";
	$s.='<tr>';	
	$s.='<th colspan="2">'.get_string('liste_codes_competence','referentiel').'</th>';
	$s.='<th>'.get_string('referent','referentiel').'</th>';
	$s.='<th>'.get_string('validation','referentiel').'</th>';
	$s.='<th>'.get_string('date_modif_student','referentiel').'</th>';	
	$s.='<th>'.get_string('date_modif','referentiel').'</th>';
	$s.='</tr>'."\n";
	$s.='<tr>';
	$s.='<th colspan="3">'.get_string('commentaire','referentiel').'</th>';
	$s.='<td colspan="3" class="yellow" align="center">'.get_string('document','referentiel').'</td>';
	$s.='</tr>'."\n";
	return $s;
}

/************************************************************************
 * takes a list of records, a search string,                            *
 * input @param array $records   of users                               *
 *       @param string $search                                          *
 * output null                                                          *
 ************************************************************************/

// Affiche les activites de ce referentiel
function referentiel_menu_activite($context, $activite_id, $referentiel_instance_id, $approved, $select_acc=0, $mode='updateactivity'){
	global $CFG;
	global $OUTPUT;
	$s="";
	$s.='<tr><td align="center" colspan="7">'."\n";
	$s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?d='.$referentiel_instance_id.'&amp;select_acc='.$select_acc.'&amp;activite_id='.$activite_id.'&amp;mode=listactivityall&amp;old_mode='.$mode.'&amp;sesskey='.sesskey().'#activite"><img src="'.$OUTPUT->pix_url('search','referentiel').'" alt="'.get_string('plus', 'referentiel').'" title="'.get_string('plus', 'referentiel').'" /></a>'."\n";
	
	$has_capability=has_capability('mod/referentiel:approve', $context);
	$is_owner=referentiel_activite_isowner($activite_id);
	
	if ($has_capability	or $is_owner){
		if ($has_capability || ($is_owner && !$approved)) {
	        $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?d='.$referentiel_instance_id.'&amp;select_acc='.$select_acc.'&amp;activite_id='.$activite_id.'&amp;mode=updateactivity&amp;old_mode='.$mode.'&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('t/edit').'" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>'."\n";
		}
    if ($has_capability || ($is_owner && !$approved)) {
		    $s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?d='.$referentiel_instance_id.'&amp;select_acc='.$select_acc.'&amp;activite_id='.$activite_id.'&amp;mode=deleteactivity&amp;old_mode='.$mode.'&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('t/delete').'" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>'."\n";
    	}
	}
	// valider
    if ($has_capability){
		if (!$approved){
			$s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?d='.$referentiel_instance_id.'&amp;select_acc='.$select_acc.'&amp;activite_id='.$activite_id.'&amp;mode=approveactivity&amp;old_mode='.$mode.'&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('nonvalide','referentiel').'" alt="'.get_string('approve', 'referentiel').'" title="'.get_string('approve', 'referentiel').'" /></a>'."\n";
		}
		else{
    		$s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?d='.$referentiel_instance_id.'&amp;select_acc='.$select_acc.'&amp;activite_id='.$activite_id.'&amp;mode=desapproveactivity&amp;old_mode='.$mode.'&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('valide','referentiel').'" alt="'.get_string('desapprove', 'referentiel').'" title="'.get_string('desapprove', 'referentiel').'" /></a>'."\n";
		}
	}
	// commentaires
    if (has_capability('mod/referentiel:comment', $context)){
    	$s.='&nbsp; <a href="'.$CFG->wwwroot.'/mod/referentiel/activite.php?d='.$referentiel_instance_id.'&amp;select_acc='.$select_acc.'&amp;activite_id='.$activite_id.'&amp;mode=commentactivity&amp;old_mode='.$mode.'&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('feedback','referentiel').'" alt="'.get_string('comment', 'referentiel').'" title="'.get_string('comment', 'referentiel').'" /></a>'."\n";
	}
	$s.='</td></tr>'."\n";
	return $s;
}



function referentiel_select_users_activite($record_users, $userid=0, $mode='listactivity', $select_acc=0, $data_f=NULL){
global $cm;
global $course;
$maxcol=MAXBOITESSELECTION;
$s="";
	if ($record_users){
		
		$s.='<div align="center">
		
<form name="form" method="post" action="activite.php?id='.$cm->id.'&amp;action=selectuser">'."\n"; 
		$s.='<table class="selection">'."\n";
		$s.='<tr>';
		$s.='<td>';
		if (($userid=='') || ($userid==0)){
			$s.='<input type="radio" name="userid" id="userid" value="" checked="checked" />'.get_string('tous', 'referentiel').'</td>'."\n";;
		}
		else{
			$s.='<input type="radio" name="userid" id="userid" value="" />'.get_string('tous', 'referentiel').'</td>'."\n";;
		}
		$s.='</tr>';
		$s.='<tr>';
		
		$col=0;
		$lig=0;
		foreach ($record_users as $record_u) {   // liste d'id users
			$user_info=referentiel_get_user_info($record_u->userid);
			if ($record_u->userid==$userid){
				$s.='<td><input type="radio" name="userid" id="userid" value="'.$record_u->userid.'" checked="checked" />'.$user_info.'</td>'."\n";;
			}
			else{
				$s.='<td><input type="radio" name="userid" id="userid" value="'.$record_u->userid.'" />'.$user_info.'</td>'."\n";;
			}
			if ($col<$maxcol){
				$col++;
			}
			else{
				$s.='</tr><tr>'."\n";
				$col=0;
				$lig++;
			}
		}
		if ($lig>0){
			while ($col<$maxcol){
				$s.='<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>'."\n";
				$col++;
			}
		}
			
		$s.='<td>&nbsp; &nbsp; &nbsp; <input type="submit" value="'.get_string('select', 'referentiel').'" /></td>';
		if (!empty($data_f)){
            $s.='
// Filtres
<input type="hidden" name="f_auteur" value="'.$data_f->f_auteur.'" />
<input type="hidden" name="f_validation" value="'.$data_f->f_validation.'" />
<input type="hidden" name="f_referent" value="'.$data_f->f_referent.'" />
<input type="hidden" name="f_date_modif" value="'.$data_f->f_date_modif.'" />
<input type="hidden" name="f_date_modif_student" value="'.$data_f->f_date_modif_student.'" />
';
        }
		$s.='
<input type="hidden" name="select_acc" value="'.$select_acc.'" />
<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="'.$course->id.'" />
<input type="hidden" name="sesskey"     value="'.sesskey().'" />
<input type="hidden" name="mode"          value="'.$mode.'" />
</tr></table>
</form>
</div>'."\n";
	}
	return $s;
}


// Affiche une entete activite
// *****************************************************************
// *
// output string                                                    *
// *****************************************************************

function referentiel_print_entete_activite(){
// Affiche une entete activite
$s='';
	$s.='<table class="activite" width="100%">'."\n";
	$s.='<tr>';
	$s.='<th>'.get_string('id','referentiel').'</th>';
	$s.='<th>'.get_string('auteur','referentiel').'</th>';
	$s.='<th>'.get_string('course').'</th>';	
	$s.='<th>'.get_string('type_activite','referentiel').'</th>';
	$s.='<th>'.get_string('liste_codes_competence','referentiel').'</th>';
	$s.='<th>'.get_string('referent','referentiel').'</th>';
	$s.='<th>'.get_string('validation','referentiel').'</th>';
	$s.='<th>'.get_string('date_modif_student','referentiel').'</th>';	
	$s.='<th>'.get_string('date_modif','referentiel').'</th>';
	$s.='<th>&nbsp;</th>';	
	$s.='</tr>'."\n";
	return $s;
}

// Affiche une activite et les documents associés
// *****************************************************************
// input @param a $record_a   of activite                          *
// output null                                                     *
// *****************************************************************

function referentiel_print_activite($record_a, $context){
global $CFG;
$s="";
	if ($record_a){
		$activite_id=$record_a->id;
		$type_activite = stripslashes($record_a->type_activite);
		$description_activite = stripslashes($record_a->description_activite);
		$competences_activite = $record_a->competences_activite;
		$commentaire_activite = stripslashes($record_a->commentaire_activite);
		$ref_instance = $record_a->ref_instance;
		$ref_referentiel = $record_a->ref_referentiel;
		$ref_course = $record_a->ref_course;
		$userid = $record_a->userid;
		$teacherid = $record_a->teacherid;
		$date_creation = $record_a->date_creation;
		$date_modif_student = $record_a->date_modif_student;
		$date_modif = $record_a->date_modif;
		
        $prioritaire=referentiel_activite_prioritaire($record_a);
		$approved = $record_a->approved;
		$ref_task = $record_a->ref_task;

        $user_info=referentiel_get_user_info($userid);
		$teacher_info=referentiel_get_user_info($teacherid);

		// dates
		if ($date_creation!=0){
			$date_creation_info=userdate($date_creation);
		}
		else{
			$date_creation_info='';
		}

		if ($date_modif!=0){
			$date_modif_info=userdate($date_modif);
		}
		else{
			$date_modif_info='';
		}

		if ($date_modif_student==0){
			$date_modif_student=$date_creation;
		}
		if ($date_modif_student!=0){
			$date_modif_student_info=userdate($date_modif_student);
		}
		else{
			$date_modif_student_info='';
		}


		$s.='<tr>';
        if (!empty($prioritaire)){
            $s.='<td class="prioritaire">';
        }
        else if (isset($approved) && ($approved)){
			$s.= '<td class="valide">';
		}
		else{
			$s.= '<td class="invalide">';
		}

		$s.= $activite_id;
		$s.='</td><td>';
		$s.=$user_info;
		// MODIF JF 2012/05/06
        $s.= referentiel_liste_groupes_user($ref_course, $userid);
		$s.='</td><td>';
		$s.=$type_activite;
		// Modif JF 06/10/2010
		if ($ref_task){
            // consignes associées à une tâche
            $titre_task=referentiel_get_theme_task($ref_task);
            $info_task=referentiel_get_content_task($ref_task);
            if ($info_task!=''){
                // lien vers la tâche
                $s.='<br />'.referentiel_affiche_overlib_texte($titre_task, $info_task);
            }
            // documents associés à une tâche
            echo referentiel_print_liste_documents_task($ref_task, referentiel_get_auteur_task($ref_task), $context);
        }
/*
		p($type_activite);
		$s.=nl2br($description_activite);
*/

		if (isset($approved) && ($approved)){
			$s.='</td><td class="valide">';
		}
		else{
			$s.='</td><td class="invalide">';
		}
		$s.=referentiel_affiche_liste_codes_competence('/',$competences_activite, $ref_referentiel);
		
		//Modif bareme	
			if ($CFG->referentiel_use_scale){
				require_once('lib_bareme.php');
				if ($rec_assoc=referentiel_get_assoc_bareme_occurrence($ref_referentiel)){
					if ($bareme=referentiel_get_bareme($rec_assoc->refscaleid)){
						if ($competences_bareme=referentiel_get_competences_activite($activite_id, $bareme->id)){
							$s1.='<br /><span class="bold">'.get_string('evaluation','referentiel').'</span> '.referentiel_affiche_bareme_activite($competences_bareme, $bareme);
						}
					}
				}									
			}
		$s.='</td><td>';
		$s.=$teacher_info;
		$s.='</td><td>';
		if (isset($approved) && ($approved)){
			$s.=get_string('approved','referentiel');
		}
		else{
			$s.=get_string('not_approved','referentiel');	
		}
		$s.='</td><td>';
		$s.='<span class="small">'.$date_modif_info.'</span>';
		$s.='</td></tr>'."\n";
	}
	return $s;
}



/**************************************************************************
 * takes a the current referentiel, an user id                            *
 * input                                                                  *
 *       @param object $referentiel_instance                              *
 *       @param int $userid                                               *
 * output true                                                            *
 **************************************************************************/

function referentiel_print_liste_activites_user($referentiel_instance, $userid, $sql_filtre_where='', $sql_filtre_order='', $select_acc=0, $data_f=NULL) {
global $CFG;
global $DB;
global $USER;
static $referentiel_id = NULL;
global $appli;


	// contexte
    $cm = get_coursemodule_from_instance('referentiel', $referentiel_instance->id);
    $course = $DB->get_record("course", array("id" => "$cm->course"));

    if (empty($cm) or empty($course)){
        print_print_error('REFERENTIEL_print_error 5 :: print_lib_activite.php :: You cannot call this script in that way');
	}
	
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

	$records = array();
	$referentiel_id = $referentiel_instance->ref_referentiel;

    $roles=referentiel_roles_in_instance($referentiel_instance->id);
    $iseditor=$roles->is_editor;
    $isadmin=$roles->is_admin;
    $isteacher=$roles->is_teacher;
    $istutor=$roles->is_tutor;
    $isstudent=$roles->is_student;
    $isguest=$roles->is_guest;
	if (isset($referentiel_id) && ($referentiel_id>0)){
		$referentiel_referentiel=referentiel_get_referentiel_referentiel($referentiel_id);
		if (!$referentiel_referentiel){
			if ($iseditor){
				print_print_error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/mod/referentiel/edit.php?d=$referentiel_instance->id&amp;mode=editreferentiel&amp;sesskey=".sesskey());
			}
			else {
				print_print_error(get_string('creer_referentiel','referentiel'), "$CFG->wwwroot/course/view.php?id=$course->id&amp;sesskey=".sesskey());
			}
		}
	 	// preparer les variables globales pour Overlib
		// referentiel_initialise_data_referentiel($referentiel_referentiel->id);

		if (isset($userid) && ($userid==$USER->id)){ 
			$records=referentiel_get_all_activites_user($referentiel_instance->ref_referentiel, $record_id->userid, $sql_filtre_where, $sql_filtre_order);
		}
		else{
			$records=referentiel_get_all_activites_user_course($referentiel_instance->ref_referentiel, $record_id->userid, $course->id);
		}
		if ($records){
			foreach ($records as $record) {   
				// Afficher 	
				referentiel_print_activite_detail($record);
			}
		}
		else{
			echo referentiel_print_aucune_activite_user($record_id->userid);
		}
	}
	return true;
}


?>