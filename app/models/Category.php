<?php
/**
 * Category Model
 */

class Category extends BaseModel
{
    protected $table = 'categories';
    protected $fillable = ['category_name', 'description'];

    protected function getPrimaryKey()
    {
        return 'category_id';
    }

    /**
     * Get all categories
     */
    public function getAll($limit = null, $offset = 0)
    {
        $query = "SELECT * FROM {$this->table} ORDER BY category_name ASC";
        
        if ($limit) {
            $query .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    /**
     * Get category with product count
     */
    public function getCategoryWithCount()
    {
        $query = "SELECT c.*, COUNT(p.product_id) as product_count
                  FROM {$this->table} c
                  LEFT JOIN products p ON c.category_id = p.category_id
                  GROUP BY c.category_id
                  ORDER BY c.category_name";
        
        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAll();
    }
}
