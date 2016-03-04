<?php

class Permissions_model extends CI_Model {

    /**
      * Search on database for the permissions of a group
      * @param $groupId - The group id to get the permissions
      * @return an array with the permissions of the given group
      */
    public function getGroupPermissions($groupId){

        $this->db->select('permission.*');
        $this->db->from("permission");
        $this->db->join("group_permission", "permission.id_permission = group_permission.id_permission");
        $this->db->where("group_permission.id_group", $groupId);
        $groupPermissions = $this->db->get()->result_array();

        $groupPermissions = checkArray($groupPermissions);

        return $groupPermissions;
    }
}