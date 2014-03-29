<?php


    $f_validation = optional_param('f_validation', 0, PARAM_INT);
    $f_referent = optional_param('f_referent', 0, PARAM_INT);
    $f_date_modif = optional_param('f_date_modif', 0, PARAM_INT);
    $f_date_modif_student = optional_param('f_date_modif_student', 0, PARAM_INT);
    $f_auteur = optional_param('f_auteur', 0, PARAM_INT);

    $f_valide = optional_param('f_valide', 0, PARAM_INT);
    $f_verrou = optional_param('f_verrou', 0, PARAM_INT);
    $f_date_decision = optional_param('f_date_decision', 0, PARAM_INT);
    
    $sql_f_where=optional_param('sql_f_where','', PARAM_ALPHA);
    $sql_f_order=optional_param('sql_f_order','', PARAM_ALPHA);
    $sql_f_user=optional_param('sql_f_user','', PARAM_ALPHA);

	$data_f= new Object(); // parametres de filtrage
	
	// Activites
	if (isset($f_validation)){
			$data_f->f_validation=$f_validation;
	}
	else {
		$data_f->f_validation=0;
	}
	if (isset($f_referent)){
		$data_f->f_referent=$f_referent;
	}
	else{
		$data_f->f_referent=0;
	}
	if (isset($f_date_modif_student)){
		$data_f->f_date_modif_student=$f_date_modif_student;
	}
	else{
		$data_f->f_date_modif_student=0;
	}
	if (isset($f_date_modif)){
		$data_f->f_date_modif=$f_date_modif;
	}
	else{
		$data_f->f_date_modif=0;
	}
	if (isset($f_auteur)){
		$data_f->f_auteur=$f_auteur;
	}
	else{
		$data_f->f_auteur=0;
	}
	
// Certificat
    if (isset($f_valide)){
		$data_f->f_valide=$f_valide;
	}
	else {
		$data_f->f_valide=0;
	}
	if (isset($f_verrou)){
			$data_f->f_verrou=$f_verrou;
	}
	else {
		$data_f->f_verrou=0;
	}
	if (isset($f_date_decision)){
		$data_f->f_date_decision=$f_date_decision;
	}
	else{
		$data_f->f_date_decision=0;
	}


//-----------------
function set_filtres_sql($type=''){
    global $data_f;
	global $sql_f_where;
	global $sql_f_order;
	//echo "<br />DEBUG :: filtres.php :: Ligne 78 :: TYPE=$type\n";
	//print_object( $data_f);

	if ($type=='certificat'){
		if (isset($data_f->f_valide) && ($data_f->f_valide=='1')){
			$sql_f_where.=' AND (valide=1) ';
		}
		else if (isset($data_f->f_valide) && ($data_f->f_valide=='-1')){
			$sql_f_where.=' AND (valide=0) ';
		}

		if (isset($data_f->f_verrou) && ($data_f->f_verrou=='1')){
			$sql_f_where.=' AND (verrou=1) ';
		}
		else if (isset($data_f->f_verrou) && ($data_f->f_verrou=='-1')){
			$sql_f_where.=' AND (verrou=0) ';
		}

		if (isset($data_f->f_date_decision) && ($data_f->f_date_decision=='1')){
			if ($sql_f_order!='')
				$sql_f_order.=', date_decision ASC ';
			else
				$sql_f_order.=' date_decision ASC ';
		}
		else if (isset($data_f->f_date_decision) && ($data_f->f_date_decision=='-1')){
			if ($sql_f_order!='')
				$sql_f_order.=', date_decision DESC ';
			else
				$sql_f_order.=' date_decision DESC ';
		}

		if (isset($data_f->f_auteur) && ($data_f->f_auteur=='1')){
			if ($sql_f_order!='')
				$sql_f_order.=', userid ASC ';
			else
				$sql_f_order.=' userid ASC ';
		}
		else if (isset($data_f->f_auteur) && ($data_f->f_auteur=='-1')){
			if ($sql_f_order!='')
				$sql_f_order.=', userid DESC ';
			else
				$sql_f_order.=' userid DESC ';
		}

	}
	else{
		if (isset($data_f->f_validation) && ($data_f->f_validation=='1')){
			$sql_f_where.=' AND (approved=1) ';
		}
		else if (isset($data_f->f_validation) && ($data_f->f_validation=='-1')){
			$sql_f_where.=' AND (approved=0) ';
		}

		if (isset($data_f->f_referent) && ($data_f->f_referent=='1')){
			$sql_f_where.=' AND ((date_modif_student>date_modif) AND (approved=0))  ';
		}
		else if (isset($data_f->f_referent) && ($data_f->f_referent=='-1')){
			$sql_f_where.=' AND (date_modif>=date_modif_student)  ';
		}

		if (isset($data_f->f_date_modif) && ($data_f->f_date_modif=='1')){
			if ($sql_f_order!='')
				$sql_f_order.=', date_modif ASC ';
			else
				$sql_f_order.=' date_modif ASC ';
		}
		else if (isset($data_f->f_date_modif) && ($data_f->f_date_modif=='-1')){
			if ($sql_f_order!='')
				$sql_f_order.=', date_modif DESC ';
			else
				$sql_f_order.=' date_modif DESC ';
		}

		if (isset($data_f->f_date_modif_student) && ($data_f->f_date_modif_student=='1')){
			if ($sql_f_order!='')
				$sql_f_order.=', date_modif_student ASC ';
			else
				$sql_f_order.=' date_modif_student ASC ';
		}
		else if (isset($data_f->f_date_modif_student) && ($data_f->f_date_modif_student=='-1')){
			if ($sql_f_order!='')
				$sql_f_order.=', date_modif_student DESC ';
			else
				$sql_f_order.=' date_modif_student DESC ';
		}

		if (isset($data_f->f_auteur) && ($data_f->f_auteur=='1')){
			if ($sql_f_order!='')
				$sql_f_order.=', userid ASC ';
			else
				$sql_f_order.=' userid ASC ';
		}
		else if (isset($data_f->f_auteur) && ($data_f->f_auteur=='-1')){
			if ($sql_f_order!='')
				$sql_f_order.=', userid DESC ';
			else
				$sql_f_order.=' userid DESC ';
		}
	}
	//echo "<br />DEBUG :: filtres.php :: Ligne 198 :: FILTRES : WHERE=".htmlentities($sql_f_where)."<br />ORDER=".htmlentities($sql_f_order)."\n";
}

?>
