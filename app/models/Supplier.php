<?php
/**
 * Supplier Model
 */

class Supplier extends BaseModel
{
    protected $table = 'suppliers';
    protected $fillable = ['supplier_code', 'supplier_name', 'contact_person', 'phone', 'email', 'address', 'city', 'state', 'postal_code', 'country', 'status'];

    protected function getPrimaryKey()
    {
        return 'supplier_id';
    }

    /**
     * Get by code
     */
    public function getByCode($code)
    {
        return $this->getBy('supplier_code', $code);
    }

    /**
     * Get active suppliers
     */
    public function getActive()
    {
        $query = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY supplier_name";
        $this->db->prepare($query);
        $this->db->execute();
        return $this->db->fetchAll();
    }

    /**
     * Search suppliers
     */
    public function searchSuppliers($keyword)
    {
        $columns = ['supplier_code', 'supplier_name', 'contact_person', 'email'];
        return $this->search($columns, $keyword);
    }
}
