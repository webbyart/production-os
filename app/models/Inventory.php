<?php
/**
 * Inventory Model
 */

class Inventory extends BaseModel
{
    protected $table = 'inventory';
    protected $fillable = ['product_id', 'rm_id', 'warehouse_location', 'quantity'];

    protected function getPrimaryKey()
    {
        return 'inventory_id';
    }

    /**
     * Get product inventory
     */
    public function getProductInventory($productId)
    {
        $query = "SELECT * FROM {$this->table} WHERE product_id = ?";
        $this->db->prepare($query);
        $this->db->execute([$productId]);
        return $this->db->fetchAll();
    }

    /**
     * Get material inventory
     */
    public function getMaterialInventory($rmId)
    {
        $query = "SELECT * FROM {$this->table} WHERE rm_id = ?";
        $this->db->prepare($query);
        $this->db->execute([$rmId]);
        return $this->db->fetchAll();
    }

    /**
     * Update quantity
     */
    public function updateQuantity($inventoryId, $quantity)
    {
        $query = "UPDATE {$this->table} SET quantity = ? WHERE inventory_id = ?";
        $this->db->prepare($query);
        return $this->db->execute([$quantity, $inventoryId]);
    }

    /**
     * Increase quantity
     */
    public function increaseQuantity($inventoryId, $quantity)
    {
        $query = "UPDATE {$this->table} SET quantity = quantity + ? WHERE inventory_id = ?";
        $this->db->prepare($query);
        return $this->db->execute([$quantity, $inventoryId]);
    }

    /**
     * Decrease quantity
     */
    public function decreaseQuantity($inventoryId, $quantity)
    {
        $query = "UPDATE {$this->table} SET quantity = quantity - ? WHERE inventory_id = ?";
        $this->db->prepare($query);
        return $this->db->execute([$quantity, $inventoryId]);
    }

    /**
     * Get total inventory value
     */
    public function getTotalValue()
    {
        $query = "SELECT SUM(i.quantity * rm.price) as total_value
                  FROM {$this->table} i
                  LEFT JOIN raw_materials rm ON i.rm_id = rm.rm_id
                  WHERE i.rm_id IS NOT NULL";
        
        $this->db->prepare($query);
        $this->db->execute();
        $result = $this->db->fetch();
        return $result['total_value'] ?? 0;
    }
}
