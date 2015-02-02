<?php
class Discipline_model extends CI_Model {

	public function listAllDisciplines(){
		$this->db->select('*');
		$this->db->from('discipline');
		$this->db->order_by("discipline_name", "asc");
		$registeredDisciplines = $this->db->get()->result_array();
		
		return $registeredDisciplines;
	}
	
	public function saveNewDiscipline($disciplineToRegister){
		$insertNew = $this->db->insert("discipline", $disciplineToRegister);
		if($insertNew){
			$insertionStatus = TRUE;
		}else{
			$insertionStatus = FALSE;
		}
		return $insertionStatus;
	}
	
	public function updateDisciplineData($disciplineCode,$disciplineToUpdate){
		$disciplineExists = $this->getDisciplineByCode($disciplineCode);
		
		if($disciplineExists){
			//verify is names has changed
			$updatedName = $disciplineToUpdate['discipline_name'];
			$lastName = $disciplineExists['discipline_name'];
			if($updatedName != $lastName){
				$nameAlreadyExists = $this->checkDisciplineNameExists($updatedName);
				if(!$nameAlreadyExists){
					$this->updateDisciplineOnDB($disciplineCode,$disciplineToUpdate);
				}else{
					throw new DisciplineNameException("A disciplina '".$updatedName."' já existe.");
				}
			}else{
				$this->updateDisciplineOnDB($disciplineCode,$disciplineToUpdate);
			}
		}else{
			throw new DisciplineException("Impossível alterar disciplina. O código informado não existe.");
		}
	}
	
	private function updateDisciplineOnDB($disciplineCode,$disciplineToUpdate){
		$this->db->where('discipline_code',$disciplineCode);
		$this->db->update("discipline", $disciplineToUpdate);
	}
	
	public function deleteDiscipline($disciplineCode){
		$disciplineCodeExists = $this->disciplineExists($disciplineCode);
		
		if ($disciplineCodeExists['code']){
			$this->db->delete('discipline', array('discipline_code' => $disciplineCode));
			$disciplinWasDeleted = TRUE;
		}else{
			$disciplinWasDeleted = FALSE;
		}
		
		return $disciplinWasDeleted;
	}

	public function getDisciplineByCode($discipline_code){
		$empty = empty($discipline_code);
	
		if(!$empty){
			$this->db->where('discipline_code',$discipline_code);
			$discipline = $this->db->get('discipline')->row_array();
		}else{
			$discipline = FALSE;
		}
		return $discipline;
	}
	
	private function checkDisciplineNameExists($askedName){
		
		$this->db->select('discipline_name');
		$this->db->from('discipline');
		$this->db->where('discipline_name', $askedName);
		$searchResult = $this->db->get();
		
		if($searchResult->num_rows() > 0){
			$disciplineNameAlreadyExists = TRUE;
		}else{
			$disciplineNameAlreadyExists = FALSE;
		}
		
		return $disciplineNameAlreadyExists;
		
	}
	
	
	public function disciplineExists($disciplineCode=NULL, $disciplineName=NULL){
		if($disciplineCode){
			$this->db->where('discipline_code',$disciplineCode);
			$disciplineCodeExists = $this->db->get('discipline')->row_array();
		}else{
			$disciplineCodeExists = FALSE;
		}
		if($disciplineName){
			$this->db->where('discipline_name',$disciplineName);
			$disciplineNameExists = $this->db->get('discipline')->row_array();
		}else{
			$disciplineNameExists = FALSE;
		}
		
		
		if($disciplineCodeExists && $disciplineNameExists){
			$existsCode = TRUE;
			$existsName = TRUE;
		}else if ($disciplineNameExists){
			$existsName = TRUE;
			$existsCode = FALSE;
		}else if ($disciplineCodeExists){
			$existsCode= TRUE;
			$existsName = FALSE;
		}else{
			$existsCode = FALSE;
			$existsName = FALSE;
		}
		$exists = array('code'=>$existsCode,'name'=>$existsName);
		return $exists;
	}
}