<?php
/**
 * QualityControl Model
 */

class QualityControl extends BaseModel
{
    protected $table = 'quality_control';
    protected $fillable = ['mo_id', 'inspection_date', 'sample_size', 'defects_found', 'result', 'inspector_id', 'remark', 'approved_by', 'approved_at'];

    protected function getPrimaryKey()
    {
        return 'qc_id';
    }

    /**
     * Get all QC records with details
     */
    public function getAllWithDetails($limit = null, $offset = 0)
    {
        $query = "SELECT qc.*, mo.mo_no, p.product_name, u1.first_name as inspector_name, u2.first_name as approver_name
                  FROM {$this->table} qc
                  LEFT JOIN manufacturing_orders mo ON qc.mo_id = mo.mo_id
                  LEFT JOIN products p ON mo.product_id = p.product_id
                  LEFT JOIN users u1 ON qc.inspector_id = u1.user_id
                  LEFT JOIN users u2 ON qc.approved_by = u2.user_id
                  ORDER BY qc.qc_id DESC";
        
        if ($limit) {
            $query .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    /**
     * Get QC by MO ID
     */
    public function getByMO($moId)
    {
        $query = "SELECT * FROM {$this->table} WHERE mo_id = ? ORDER BY inspection_date DESC";
        $this->db->prepare($query);
        $this->db->execute([$moId]);
        return $this->db->fetchAll();
    }

    /**
     * Get latest QC for MO
     */
    public function getLatestByMO($moId)
    {
        $query = "SELECT * FROM {$this->table} WHERE mo_id = ? ORDER BY inspection_date DESC LIMIT 1";
        $this->db->prepare($query);
        $this->db->execute([$moId]);
        return $this->db->fetch();
    }

    /**
     * Get pending QC
     */
    public function getPending()
    {
        $query = "SELECT * FROM {$this->table} WHERE result = 'hold' ORDER BY inspection_date DESC";
        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    /**
     * Get approved records
     */
    public function getApproved()
    {
        $query = "SELECT * FROM {$this->table} WHERE result = 'pass' AND approved_by IS NOT NULL ORDER BY approved_at DESC";
        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    /**
     * Get failed records
     */
    public function getFailed()
    {
        $query = "SELECT * FROM {$this->table} WHERE result = 'fail' ORDER BY inspection_date DESC";
        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    /**
     * Approve QC
     */
    public function approve($qcId, $approvedBy)
    {
        $query = "UPDATE {$this->table} SET approved_by = ?, approved_at = NOW() WHERE qc_id = ?";
        $this->db->prepare($query);
        return $this->db->execute([$approvedBy, $qcId]);
    }

    /**
     * Get defect rate
     */
    public function getDefectRate()
    {
        $query = "SELECT 
                    COUNT(*) as total_inspections,
                    SUM(CASE WHEN result = 'pass' THEN 1 ELSE 0 END) as passed,
                    SUM(CASE WHEN result = 'fail' THEN 1 ELSE 0 END) as failed,
                    SUM(CASE WHEN result = 'hold' THEN 1 ELSE 0 END) as hold,
                    ROUND((SUM(CASE WHEN result = 'fail' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as defect_percentage
                  FROM {$this->table}
                  WHERE inspection_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        
        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetch();
    }
}
