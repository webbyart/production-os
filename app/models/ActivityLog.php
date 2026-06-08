<?php
/**
 * ActivityLog Model
 */

class ActivityLog extends BaseModel
{
    protected $table = 'activity_logs';
    protected $fillable = ['user_id', 'action', 'description', 'related_module', 'related_id'];

    protected function getPrimaryKey()
    {
        return 'activity_id';
    }

    /**
     * Get user activities
     */
    public function getUserActivities($userId, $limit = 50)
    {
        $query = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC LIMIT {$limit}";
        $this->db->prepare($query);
        $this->db->execute([$userId]);
        return $this->db->fetchAll();
    }

    /**
     * Get module activities
     */
    public function getModuleActivities($module, $limit = 50)
    {
        $query = "SELECT al.*, u.first_name, u.last_name
                  FROM {$this->table} al
                  LEFT JOIN users u ON al.user_id = u.user_id
                  WHERE al.related_module = ?
                  ORDER BY al.created_at DESC
                  LIMIT {$limit}";
        
        $this->db->prepare($query);
        $this->db->execute([$module]);
        return $this->db->fetchAll();
    }

    /**
     * Get recent activities
     */
    public function getRecent($limit = 20)
    {
        $query = "SELECT al.*, u.first_name, u.last_name
                  FROM {$this->table} al
                  LEFT JOIN users u ON al.user_id = u.user_id
                  ORDER BY al.created_at DESC
                  LIMIT {$limit}";
        
        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAll();
    }
}
