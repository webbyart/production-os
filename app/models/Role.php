<?php
/**
 * Role Model
 */

class Role extends BaseModel
{
    protected $table = 'roles';
    protected $fillable = ['role_name', 'description', 'is_active'];

    protected function getPrimaryKey()
    {
        return 'role_id';
    }

    /**
     * Get role with permissions
     */
    public function getRoleWithPermissions($roleId)
    {
        $query = "SELECT r.*, p.permission_id, p.permission_name, p.module, p.action
                  FROM {$this->table} r
                  LEFT JOIN role_permissions rp ON r.role_id = rp.role_id
                  LEFT JOIN permissions p ON rp.permission_id = p.permission_id
                  WHERE r.role_id = ?";
        
        $this->db->prepare($query);
        $this->db->execute([$roleId]);
        return $this->db->fetchAll();
    }

    /**
     * Get active roles
     */
    public function getActive()
    {
        $query = "SELECT * FROM {$this->table} WHERE is_active = TRUE ORDER BY role_name";
        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAll();
    }
}
