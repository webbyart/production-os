<?php
/**
 * Unit Model
 */

class Unit extends BaseModel
{
    protected $table = 'units';
    protected $fillable = ['unit_name', 'unit_symbol', 'description'];

    protected function getPrimaryKey()
    {
        return 'unit_id';
    }

    /**
     * Get all units
     */
    public function getAll()
    {
        $query = "SELECT * FROM {$this->table} ORDER BY unit_name ASC";
        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAll();
    }
}
