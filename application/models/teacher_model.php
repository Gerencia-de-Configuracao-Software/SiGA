<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacher_model extends CI_Model {

	const TABLE_NAME = "teacher_profile";
	const SUMMARY_COLUMN = "summary";
	const ID_COLUMN = "id_user";
	const LATTES_COLUMN = "lattes_link";

	public function updateProfile($teacherData){

		$id = array(
			self::ID_COLUMN => $teacherData[self::ID_COLUMN]
		);

		$alreadyExists = $this->verifyIfGetProfile($id);
		
		if($alreadyExists){
			$wasUpdated = $this->editTeacherProfile($teacherData);
		}
		
		else{
			$wasUpdated = $this->insertTeacherProfile($teacherData);
		}
		
		return $wasUpdated;
	}

	public function editTeacherProfile($teacherData){

		$id = $teacherData[self::ID_COLUMN];
		$summary = $teacherData[self::SUMMARY_COLUMN];
		$lattes = $teacherData[self::LATTES_COLUMN];

		$this->db->where(self::ID_COLUMN, $id);
		$this->db->update(self::TABLE_NAME, array(
												self::SUMMARY_COLUMN => $summary,
												self::LATTES_COLUMN => $lattes
											));

		$wasUpdated = $this->verifyIfGetProfile($teacherData);
		return $wasUpdated;

	}


	public function insertTeacherProfile($data){

		$this->db->insert(self::TABLE_NAME, $data);

		$wasSaved = $this->verifyIfGetProfile($data);

		return $wasSaved;
	}

	public function getTeacherProfile($teacherToSearch){

		$foundTeacher = $this->db->get_where(self::TABLE_NAME, $teacherToSearch)->row_array();
		$foundTeacher = checkArray($foundTeacher);

		return $foundTeacher;

	}

	public function verifyIfGetProfile($teacherData){
		$foundTeacher = $this->getTeacherProfile($teacherData);

		if($foundTeacher !== FALSE && !empty($foundTeacher)){
			$foundProfile = TRUE;
		}
		else{
			$foundProfile = FALSE;
		}

		return $foundProfile;

	}

}