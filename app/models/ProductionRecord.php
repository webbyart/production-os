<?php
/**
 * ProductionRecord Model
 */

class ProductionRecord extends BaseModel
{
    protected $table = 'production_records';
    protected $fillable = ['mo_id', 'process_step', 'start_time', 'end_time', 'operator_id', 'quantity_produced', 'waste_quantity', 'status', 'remark'];

    protected function getPrimaryKey()
    {
        return 'production_id';
    }

    /**
     * Get production records by MO
     */
    public function getByMO($moId)
    {
        $query = "SELECT pr.*, u.first_name, u.last_name
                  FROM {$this->table} pr
                  LEFT JOIN users u ON pr.operator_id = u.user_id
                  WHERE pr.mo_id = ?
                  ORDER BY pr.start_time ASC";
        
        $this->db->prepare($query);
        $this->db->execute([$moId]);
        return $this->db->fetchAll();
    }

    /**
     * Get production records with details
     */
    public function getAllWithDetails($limit = null, $offset = 0)
    {
        $query = "SELECT pr.*, mo.mo_no, p.product_name, u.first_name, u.last_name
                  FROM {$this->table} pr
                  LEFT JOIN manufacturing_orders mo ON pr.mo_id = mo.mo_id
                  LEFT JOIN products p ON mo.product_id = p.product_id
                  LEFT JOIN users u ON pr.operator_id = u.user_id
                  ORDER BY pr.start_time DESC";
        
        if ($limit) {
            $query .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    /**
     * Get production timeline for MO
     */
    public function getTimeline($moId)
    {
        $query = "SELECT * FROM {$this->table}
                  WHERE mo_id = ?
                  ORDER BY start_time ASC";
        
        $this->db->prepare($query);
        $this->db->execute([$moId]);
        return $this->db->fetchAll();
    }

    /**
     * Complete production record
     */
    public function complete($productionId)
    {
        $query = "UPDATE {$this->table} SET status = 'completed', end_time = NOW() WHERE production_id = ?";
        $this->db->prepare($query);
        return $this->db->execute([$productionId]);
    }
}
